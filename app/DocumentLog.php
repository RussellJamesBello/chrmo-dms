<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentLog extends Model
{
    protected $primaryKey = 'document_log_id';

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function document()
    {
    	return $this->belongsTo('App\Document', 'document_id', 'document_id');
    }
}
