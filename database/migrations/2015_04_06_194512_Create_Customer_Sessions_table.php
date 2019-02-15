<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbb_customer_sessions', function(Blueprint $table)
		{
            $table->integer('customer_id')->unsigned()->primary();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('remember_token')->index();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbb_customer_sessions');
	}

}
