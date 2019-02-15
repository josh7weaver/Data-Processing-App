<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbbLog extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbb_log', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('channel')->nullable()->index();
            $table->string('message')->nullable();
            $table->string('level')->nullable()->index();
            $table->string('context')->nullable();
            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbb_log');
    }

}
