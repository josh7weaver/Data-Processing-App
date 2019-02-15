<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\School;

class rocketAdjuster implements Adjuster
{
    /**
     * @var School
     */
    private $school;

    /**
     * TermCourseAdjuster constructor.
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->school = $school;
    }

    /**
     * Perform the necessary adjustments for the given adjuster
     * @return mixed
     * @throws \Exception
     */
    public function adjust()
    {
        if($this->school->getCode() != 'rocket'){
            return false; //no-op
        }

        $courseFile = $this->school->getFullImportPath().'/rocket_courses.csv';
        $enrollmentFile = $this->school->getFullImportPath().'/rocket_enrollments.csv';
        $studentFile = $this->school->getFullImportPath().'/rocket_students.csv';

        $courseFileOut = $this->school->getFullImportPath().'/rocket_courses_processed.csv';
        $enrollmentFileOut = $this->school->getFullImportPath().'/rocket_enrollments_processed.csv';
        $studentFileOut = $this->school->getFullImportPath().'/rocket_students_processed.csv';

        /**
         * TRUNCATE TEMP TABLES
         */
        \DB::affectingStatement("TRUNCATE rocket_courses");
        \DB::affectingStatement("TRUNCATE rocket_enrollments");
        \DB::affectingStatement("TRUNCATE rocket_students");

        /**
         * IMPORT TO TEMP TABLES
         */
        shell_exec("psql -U data_staging -d data_staging -c \"\\copy rocket_courses FROM '$courseFile' WITH DELIMITER ',' CSV HEADER\"");
        shell_exec("psql -U data_staging -d data_staging -c \"\\copy rocket_enrollments FROM '$enrollmentFile' WITH DELIMITER ',' CSV HEADER\"");
        shell_exec("psql -U data_staging -d data_staging -c \"\\copy rocket_students FROM '$studentFile' WITH DELIMITER ',' CSV HEADER\"");

        /**
         * DO ADJUSTMENTS
         */

        // update course code
        $coursesCourseUpdated = \DB::affectingStatement("update rocket_courses set course = department || ' ' || course");
        $enrollmentsCourseUpdated = \DB::affectingStatement("update rocket_enrollments set course_id = dept_id || ' ' || course_id");

        // update campus name
        $coursesCampusUpdated = \DB::affectingStatement("update rocket_courses set campus = 'CENTRAL - ' || TRIM(campus)");
        $enrollmentsCampusUpdated = \DB::affectingStatement("update rocket_enrollments set campus_id = 'CENTRAL - ' || TRIM(campus_id)");


        //update term code
        // create temp table
        \DB::affectingStatement("DROP TABLE IF EXISTS cck_temp;");
        \DB::affectingStatement("
            SELECT
                to_char(to_timestamp(c.section_start_date, 'MM/DD/YYYY'), 'YYYY') -- start year
                || to_char(to_timestamp(c.section_start_date, 'MM/DD/YYYY'), 'MON') -- start month
                                            AS new_term,
                campus,
                term,
                department,
                course,
                section
            INTO TABLE cck_temp
            FROM
                rocket_courses c
            WHERE
                c.campus = 'CENTRAL - SPE'
            ");

        // update course term
        $coursesTermUpdated = \DB::affectingStatement("
            UPDATE rocket_courses c
            SET
              term = cck.new_term
            FROM cck_temp AS cck
            WHERE
                cck.campus = c.campus
                AND cck.term = c.term
                AND cck.department = c.department
                AND cck.course = c.course
                AND cck.section = c.section
                AND c.campus = 'CENTRAL - SPE'
            ");

        // update enrollments term
        $enrollmentsTermUpdated = \DB::affectingStatement("
            UPDATE rocket_enrollments e
            SET
              term_id = cck.new_term
            FROM cck_temp AS cck
            WHERE
                cck.campus = e.campus_id
                AND cck.term = e.term_id
                AND cck.department = e.dept_id
                AND cck.course = e.course_id
                AND cck.section = e.section_id
                AND e.campus_id = 'CENTRAL - SPE'
            ");

        //
        // FACULTY
        //

        // step 1 - convert double space to single space in professors full name in course file so it will match whats
        // in student file after we move them in step 2
        $facultyWithDoubleSpace = \DB::affectingStatement("
            update rocket_courses set professor = regexp_replace(professor, '\s+', ' ');
        ");

        // step 2 - extract faculty from the course file into the student file for later inclusion in customer file
        $facultyAdded = \DB::affectingStatement("
            insert into rocket_students (student_id, first_name, last_name, primary_email, year_in_school)
            select
                    t.id as student_id,
                    split_part(t.full_name, ' ', 1) as first_name,
                    split_part(t.full_name, ' ', 2) as last_name,
                    t.email as primary_email,
                    'faculty' as year_in_school
            from 
            (
                    SELECT DISTINCT
                        c.professor    AS full_name,
                        c.professor_id AS email,
                        c.faculty_id   AS id
                    FROM
                        rocket_courses c
                    WHERE
                        (c.professor        IS NOT NULL
                            AND c.professor          != '')
                        AND (c.professor_id IS NOT NULL
                            AND c.professor_id       != '')
                        AND (c.faculty_id   IS NOT NULL
                            AND c.faculty_id         != '')
            ) t
        ");

        /**
         * EXPORT TO NEW FILE
         */
        shell_exec("psql -U data_staging -d data_staging -c \"\\copy rocket_courses TO '$courseFileOut' WITH DELIMITER ',' CSV HEADER\"");
        shell_exec("psql -U data_staging -d data_staging -c \"\\copy rocket_enrollments TO '$enrollmentFileOut' WITH DELIMITER ',' CSV HEADER\"");
        shell_exec("psql -U data_staging -d data_staging -c \"\\copy rocket_students TO '$studentFileOut' WITH DELIMITER ',' CSV HEADER\"");

        /**
         * Move original files
         */
        \File::move($courseFile, $this->school->getFullImportPath().'/old/rocket_courses.csv');
        \File::move($enrollmentFile, $this->school->getFullImportPath().'/old/rocket_enrollments.csv');
        \File::move($studentFile, $this->school->getFullImportPath().'/old/rocket_students.csv');


        \Log::info("------------\n\nADJUSTED rocket term and course codes\n\n");
        \Log::info("Courses Updated with campus string: $coursesCampusUpdated\n");
        \Log::info("Enrollments Updated with campus string: $enrollmentsCampusUpdated\n");
        \Log::info("Courses Updated for SPE with new Terms: $coursesTermUpdated\n");
        \Log::info("Courses Updated with course code: $coursesCourseUpdated\n");
        \Log::info("Enrollments Updated for SPE with new Terms: $enrollmentsTermUpdated\n");
        \Log::info("Enrollments Updated with course code: $enrollmentsCourseUpdated\n");
        \Log::info("Converted double to single spaces in course file: $facultyWithDoubleSpace\n");
        \Log::info("Copied faculty from the course file into the students file: $facultyAdded\n\n");
    }
}