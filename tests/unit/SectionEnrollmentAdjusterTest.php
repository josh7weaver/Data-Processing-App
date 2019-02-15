<?php

use DataStaging\Models\Division;
use DataStaging\Models\Section;

class SectionEnrollmentAdjusterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \DataStaging\Adjusters\SectionEnrollmentAdjuster
     */
    protected $adjuster;

    /**
     * @var \DataStaging\Models\School
     */
    protected $school;

    protected function _before()
    {
        DB::beginTransaction();

        $this->school = \DataStaging\Models\School::where('code', 'TU')->firstOrFail();
        $this->adjuster = new \DataStaging\Adjusters\SectionEnrollmentAdjuster($this->school);
    }

    protected function _after()
    {
        DB::rollBack();
    }

    /**
     * @dataProvider provideTestPickLarger
     * @param $newActEnrollment
     * @param $butlerEnrollment
     * @param $expectedEnrollment
     */
    public function testPickLarger($newActEnrollment, $butlerEnrollment, $expectedEnrollment)
    {
        $actual = $this->adjuster->pickLarger($newActEnrollment, $butlerEnrollment);
        $this->assertEquals($expectedEnrollment, $actual);
    }

    public function provideTestPickLarger()
    {
        return [
            [12, 10, 12],
            [20, 20, 20],
            [0, 5, 5]
        ];
    }

    /**
     * @dataProvider provideTestRecalculateEnrollment
     *
     * @param $enrollment
     * @param $adjustmentPercentage
     * @param $expectedEnrollment
     */
    public function testRecalculateEnrollment($enrollment, $adjustmentPercentage, $expectedEnrollment)
    {
        $actual = $this->adjuster->recalculateEnrollment($enrollment, $adjustmentPercentage);
        $this->assertEquals($expectedEnrollment, $actual);
    }

    public function provideTestRecalculateEnrollment()
    {
        return [
            [14, .8, 12],
            [23, .2, 5],
            [45, .6, 27]
        ];
    }

    /**
     * @dataProvider provideTestSave
     *
     * @param                             $newEnrollment
     */
    public function testSave($newEnrollment)
    {
        $section = $this->school->sections()->firstOrFail();

        $result = $this->adjuster->save($section, $newEnrollment);

        $this->assertTrue($result);
    }

    public function provideTestSave()
    {
        return [
            [12],
            [43],
            [0]
        ];
    }

    public function testSaveForNullEnrollmentThrowsException()
    {
        $this->setExpectedException('ErrorException');

        $section = $this->school->sections()->firstOrFail();

        $this->adjuster->save($section, null);
    }

    public function testAdjustSectionEnrollmentFor()
    {
        $query = DB::table('sections')->join('divisions', 'sections.campus', '=', 'divisions.name')
                   ->join('count_tbb_enrollments as tbb', 'tbb.section_id', '=', 'sections.id')
                   ->select([
                       'sections.school_id AS school_id',
                       'divisions.id AS division_id',
                       'sections.id AS section_id',
                       'divisions.enrollment_adjustment_enabled AS percent_adjustment_enabled',
                       'divisions.enrollment_percentage AS enrollment_percentage',
                       'sections.act_enrollment AS act_enrollment',
                       DB::raw('CEIL(divisions.enrollment_percentage * sections.act_enrollment) AS recalculated_enrollment'),
                       'tbb.counted_enrollment AS tbb_enrollment',
                   ])
                   ->where('divisions.use_butler', true)
                   ->where('sections.enabled', true)
                   ->where('divisions.enabled', true);

        $sections = collect($query->get());

        $recalculatedIsLarger = $sections->filter(function($section)
        {
            return $section->recalculated_enrollment > $section->tbb_enrollment
            && $section->percent_adjustment_enabled == true
            && $section->enrollment_percentage != '1.0';
        })->first();

        $tbbIsLarger = $sections->filter(function($section)
        {
            return $section->tbb_enrollment > $section->recalculated_enrollment
            && $section->percent_adjustment_enabled == true
            && $section->enrollment_percentage != '1.0';
        })->first();


        // this really should also have an addtional filter for
        //  && $section->act_enrollment != $section->tbb_enrollment;
        //  HOWEVER, doesn't currently because there were no results where they weren't equal.
        $noEnrollmentPercentage = $sections->filter(function($section)
        {
            return $section->percent_adjustment_enabled == false;
        })->first();

        $data = [
            [
                'section' => $recalculatedIsLarger,
                'expectedEnrollment' => $recalculatedIsLarger->recalculated_enrollment,
                'prediction' => "This section's act_enrollment should equal the % of previous act_enrollment because its larger than the butler enrollment"
            ],
            [
                'section' => $tbbIsLarger,
                'expectedEnrollment' => $tbbIsLarger->tbb_enrollment,
                'prediction' => "This section's act_enrollment should equal the butler enrollment because its larger than the % of previous act_enrollment"
            ],
            [
                'section' => $noEnrollmentPercentage,
                'expectedEnrollment' => $noEnrollmentPercentage->tbb_enrollment,
                'prediction' => "This section's act_enrollment should equal the butler enrollment because there is no % to adjust act_enrollment by."
            ]
        ];

        foreach($data as $item){
            $this->runAdjustSectionEnrollmentFor($item['section'], $item['expectedEnrollment'], $item['prediction']);
        }
    }

    /**
     * @param $section
     * @param $expectedEnrollment
     * @param $prediction
     */
    private function runAdjustSectionEnrollmentFor($section, $expectedEnrollment, $prediction)
    {
        $this->adjuster->adjustSectionEnrollmentFor(Division::findOrFail($section->division_id));

        $updatedSection = Section::findOrFail($section->section_id);

//        dump($section, $updatedSection->toArray(), $prediction);
        $this->tester->expect($prediction);
        $this->assertEquals($expectedEnrollment, $updatedSection->act_enrollment);
    }
}