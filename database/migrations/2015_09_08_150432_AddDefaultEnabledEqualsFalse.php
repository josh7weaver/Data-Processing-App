<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultEnabledEqualsFalse extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courses', function($table)
		{
            $table->boolean('enabled')->default(false)->change();
		});

        Schema::table('sections', function($table)
        {
            $table->boolean('enabled')->default(false)->change();
        });

        Schema::table('enrollments', function($table)
        {
            $table->boolean('enabled')->default(false)->change();
        });

        Schema::table('customers', function($table)
        {
            $table->boolean('enabled')->default(false)->change();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('courses', function($table)
        {
            $table->boolean('enabled')->default(NULL)->change();
        });

        Schema::table('sections', function($table)
        {
            $table->boolean('enabled')->default(NULL)->change();
        });

        Schema::table('enrollments', function($table)
        {
            $table->boolean('enabled')->default(NULL)->change();
        });

        Schema::table('customers', function($table)
        {
            $table->boolean('enabled')->default(NULL)->change();
        });
	}

}
