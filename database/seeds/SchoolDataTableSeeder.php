<?php

use Illuminate\Database\Seeder;

class SchoolDataTableSeeder extends Seeder {

    public function run()
    {
        DB::table('tbb_school_data')->insert([
            'school_id' => 22,
            'slug'=> 'indwes',
            'use_butler' => true,
            'default_pref' => 'opt out',
        ]);

        DB::table('tbb_school_data')->insert([
            'school_id' => 19,
            'slug'=> 'taylor',
            'use_butler' => true,
            'default_pref' => 'rent',
        ]);

        DB::table('tbb_school_data')->insert([
            'school_id' => 17,
            'slug'=> 'obu',
            'use_butler' => true,
            'default_pref' => 'opt out',
        ]);

        DB::table('tbb_school_data')->insert([
            'school_id' => 13,
            'slug'=> 'malone',
            'use_butler' => true,
            'default_pref' => 'opt out',
        ]);

        DB::table('tbb_school_data')->insert([
            'school_id' => 1,
            'slug'=> 'anderson',
            'use_butler' => true,
            'default_pref' => 'opt out',
        ]);

        DB::table('tbb_school_data')->insert([
            'school_id' => 3,
            'slug'=> 'asburyseminary',
            'use_butler' => true,
            'default_pref' => 'rent',
        ]);
    }

}