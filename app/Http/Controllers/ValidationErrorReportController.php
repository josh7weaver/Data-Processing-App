<?php namespace DataStaging\Http\Controllers;

use Carbon\Carbon;
use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Enrollment;
use DataStaging\Models\ProcessLog;
use DataStaging\Models\School;
use DataStaging\Models\Section;
use DataStaging\SchoolErrorDto;
use DataStaging\Util;
use DataStaging\ValidationErrorPresenter;
use DataStaging\ValidationErrorRepository;
use Illuminate\Support\Collection;

class ValidationErrorReportController extends Controller {

    /**
     * @var School
     */
    private $school;
    /**
     * @var ProcessLog
     */
    private $processLog;

    /**
     * ValidationErrorReportController constructor.
     * @param School     $school
     * @param ProcessLog $processLog
     */
    public function __construct(School $school, ProcessLog $processLog)
    {
        $this->school = $school;
        $this->processLog = $processLog;
        $this->validationErrorRepository = ValidationErrorRepository::class;
        $this->schoolDropdownOptions = $this->school->buildDropdownOptions('Show All');
    }

    /**
     * Display a listing of the resource.
     *
     * @param                $processToken
     * @param SchoolErrorDto $schoolErrorDto
     * @return Response
     * @internal param Request $request
     */
	public function index($processToken, SchoolErrorDto $schoolErrorDto)
	{
        $errors = $this->school->all()
            ->map(function($school) use($processToken, $schoolErrorDto)
            {
                $repo = new $this->validationErrorRepository($processToken, $school);

                return $schoolErrorDto->newInstance()
                        ->setSchoolName($school->getName())
                        ->setGeneralErrors(
                            $this->processLog->nonValidationErrors($processToken, $school->getCode())->get()
                        )
                        ->addValidationError('Customer', $this->getPresenters( $repo->getCustomerErrors()->groupBy('validation_code'), Customer::getBaseName()))
                        ->addValidationError('Course', $this->getPresenters($repo->getCourseErrors()->groupBy('validation_code'), Course::getBaseName()))
                        ->addValidationError('Section', $this->getPresenters($repo->getSectionErrors()->groupBy('validation_code'), Section::getBaseName()))
                        ->addValidationError('Enrollment', $this->getPresenters($repo->getEnrollmentErrors()->groupBy('validation_code'), Enrollment::getBaseName()));
            })
            ->filter(function($possibleErrors) {
                return $possibleErrors->hasErrors(); // if $possibleErrors is false, that school won't be included
            });

        return view('reports.show', [
            'schoolsErrorList' => $errors,
            'schoolDropdown' => $this->schoolDropdownOptions,
            'schoolId' => 0, // used for setting dropdown
            'processToken' => $processToken,
        ]);
	}

    /**
     * Display errors for school
     *
     * @param                $processToken
     * @param                $schoolCode
     * @param SchoolErrorDto $schoolErrorDto
     * @return Response
     */
	public function show($processToken, $schoolCode, SchoolErrorDto $schoolErrorDto)
	{
        $school = $this->school->where('code', $schoolCode)->firstOrFail();
        $repo = new $this->validationErrorRepository($processToken, $school);

        $schoolErrorDto = $schoolErrorDto->newInstance()
            ->setSchoolName($school->getName())
            ->setGeneralErrors(
                $this->processLog->nonValidationErrors($processToken, $schoolCode)->get()
            )
            ->addValidationError('Customer', $this->getPresenters( $repo->getCustomerErrors()->groupBy('validation_code'), Customer::getBaseName()))
            ->addValidationError('Course', $this->getPresenters($repo->getCourseErrors()->groupBy('validation_code'), Course::getBaseName()))
            ->addValidationError('Section', $this->getPresenters($repo->getSectionErrors()->groupBy('validation_code'), Section::getBaseName()))
            ->addValidationError('Enrollment', $this->getPresenters($repo->getEnrollmentErrors()->groupBy('validation_code'), Enrollment::getBaseName()));

        return view('reports.show', [
            'schoolsErrorList' => collect([$schoolErrorDto]),
            'schoolDropdown' => $this->schoolDropdownOptions,
            'schoolId' => $school->getKey(),
            'processToken' => $processToken,
        ]);
	}

    /**
     * @param Collection $validationErrors
     * @param            $fileType
     * @return Collection of ValidationErrorPresenter objects
     */
    protected function getPresenters(Collection $validationErrors, $fileType)
    {
        return $validationErrors->map(function($items, $code) use($fileType)
        {
            return new ValidationErrorPresenter($code, $items, $fileType);
        });
    }

    public function lookupSchoolById($processToken, $schoolId)
    {
        if((int) $schoolId === 0){
            return redirect()->route('reports.index', [$processToken]);
        }

        $school = $this->school->findOrFail($schoolId);

        return redirect()->route('reports.show', [$processToken, $school->getCode()]);
    }

    public function latest()
    {
        $lastSuccessfulRunToken = Util::createProcessToken(new Carbon('-1 hour'));
        return redirect()->route('reports.index', [$lastSuccessfulRunToken]);
    }
}
