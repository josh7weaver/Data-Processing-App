<?php namespace DataStaging\Console\Commands;

use Carbon\Carbon;
use DataStaging\FileValidator;
use DataStaging\Models\ProcessLog;
use DataStaging\Models\School;
use DataStaging\Models\ValidationErrorView;
use DataStaging\Util;
use DataStaging\ValidationErrorRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;

class SendFileErrorReportCommand extends Command {

	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'report:file';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Send the email for data FILE validation errors';

    /**
     * @var int
     */
    protected $processToken;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $generalProcessErrors;

    /**
	 * @var School
	 */
	private $school;

    /**
     * @var ValidationErrorView
     */
    private $validationErrorView;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var ProcessLog
     */
    private $processLog;

    /**
     * Create a new command instance.
     * @param School              $school
     * @param ValidationErrorView $validationErrorView
     * @param ProcessLog          $processLog
     * @param Mailer              $mailer
     */
	public function __construct(
        School $school,
        ValidationErrorView $validationErrorView,
        ProcessLog $processLog,
        Mailer $mailer
    ){
		$this->school              = $school;
        $this->validationErrorView = $validationErrorView;
        $this->processLog          = $processLog;
        $this->processToken        = Util::createProcessToken(new Carbon('-1 hour'));
        $this->mailer              = $mailer;

        $this->generalProcessErrors = $this->processLog->nonValidationErrors($this->processToken)->get();

		parent::__construct();
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        if($this->hasNoErrors())
        {
            $this->sendEmail('emails.fileValidationNoErrors');
            $this->comment("Sent File Validation Error Email. No errors found for token: " . $this->processToken);
            return;
        }

        $errors = collect();
        $schools = $this->validationErrorView->getSchoolsWithErrors($this->processToken, FileValidator::VALIDATION_ID);

        foreach($schools as $school)
        {
            $report = new ValidationErrorRepository($this->processToken, $school);
            $fileErrorsForSchool = $report->getAllErrors(FileValidator::VALIDATION_ID);
            $errors = $errors->merge($fileErrorsForSchool);
        }

        $this->sendEmail('emails.fileValidationErrors', [
            'fileErrors' => $errors,
            'generalErrors' => $this->generalProcessErrors,
            'processToken' => $this->processToken
        ]);

        $this->comment("Sent the File Validation Email with ".$errors->count()." error(s) and ".$this->generalProcessErrors->count()." general error(s) for token: " . $this->processToken);
	}

    protected function sendEmail($view, $data = [])
    {
        $this->mailer->send($view, $data, function($message)
        {
            $message->to(getenv('VALIDATION_EMAIL_GROUP'))
                    ->subject("Daily Data File Validation");
        });
    }

    /**
     * @return bool
     */
    protected function hasNoErrors()
    {
        $viewFileErrors = $this->validationErrorView->countAllErrors($this->processToken, FileValidator::VALIDATION_ID);
        $generalErrors = $this->generalProcessErrors->count();

        return $viewFileErrors == 0 && $generalErrors == 0;
    }
}
