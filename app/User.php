<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    protected $primaryKey = 'user_id';

    protected $hidden = [
        'password',
    ];

    public function document_logs()
    {
        return $this->hasMany('App\DocumentLog', 'user_id', 'user_id');
    }

    public function role_users()
    {
        return $this->hasMany('App\RoleUser', 'user_id', 'user_id');
    }
    
	//https://stackoverflow.com/questions/43467328/laravel-5-authentication-without-remember-token
    //https://laravel.io/forum/05-21-2014-how-to-disable-remember-token
    public function setAttribute($key, $value)
	{
    	$isRememberTokenAttribute = $key == $this->getRememberTokenName();
    	if (!$isRememberTokenAttribute)
    	{
    		parent::setAttribute($key, $value);
    	}
	}
}
