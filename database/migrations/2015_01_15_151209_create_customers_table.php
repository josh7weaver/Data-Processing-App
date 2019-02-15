<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
			$table->text('customer_acct')->index();
			$table->text('student_acct')->index();
			$table->text('bad_check_num')->nullable();
			$table->text('salutation')->nullable();
			$table->text('firstname');
			$table->text('lastname');
			$table->text('alias')->nullable();;
			$table->text('email')->index();
			$table->text('notes')->nullable();
			$table->text('active_date')->nullable();
			$table->text('comment')->nullable();
			$table->text('preference')->index()->nullable()->default(NULL);
			$table->text('type_code')->nullable();
			$table->text('card_code')->nullable();
			$table->text('member_amount')->nullable();
			$table->text('ship_addr_desc')->nullable();;
			$table->text('ship_addr')->nullable();;
			$table->text('ship_city')->nullable();;
			$table->text('ship_state')->nullable();;
			$table->text('ship_postal_code')->nullable();;
			$table->text('ship_country')->nullable();;
			$table->text('ship_phone1')->nullable();
			$table->text('ship_phone2')->nullable();
			$table->text('ship_phone3')->nullable();
			$table->text('ship_ext1')->nullable();
			$table->text('ship_ext2')->nullable();
			$table->text('ship_ext3')->nullable();
			$table->text('bill_addr_desc')->nullable();
			$table->text('bill_addr')->nullable();
			$table->text('bill_city')->nullable();
			$table->text('bill_state')->nullable();
			$table->text('bill_postal_code')->nullable();
			$table->text('bill_country')->nullable();
			$table->text('bill_phone1')->nullable();
			$table->text('bill_phone2')->nullable();
			$table->text('bill_phone3')->nullable();
			$table->text('bill_ext1')->nullable();
			$table->text('bill_ext2')->nullable();
			$table->text('bill_ext3')->nullable();
			$table->boolean('b_delete')->index();
			$table->boolean('enabled')->index();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

			$table->unique(['school_id', 'customer_acct']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customers');
	}

}
