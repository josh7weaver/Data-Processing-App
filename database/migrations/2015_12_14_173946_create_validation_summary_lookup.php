<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidationSummaryLookup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('validation_data', function(Blueprint $table)
        {
            $table->text('code')->unique()->index();
            $table->text('type');
            $table->text('summary');
		});

//        INSERT INTO validation_data (code, type, summary) VALUES ('NO_DATES', 'row', 'Can''t find valid start/end date(s).');
//        INSERT INTO validation_data (code, type, summary) VALUES ('NO_SECTION', 'row', 'Can''t find corresponding section.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('NO_CUSTOMER', 'row', 'Can''t find corresponding customer.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('NO_COURSE', 'row', 'Can''t find corresponding course.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('NO_INSTRUCTOR', 'row', 'Can''t find corresponding instructor.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('DIV_NAME', 'row', 'Invalid division name(s) present.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('R_COL_COUNT', 'row', 'Column Count Mismatch (within file).');
//        INSERT INTO validation_data (code, type, summary) VALUES ('BAD_DATES', 'row', 'The end date is before the start date.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('ADDRESS', 'row', 'An Address Field is Missing.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('ENCODING', 'file', 'The file is encoded incorrectly.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('F_COL_COUNT', 'file', 'Column Count Mismatch.');
//        INSERT INTO validation_data (code, type, summary) VALUES ('FILE_OLD', 'file', 'The file is outdated.');

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('validation_data');
	}

}
