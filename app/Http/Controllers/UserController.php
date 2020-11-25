<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\RoleUser;
use Validator;

class UserController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
    	$this->request = $request;
    }

    public function showUserManagement()
    {
    	$role = Role::where('name', '=', 'standard_user')->first();

    	return view('user.dashboard', [
    		'title' => 'User Administration',
    		'users' => User::whereHas('role_users', function($query) use($role){
    			$query->where('role_id', '=', $role->id);
    		})->get()
    	]);
    }

    public function AddUser()
    {
    	$validator = Validator::make($this->request->all(), [
    		'name' => 'bail|required|alpha_spaces|max:80',
    		'username' => 'bail|required|alpha_num_spaces|max:15|unique:users,username',
    		'password' => 'bail|required|min:5|max:20|confirmed',
    	])->validate();

    	$user = new User;
    	$user->name = $this->request->name;
    	$user->username = $this->request->username;
    	$user->password = bcrypt($this->request->password);
    	$user->save();

    	$role = Role::where('name', '=', 'standard_user')->first();

		DB::table('role_user')->insert([
			'user_id' => $user->user_id, 'role_id' => $role->id
		]);

    	return back()->with('success', ['header' => 'User Added Successfully!']);
    }

    public function EditUser(User $user)
    {
    	if($this->request->isMethod('get'))
    	{
    		return view('user.edit', [
    			'title' => 'Edit User',
    			'user' => $user
    		]);
    	}

    	elseif($this->request->isMethod('put'))
    	{
    		$validator = Validator::make($this->request->all(), [
	    		'name' => 'bail|required|alpha_spaces|max:80',
	    		'username' => "bail|required|alpha_num_spaces|max:15|unique:users,username,{$user->user_id},user_id",
	    		'password' => 'bail|required|min:5|max:20|confirmed',
	    	])->validate();

	    	$user->name = $this->request->name;
	    	$user->username = $this->request->username;
	    	$user->password = bcrypt($this->request->password);
	    	$user->save();

	    	return back()->with('success', ['header' => 'User Edited Successfully!']);
    	}
    }

    public function removeUser(User $user)
    {
        $role = Role::where('name', '=', 'standard_user')->first();

        $removables = User::whereHas('role_users', function($query) use($role){
            $query->where('role_id', '=', $role->id);
        })->get();

        if($removables->contains('user_id', $user->user_id))
            $user->delete();

        return back();
    }
}
