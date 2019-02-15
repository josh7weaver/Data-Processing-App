<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTbbSchoolCustomerFulfillmentPreferences extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('tbb_school_fulfillment_pivot', function($table)
        {
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('fulfillment_preference_id')->unsigned();
            $table->foreign('fulfillment_preference_id')->references('id')->on('tbb_fulfillment_preferences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbb_school_fulfillment_pivot');
    }

}
