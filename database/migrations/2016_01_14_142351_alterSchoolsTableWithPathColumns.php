<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSchoolsTableWithPathColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schools', function($table)
		{
			$table->renameColumn('path', 'import_path');
			$table->text('backup_path')->default('');
			$table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
			$table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schools', function($table)
		{
			$table->renameColumn('import_path', 'path');
			$table->dropColumn('backup_path');
			$table->dateTime('created_at')->nullable()->change();
			$table->dateTime('updated_at')->nullable()->change();
		});
	}

}
