<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('SchoolsTableSeeder');
        $this->call('DivisionTableSeeder');
        $this->call('SchoolDataTableSeeder');
        $this->call('TbbCustomerPreferenceOptionsSeeder');
        $this->call('TestCustomerSeeder');
    }

}
