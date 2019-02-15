<?php

use Illuminate\Database\Seeder;
use DataStaging\Models\School;

class SchoolsTableSeeder extends Seeder
{
	// auto triggered
	public function run()
	{

			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (1, 'Anderson', 'AU', 'anderson/data_files', 'anderson', 'course', 'section', 'enroll', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:52')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (2, 'Andrews', 'ANDR', 'Andrews/data_files', 'andrews', 'course', 'section', 'enrollment', 'customer', 'csv', false, '2015-05-14 10:52:09', '2015-05-15 10:56:53')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (3, 'Asbury Seminary', 'ABS', 'asburyseminary/data_files', 'asburyseminary', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-21 09:59:24')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (4, 'Bluffton', 'BU', 'bluffton/data_files', 'bluffton', 'course', 'section', 'enroll', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-10-22 11:56:06')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (5, 'Cairn', 'CARN', 'cairn/data_files', 'cairn', 'course', 'section', 'enrollment', 'customer', 'csv', false, '2015-05-14 10:52:09', '2015-08-28 16:04:21')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (6, 'Cornerstone', 'CSU', 'cornerstone/data_files', 'cornerstone', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:53')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (7, 'Grace Bible', 'GBC', 'gracebible/data_files', 'gracebible', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:53')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (8, 'Grace College', 'GC', 'gracecollege/data_files', 'gracecollege', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:53')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (9, 'Greenville College', 'GRC', 'greenville/data_files', 'greenville', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-06-08 21:56:36')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (10, 'Huntington', 'HU', 'huntington/data_files', 'huntington', 'course', 'section', 'enrollment', 'customer', 'csv', false, '2015-05-14 10:52:09', '2015-05-15 10:56:54')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (11, 'Indiana Wesleyan', 'IWU', 'indwes/data_files', 'indwes', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-07-07 14:25:29')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (12, 'LeTourneau University', 'LETU', 'letu/data_files', 'letu', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-06-02 12:06:08')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (13, 'Malone', 'MU', 'malone/data_files', 'malone', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:54')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (14, 'Mt. Vernon', 'MVNU', 'mtvernon/data_files', 'mtvernon', 'course', 'section', 'enrollment', 'customer', 'csv', false, '2015-05-14 10:52:09', '2015-05-15 10:56:54')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (15, 'Northwest', 'NTHW', 'northwest/data_files', 'northwest', 'course', 'section', 'enrollment', 'customer', 'csv', false, '2015-05-14 10:52:09', '2015-05-15 10:56:54')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (16, 'Ohio Christian', 'OCU', 'ohiochristian/data_files', 'ohiochristian', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:54')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (17, 'Oklahoma Baptist', 'OKBU', 'oklahomabaptist/data_files', 'oklahomabaptist', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:54')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (18, 'Spring Arbor', 'SAU', 'springarbor/data_files', 'springarbor', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:55')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (19, 'Taylor', 'TU', 'taylor/data_files', 'taylor', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-15 10:56:55')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (20, 'Trevecca Naz', 'TNU', 'trevecca/data_files', 'trevecca', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-07-01 14:29:52')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (21, 'Warner Pacific', 'WPC', 'warnerpacific/data_files', 'warnerpacific', 'course', 'section', 'enroll', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-05-20 11:51:34')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (22, 'Indiana Wesleyan - Non Residential', 'IWUNR', 'indwes-nr/data_files', 'indwes-nr', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-05-14 10:52:09', '2015-07-07 14:25:30')");
			DB::insert("INSERT INTO schools (id, name, code, path, export_path, course_pattern, section_pattern, enrollment_pattern, customer_pattern, file_type, enabled, created_at, updated_at) VALUES (23, 'Oklahoma City University', 'OKCU', 'oklahomacity/data_files', 'oklahomacity', 'course', 'section', 'enrollment', 'customer', 'csv', true, '2015-06-11 19:10:42', '2015-06-11 23:22:39')");

//		School::create([
//			'name' => 'Anderson',
//            'code' => 'AU',
//			'path' => 'anderson/data_files',
//            'export_path' => 'anderson',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enroll',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Andrews',
//            'code' => 'ANDR',
//			'path' => 'Andrews/data_files',
//            'export_path' => 'andrews',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => false
//		]);
//
//		School::create([
//			'name' => 'Asbury Seminary',
//            'code' => 'ABS',
//			'path' => 'asburyseminary',
//            'export_path' => 'asburyseminary',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Bluffton',
//            'code' => 'BU',
//			'path' => 'bluffton/data_files',
//            'export_path' => 'bluffton',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Cairn',
//            'code' => 'CARN',
//			'path' => 'cairn/data_files',
//            'export_path' => 'cairn',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Cornerstone',
//            'code' => 'CSU',
//			'path' => 'cornerstone/data_files',
//            'export_path' => 'cornerstone',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Grace Bible',
//            'code' => 'GBC',
//			'path' => 'gracebible/data_files',
//            'export_path' => 'gracebible',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Grace College',
//            'code' => 'GC',
//			'path' => 'gracecollege/data_files',
//            'export_path' => 'gracecollege',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Greenville College',
//            'code' => 'GRC',
//			'path' => 'greenville/data_files',
//            'export_path' => 'greenville',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Huntington',
//            'code' => 'HU',
//			'path' => 'huntington/data_files',
//            'export_path' => 'huntington',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => false
//		]);
//
//		School::create([
//			'name' => 'Indiana Wesleyan',
//            'code' => 'IWU',
//			'path' => 'indwes/data_files',
//            'export_path' => 'indwes',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'LeTourneau University',
//            'code' => 'LETU',
//			'path' => 'letu',
//            'export_path' => 'letu',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Malone',
//            'code' => 'MU',
//			'path' => 'malone/data_files',
//            'export_path' => 'malone',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Mt. Vernon',
//            'code' => 'MVNU',
//			'path' => 'mtvernon/data_files',
//            'export_path' => 'mtvernon',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => false
//		]);
//
//		School::create([
//			'name' => 'Northwest',
//            'code' => 'NTHW',
//			'path' => 'northwest/data_files',
//            'export_path' => 'northwest',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => false
//		]);
//
//		School::create([
//			'name' => 'Ohio Christian',
//            'code' => 'OCU',
//			'path' => 'ohiochristian/data_files',
//            'export_path' => 'ohiochristian',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Oklahoma Baptist',
//            'code' => 'OKBU',
//			'path' => 'oklahomabaptist/data_files',
//            'export_path' => 'oklahomabaptist',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Spring Arbor',
//            'code' => 'SAU',
//			'path' => 'springarbor/data_files',
//            'export_path' => 'springarbor',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Taylor',
//            'code' => 'TU',
//			'path' => 'taylor/data_files',
//            'export_path' => 'taylor',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Trevecca Naz',
//            'code' => 'TRVN',
//			'path' => 'trevecca',
//            'export_path' => 'trevecca',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		School::create([
//			'name' => 'Warner Pacific',
//            'code' => 'WPC',
//			'path' => 'warnerpacific/data_files',
//            'export_path' => 'warnerpacific',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enroll',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
//
//		// this is a special case
//		School::create([
//			'name' => 'Indiana Wesleyan - Non Residential',
//            'code' => 'IWUNR',
//			'path' => 'indwes-nr/data_files',
//            'export_path' => 'indwes-nr',
//            'course_pattern' => 'course',
//            'course_pattern' => 'course',
//			'section_pattern' => 'section',
//			'enrollment_pattern' => 'enrollment',
//			'customer_pattern' => 'customer',
//			'file_type' => 'csv',
//			'enabled' => true
//		]);
	}
}