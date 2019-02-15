<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('divisions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
			$table->string('name')->unique();
			$table->double('enrollment_percentage', 64)->default('1');
//			$table->string('default_pref', 128)->nullable();
            $table->boolean('enrollment_adjustment_enabled')->default(false)->index();
            $table->boolean('use_butler')->index()->nullable(); // this field is used for section enrollment adjusters
			$table->boolean('enabled')->index();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('divisions');
	}

}
