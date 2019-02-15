<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnableProcessingToSchoolTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('enable_preprocessing')->default('false');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('enable_preprocessing');
        });
	}

}
