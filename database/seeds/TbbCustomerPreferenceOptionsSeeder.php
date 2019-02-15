<?php

use Illuminate\Database\Seeder;

class TbbCustomerPreferenceOptionsSeeder extends Seeder
{
	// auto triggered
	public function run()
	{
		DB::table('tbb_fulfillment_preferences')->insert([
           'title'=>'Prefer RENT',
           'code' => 'rent',
           'description'=>'Rental course materials will be provided whenever possible. Some resources are only available for purchase and will be substituted as necessary.<i>By selecting "rent" I agree to Tree of Life <a href="/rental-terms-and-conditions/" target="_blank">Rental Terms and Conditions.</a></i>',
           'cost'=>'$',
           'order'=>'10',
        ]);

        DB::table('tbb_fulfillment_preferences')->insert([
            'title'=>'Prefer USED/NEW PURCHASE',
            'code' => 'buy used',
            'description'=>'Used course materials will be provided for purchase whenever available. Some titles are only available new and will be substituted as necessary.',
            'cost'=>'$$',
            'order'=>'20',
        ]);

        DB::table('tbb_fulfillment_preferences')->insert([
            'title'=>'Prefer NEW PURCHASE',
            'code' => 'buy new',
            'description'=>'New course materials will be provided for purchase whenever available. Occasionally a title may only be available used and will be substituted as necessary.',
            'cost'=>'$$$',
            'order'=>'30',
        ]);

        DB::table('tbb_fulfillment_preferences')->insert([
            'title'=>'Opt Out',
            'code' => 'opt out',
            'description'=>'No course materials will be provided. <i>By opting out, you are responsible to source all of your course materials.</i>',
            'cost'=>'',
            'order'=>'40',
        ]);

        $optionIds = DB::table('tbb_fulfillment_preferences')->lists('id');
        $schoolIds = DB::table('tbb_school_data')->lists('school_id');

        foreach ($schoolIds as $schoolId)
        {
            foreach($optionIds as $optionId)
            {
                DB::table('tbb_school_fulfillment_pivot')->insert([
                   'school_id'=>$schoolId,
                   'fulfillment_preference_id'=> $optionId
                ]);
            }
        }

    }
}