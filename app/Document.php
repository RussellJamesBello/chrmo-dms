<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use Eloquence;
    
    protected $primaryKey = 'document_id';

    public function document_logs()
    {
    	return $this->hasMany('App\DocumentLog', 'document_id', 'document_id');
    }

    public function document_contents()
    {
    	return $this->hasMany('App\DocumentContent', 'document_id', 'document_id');
    }

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'employee_id', 'employee_id');
    }
}