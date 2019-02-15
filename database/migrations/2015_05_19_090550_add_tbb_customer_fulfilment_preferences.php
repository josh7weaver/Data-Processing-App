<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTbbCustomerFulfilmentPreferences extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('tbb_fulfillment_preferences', function($table)
        {
            $table->increments('id');
            $table->text('title');
            $table->text('code');
            $table->text('description');
            $table->text('cost');
            $table->tinyInteger('order')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbb_fulfillment_preferences');
    }

}
