<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cmo = DB::table('offices')->where('name_acronym', 'CMO')->first();
        $cho = DB::table('offices')->where('name_acronym', 'CHO')->first();
        $lcpmcc = DB::table('offices')->where('name_acronym', 'LCPMCC')->first();

        DB::table('divisions')->insert([
        	['office_id' => $cmo->office_id, 'name' => 'Mayor\'s Staff', 'name_acronym' => 'MS'],
        	['office_id' => $cmo->office_id, 'name' => 'Civil Security Unit', 'name_acronym' => 'CSU'],
        	['office_id' => $cmo->office_id, 'name' => 'Administrative and Records Division', 'name_acronym' => 'ARD'],
        	['office_id' => $cmo->office_id, 'name' => 'Internal Control Division', 'name_acronym' => 'IRD'],
        	['office_id' => $cmo->office_id, 'name' => 'Permits and Licenses Division', 'name_acronym' => 'PLD'],
        	['office_id' => $cmo->office_id, 'name' => 'Cooperative Services', 'name_acronym' => 'COOP'],
        	['office_id' => $cmo->office_id, 'name' => 'City Disaster Risk Reduction & Management Office', 'name_acronym' => 'CDRRMO'],
        	['office_id' => $cmo->office_id, 'name' => 'Department of Public Safety', 'name_acronym' => 'DPS'],
        	['office_id' => $cmo->office_id, 'name' => 'LC Tourism and Social Concerns Council', 'name_acronym' => 'TOUR'],
        	['office_id' => $cmo->office_id, 'name' => 'Association of Barangay Councils Office', 'name_acronym' => 'ABC'],
        	['office_id' => $cmo->office_id, 'name' => 'International Organization for Standardization Office', 'name_acronym' => 'ISO'],
        	['office_id' => $cmo->office_id, 'name' => 'City Library', 'name_acronym' => 'CL'],

        	['office_id' => $cho->office_id, 'name' => 'Population Services', 'name_acronym' => 'POPCOM'],

        	['office_id' => $lcpmcc->office_id, 'name' => 'Laoag City Slaughterhouse', 'name_acronym' => 'LCS'],
        ]);
    }
}
