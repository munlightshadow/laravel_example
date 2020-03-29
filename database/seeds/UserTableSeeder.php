<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $role_admin = Role::where('name', 'admin')->first();
	    $role_user  = Role::where('name', 'user')->first();
	 
	    $employee = new User();
	    $employee->name = 'admin';
	    $employee->email = 'admin@test.com';
	    $employee->password = bcrypt('qweqwe');
	    $employee->save();
	    $employee->roles()->attach($role_admin);
	 
	    $employee = new User();
	    $employee->name = 'user1';
	    $employee->email = 'user1@test.com';
	    $employee->password = bcrypt('qweqwe');
	    $employee->save();
	    $employee->roles()->attach($role_user);

	    $employee = new User();
	    $employee->name = 'user2';
	    $employee->email = 'user2@test.com';
	    $employee->password = bcrypt('qweqwe');
	    $employee->save();
	    $employee->roles()->attach($role_user);	    	    	    

    }
}
