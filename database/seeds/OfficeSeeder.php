<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //since this query inserts three values, it is not included in the query below because MySQL will throw an error.
        DB::table('offices')->insert(['name' => 'City Mayor\'s Office', 'name_acronym' => 'CMO', 'linkable_to_employee' => false]);

        DB::table('offices')->insert([
            ['name' => 'Office of the City Administrator', 'name_acronym' => 'ADMIN'],

            ['name' => 'Office of the City Human Resource Management Officer', 'name_acronym' => 'CHRMO'],

            ['name' => 'Office of the City Legal Officer', 'name_acronym' => 'CLO'],

            ['name' => 'Office of the City Vice Mayor', 'name_acronym' => 'CVMO'],

            //['name' => 'Secretary to the Sanggunian\'s Office', 'name_acronym' => 'SP'],

            ['name' => 'Office of the City Community Affairs Officer', 'name_acronym' => 'CAD'],

            ['name' => 'Office of the City Information & Communication Technology Officer', 'name_acronym' => 'ICTO'],

            ['name' => 'Office of the City Environment & Natural Resources Officer', 'name_acronym' => 'CENR'],

            ['name' => 'Office of the City Agriculturist', 'name_acronym' => 'AGRI'],

            ['name' => 'Office of the City Treasury Officer', 'name_acronym' => 'CTO'],

            ['name' => 'Office of the City Assessor', 'name_acronym' => 'CAO'],

            ['name' => 'Office of the City Accountant', 'name_acronym' => 'ACCT'],

            ['name' => 'Office of the City Civil Registrar', 'name_acronym' => 'CRO'],

            ['name' => 'Office of the City Budget Officer', 'name_acronym' => 'CBO'],

            ['name' => 'Office of the City Planning & Development Coordinator', 'name_acronym' => 'CPDO'],

            ['name' => 'Office of the City Health Officer', 'name_acronym' => 'CHO'],

            ['name' => 'Office of the City Social Welfare & Development Officer', 'name_acronym' => 'CSWD'],

            ['name' => 'Office of the City General Services Officer', 'name_acronym' => 'GSO'],

            ['name' => 'City Veterinarian\'s Office', 'name_acronym' => 'CVO'],

            ['name' => 'Office of the City Engineer', 'name_acronym' => 'CEO'],

            ['name' => 'Laoag City General Hospital', 'name_acronym' => 'LCGH'],

            ['name' => 'Laoag City Public Market and Commercial Complex', 'name_acronym' => 'LCPMCC'],
        ]);
    }
}
