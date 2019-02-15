<?php

use Illuminate\Database\Seeder;

class TestCustomerSeeder extends Seeder
{
	// auto triggered
	public function run()
	{
        \DataStaging\Models\Customer::create([
            'school_id' => '22',
            'customer_acct'=>'123456789',
            'student_acct'=>'123456789',
            'firstname'=>'Lester',
            'lastname'=>'Tester',
            'alias' => 'fake',
            'ship_addr_desc'=>'',
            'ship_addr'=>'',
            'ship_city'=>'',
            'ship_state'=>'',
            'ship_postal_code'=>'',
            'ship_country'=>'',
            'b_delete'=>false,
            'email'=>'jweaver@tolbookstores.com',
        ]);
    }
}