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
        $cvmo = DB::table('offices')->where('name_acronym', 'CVMO')->first();
        $cho = DB::table('offices')->where('name_acronym', 'CHO')->first();

        DB::table('divisions')->insert([
        	['office_id' => $cmo->office_id, 'name' => 'Mayor\'s Staff', 'name_acronym' => 'CMO-STAFF'],
        	['office_id' => $cmo->office_id, 'name' => 'Civil Security Unit', 'name_acronym' => 'CSU'],
        	['office_id' => $cmo->office_id, 'name' => 'Administrative and Records Division', 'name_acronym' => 'ADMIN'],
        	['office_id' => $cmo->office_id, 'name' => 'Internal Control Division', 'name_acronym' => 'ICD'],
        	['office_id' => $cmo->office_id, 'name' => 'Permits and Licenses Division', 'name_acronym' => 'PLD'],
        	['office_id' => $cmo->office_id, 'name' => 'Cooperative Services', 'name_acronym' => 'COOP'],
        	['office_id' => $cmo->office_id, 'name' => 'City Disaster Risk Reduction and Management Office', 'name_acronym' => 'CDRRMO'],
        	['office_id' => $cmo->office_id, 'name' => 'Department of Public Safety', 'name_acronym' => 'DPS'],
        	['office_id' => $cmo->office_id, 'name' => 'Tourism Office', 'name_acronym' => 'TOUR'],
        	['office_id' => $cmo->office_id, 'name' => 'City Library', 'name_acronym' => 'LIB'],

            ['office_id' => $cvmo->office_id, 'name' => 'SP Legislative', 'name_acronym' => 'SP-LEGIS'],

        	['office_id' => $cho->office_id, 'name' => 'Population Services', 'name_acronym' => 'POPCOM'],
        ]);
    }
}
