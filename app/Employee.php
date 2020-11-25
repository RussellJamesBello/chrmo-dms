<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use Eloquence;

    protected $primaryKey = 'employee_id';

    public function documents()
    {
    	return $this->hasMany('App\Document', 'employee_id', 'employee_id');
    }

    public function office()
    {
    	return $this->belongsTo('App\Office', 'office_id', 'office_id');
    }

    public function division()
    {
    	return $this->belongsTo('App\Division', 'division_id', 'division_id');
    }

    public function getWholeName()
    {
        //https://stackoverflow.com/questions/24570744/remove-extra-spaces-but-not-space-between-two-words?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
        return trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", "$this->first_name $this->middle_name $this->last_name $this->suffix_name")));
    }

    public function getOffice()
    {
        if($this->office)
            return $this->office;

        return $this->division;
    }
}
