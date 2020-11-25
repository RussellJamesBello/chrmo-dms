<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\Employee;

class SearchController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
    	$this->request = $request;
    }

    public function searchQuery()
    {
    	$documents = Document::search($this->request->search, ['name' => 2, 'keywords' => 1])
    						->with(['employee'])
    						->get();

    	$employees = Employee::search($this->request->search, ['first_name' => 4, 'middle_name' => 2, 'last_name' => 3, 'suffix_name' => 1])
    						->with(['office', 'division'])
                			->get();

        return view('search.show', [
        	'title' => "Search Results for \"{$this->request->search}\"",
        	'documents' => $documents,
        	'employees' => $employees
        ]);
    }
}
