<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $primaryKey = 'id';

    public function role_users()
    {
        return $this->hasMany('App\RoleUser', 'role_id', 'id');
    }
}
