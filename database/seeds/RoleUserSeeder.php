<?php

use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$admin = DB::table('users')->where('username', 'admin')->first();
    	$admin_role = DB::table('roles')->where('name', 'Administrator')->first();

        DB::table('role_user')->insert([
        	'user_id' => $admin->user_id,
        	'role_id' => $admin_role->id
        ]);
    }
}
