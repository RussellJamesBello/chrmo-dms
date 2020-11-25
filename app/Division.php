<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
	protected $primaryKey = 'division_id';
	protected $hidden = ['name', 'name_acronym'];
	protected $appends = ['text', 'value'];

    public function employees()
    {
    	return $this->hasMany('App\Employee', 'division_id', 'division_id');
    }

    public function office()
    {
    	return $this->belongsTo('App\Office', 'office_id', 'office_id');
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
