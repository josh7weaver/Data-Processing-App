<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use DataStaging\Models\Division;

class DivisionTableSeeder extends Seeder {

	public function run()
	{
        //anderson
        Division::create([
            'school_id'=> 1,
            'name' => 'AU - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 1,
            'name' => 'AU - ADULT STUDIES',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 1,
            'name' => 'AU - MBA PROGRAM',
            'enabled' => true,
            'use_butler' => false
        ]);

        //andrews

        //asbury
        Division::create([
            'school_id'=> 3,
            'name' => 'Extended Learning',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 3,
            'name' => 'Asbury Extension Sites',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 3,
            'name' => 'Florida Dunnam Campus',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 3,
            'name' => 'Kentucky Campus',
            'enabled' => true,
            'use_butler' => true
        ]);


        //bluffton
        Division::create([
            'school_id'=> 4,
            'name' => 'BU - BCOMP',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 4,
            'name' => 'BU - DIETETICS',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 4,
            'name' => 'BU - GRAD MAED',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 4,
            'name' => 'BU - MAOM/MBA',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 4,
            'name' => 'BU - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);

        //cairn
        Division::create([
            'school_id'=> 5,
            'name' => 'Cairn - Degree Completion',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 5,
            'name' => 'Cairn - Graduate',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 5,
            'name' => 'Cairn - Undergraduate',
            'enabled' => true,
            'use_butler' => true
        ]);

        //cornerstone
        Division::create([
            'school_id'=> 6,
            'name' => 'CU - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);

        //grace bible
        Division::create([
            'school_id'=> 7,
            'name' => 'GB - AOE',
            'enabled' => true,
            'use_butler' => true
        ]);

        // Grace College
        Division::create([
            'school_id'=> 8,
            'name' => 'GC - WEBER',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 8,
            'name' => 'GC - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 8,
            'name' => 'GC - SEMINARY',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 8,
            'name' => 'GC - GRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 8,
            'name' => 'GC - GOAL',
            'enabled' => true,
            'use_butler' => true
        ]);

        // Greenville
        Division::create([
            'school_id'=> 9,
            'name' => 'GVC - GOL',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 9,
            'name' => 'GVC - TRADITIONAL',
            'enabled' => true,
            'use_butler' => false
        ]);

        //huntington


        //IWU
        Division::create([
			'school_id' => 11,
			'name' => 'IWU - Traditional',
			'enrollment_percentage' => .47,
            'enrollment_adjustment_enabled' => true,
			'enabled' => true,
            'use_butler' => true
		]);
        Division::create([
            'school_id'=> 22,
            'name' => 'IWU - NonRes - Core',
            'enrollment_percentage' => .47,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 22,
            'name' => 'IWU - NonRes - Elective',
            'enrollment_percentage' => .47,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true
        ]);

        //Letourneau
        Division::create([
            'school_id' => 12,
            'name' => 'LETU - Traditional',
            'enrollment_percentage' => .05,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true

        ]);
        Division::create([
            'school_id' => 12,
            'name' => 'LETU - Nontraditional',
            'enrollment_percentage' => .05,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true
        ]);

        //Malone
        Division::create([
            'school_id'=> 13,
            'name' => 'MU - GRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 13,
            'name' => 'MU - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);

        //Mt.Vernon

        //Northwestern

        //OCU
        Division::create([
            'school_id'=> 16,
            'name' => 'OCU - AIM',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 16,
            'name' => 'OCU - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 16,
            'name' => 'OCU - PSEO High Schools',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 16,
            'name' => 'OCU - PSEO Online',
            'enabled' => true,
            'use_butler' => false
        ]);

        //OBU
        Division::create([
            'school_id' => 17,
            'name' => 'OBU - Traditional',
            'enrollment_percentage' => .64,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 17,
            'name' => 'OBG - Graduate',
            'enabled' => true,
            'use_butler' => false
        ]);

        //Spring Arbor
        Division::create([
            'school_id'=> 18,
            'name' => 'SAU - GPS',
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id'=> 18,
            'name' => 'SAU - TRADITIONAL',
            'enabled' => true,
            'use_butler' => false
        ]);

        //Taylor
        Division::create([
            'school_id' => 19,
            'name' => 'Taylor - Undergrad',
            'enrollment_percentage' => .40,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true
        ]);
        Division::create([
            'school_id' => 19,
            'name' => 'Taylor - Graduate',
            'enrollment_percentage' => .28,
            'enrollment_adjustment_enabled' => true,
            'enabled' => true,
            'use_butler' => true
        ]);

        //Trevecca
        Division::create([
            'school_id'=> 20,
            'name' => 'TNU - GRAD AND CLL',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 20,
            'name' => 'TNU - UNDERGRADUATE',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 20,
            'name' => 'TNU - BILL TO STUDENT',
            'enabled' => true,
            'use_butler' => false
        ]);
        Division::create([
            'school_id'=> 20,
            'name' => 'TNU - BILL TO TREVECCA',
            'enabled' => true,
            'use_butler' => false
        ]);

        //Warner Pacific
        Division::create([
            'school_id'=> 21,
            'name' => 'WarnerPacific-ADP',
            'enabled' => true,
            'use_butler' => true
        ]);

    }

}