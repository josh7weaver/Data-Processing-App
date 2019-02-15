<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidationErrorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('
			CREATE VIEW
				validation_errors
				(
					id,
					process_token,
					school_code,
					file_type,
					validation_code,
					validation_type,
					validation_summary,
					level,
					level_code,
					MESSAGE,
					context,
					channel,
					TIMESTAMP
				) AS
			SELECT
				log.id,
				log.process_token,
				log.school_code,
				log.file_type,
				log.validation_code,
				val.type AS validation_type,
				val.summary as validation_summary,
				log.level,
				log.level_code,
				log.message,
				log.context,
				log.channel,
				log."timestamp"
			FROM
				(process_log log
			JOIN
				validation_data val ON ((log.validation_code = val.code)));
		');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("
			DROP VIEW validation_errors;
		");
	}

}
