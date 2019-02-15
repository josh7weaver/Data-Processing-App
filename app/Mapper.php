<?php namespace DataStaging;

use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Division;
use DataStaging\Models\Enrollment;
use DataStaging\Models\ProcessLock;
use DataStaging\Models\ProcessLog;
use DataStaging\Models\School;
use DataStaging\Models\Section;
use DataStaging\Models\TbbSchool;
use DataStaging\Models\ValidationData;
use DataStaging\Models\ViewAllEnrollment;
use DataStaging\Models\ViewTbbEnrollment;

class Mapper{
	public static function NAME_TO_IO_MODEL_MAP()
	{
		return [
			'customer'   => Customer::class,
			'course'     => Course::class,
			'section'    => Section::class,
			'enrollment' => Enrollment::class,
		];
	}

	public static function NAME_TO_MODEL_MAP()
	{
		return array_merge(
			static::NAME_TO_IO_MODEL_MAP(),
			[
				'school'            => School::class,
				'division'          => Division::class,
				'viewtbbenrollment' => ViewTbbEnrollment::class,
                'viewallenrollment' => ViewAllEnrollment::class,
				'tbbschool'			=> TbbSchool::class,
				'processlock'		=> ProcessLock::class,
				'processlog'		=> ProcessLog::class,
                'validationdata'    => ValidationData::class,
			]
		);
	}

	public static function FILE_TYPE_TO_MODEL_MAP()
	{
		return [
			'csv' => CsvFile::class,
			'rafter_csv' => CsvFileRafterFormat::class,
			'three_file_csv' => CsvFileThreeFileFormat::class,
		];
	}

	public static function PREFERENCE_TO_COMMENT_MAP()
	{
		return [
			'rent'     => 'Prefers Rental',
			'buy new'  => 'Prefers New',
			'buy used' => 'Prefers Used',
			'opt out'  => 'Opted Out'
		];
	}

    public static function VALID_FILE_NAMES()
    {
        return [
            'course',
            'section',
            'enroll',
            'customer'
        ];
	}
}
