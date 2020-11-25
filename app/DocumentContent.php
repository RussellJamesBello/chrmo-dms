<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentContent extends Model
{
	protected $primaryKey = 'document_content_id';

	protected $fillable = ['document_id', 'page_number', 'file_name'];

	public function document()
	{
		return $this->belongsTo('App\Document', 'document_id', 'document_id');
	}
}
