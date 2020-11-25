<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $primaryKey = 'office_id';
    protected $hidden = ['name', 'name_acronym'];
    protected $appends = ['text', 'value'];

    public function employees()
    {
    	return $this->hasMany('App\Employee', 'office_id', 'office_id');
    }

    public function divisions()
    {
    	return $this->hasMany('App\Division', 'office_id', 'office_id');
    }

    public function getTextAttribute()
    {
    	return $this->attributes['name'];
    }

    public function getValueAttribute()
    {
        return $this->attributes['name_acronym'];
    }
}
