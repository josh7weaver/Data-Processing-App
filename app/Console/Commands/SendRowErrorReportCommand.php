<?php namespace DataStaging\Console\Commands;

use Carbon\Carbon;
use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Enrollment;
use DataStaging\Models\ProcessLog;
use DataStaging\Models\School;
use DataStaging\Models\Section;
use DataStaging\Util;
use DataStaging\ValidationErrorRepository;
use DataStaging\RowValidator;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;
use Symfony\Component\Console\Input\InputArgument;

class SendRowErrorReportCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'report:row';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send the email for data ROW validation errors';

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create a new command instance.
     * @param School     $school
     * @param Mailer     $mailer
     */
	public function __construct(
        School $school,
        Mailer $mailer
    ){
		$this->school = $school;
		$this->processToken = Util::createProcessToken(new Carbon('-1 hour'));
        $this->mailer = $mailer;

		parent::__construct();
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $school = $this->school->where('code', $this->argument('school_code'))->firstOrFail();
        $repo = new ValidationErrorRepository($this->processToken, $school);

		if($repo->countErrors(RowValidator::VALIDATION_ID) == 0){
			$this->comment("No Row validation errors found, quitting...");
			return;
		}

        $data = [
            'rowErrors' => [
					Customer::getBasename() => $repo->getCodesAndCounts(RowValidator::VALIDATION_ID, Customer::getBaseName()),
					Course::getBaseName() => $repo->getCodesAndCounts(RowValidator::VALIDATION_ID, Course::getBaseName()),
					Section::getBaseName() => $repo->getCodesAndCounts(RowValidator::VALIDATION_ID, Section::getBaseName()),
					Enrollment::getBaseName() => $repo->getCodesAndCounts(RowValidator::VALIDATION_ID, Enrollment::getBaseName()),
            ],
            'schoolName' => $school->getName(),
            'schoolCode' => $school->getCode(),
            'processToken' => $this->processToken,
        ];

        $this->mailer->send('emails.rowValidationErrors', $data, function($message) use($school)
        {
            $message->to(getenv('VALIDATION_EMAIL_GROUP'))
                    ->subject("Data Content Validation Snapshot for " . $school->getName() . ' ' . Carbon::now()->toDateString());
        });

        $this->comment("Sent the Row Validation Email with " . $repo->countErrors(RowValidator::VALIDATION_ID) . " error(s) for token: ". $this->processToken . " and school code: ". $school->getCode());
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['school_code', InputArgument::REQUIRED, 'School code corresponding to schools.code in db'],
		];
	}
}
