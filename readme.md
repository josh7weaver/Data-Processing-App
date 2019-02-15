Link to the readme: http://confluence.treeoflifebooks.com:8090/display/ST/Data+Staging+2.0


    CREATE VIEW iwunr_sections AS
    SELECT
        sections.id,
        sections.school_id,
        CASE WHEN ((sections.term ~ '2015.*'::text) OR (sections.term ~ '2016[A-F].*'::text)) THEN ('(Old) '::text || sections.campus) ELSE sections.campus END AS campus,
        CASE WHEN ((sections.term ~ '2015.*'::text) OR (sections.term ~ '2016[A-F].*'::text)) THEN ('(Old) '::text || sections.term)   ELSE sections.term   END AS term,
        sections.department,
        sections.course,
        sections.section,
        sections.instructor,
        sections.est_enrollment,
        sections.act_enrollment,
        sections.comment,
        sections.b_delete,
        sections.enabled,
        sections.created_at,
        sections.updated_at
    FROM
        sections
    WHERE
        ((sections.school_id      = 22)
            AND (sections.enabled = true));
            "


    CREATE VIEW iwunr_courses AS
    SELECT
        courses.id,
        courses.school_id,
        CASE WHEN ((courses.term ~ '2015.*'::text) OR (courses.term ~ '2016[A-F].*'::text)) THEN ('(Old) '::text || courses.campus) ELSE courses.campus END AS campus,
        CASE WHEN ((courses.term ~ '2015.*'::text) OR (courses.term ~ '2016[A-F].*'::text)) THEN ('(Old) '::text || courses.term)   ELSE courses.term   END AS term,
        courses.department,
        courses.course,
        courses.description,
        courses.comment,
        courses.b_delete,
        courses.enabled,
        courses.created_at,
        courses.updated_at
    FROM
        courses
    WHERE
        ((courses.school_id      = 22)
            AND (courses.enabled = true));


    CREATE VIEW iwunr_enrollments AS
    SELECT
        enrollments.id,
        enrollments.school_id,
        enrollments.student_id,
        CASE WHEN ((enrollments.term ~ '2015.*'::text) OR (enrollments.term ~ '2016[A-F].*'::text)) THEN ('(Old) '::text || enrollments.campus) ELSE enrollments.campus END AS campus,
        CASE WHEN ((enrollments.term ~ '2015.*'::text) OR (enrollments.term ~ '2016[A-F].*'::text)) THEN ('(Old) '::text || enrollments.term)   ELSE enrollments.term   END AS term,
        enrollments.department,
        enrollments.course,
        enrollments.section,
        enrollments.comment,
        enrollments.b_delete,
        enrollments.enabled,
        enrollments.created_at,
        enrollments.updated_at
    FROM
        enrollments
    WHERE
        ((enrollments.school_id      = 22)
            AND (enrollments.enabled = true));