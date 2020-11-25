<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Custom\OfficeDivisionQuerier;
use App\Office;
use App\Division;
use Validator;

class OfficeController extends Controller
{
	protected $request;

    public function __construct(Request $request)
    {
    	$this->request = $request;
    }

    public function showOfficeManagement()
    {
    	return view('office.dashboard', [
    		'title' => 'Office Administration',
    		'offices' => Office::all(),
    		'offices_json' => Office::select(['name', 'name_acronym'])->get(),
    		'divisions' => Division::with('office')->get()
    	]);
    }

    public function addOfficeDivision()
    {
    	Validator::make($this->request->all(), [
    		'office_type' => 'bail|required|in:office,division'
    	])->validate();

    	if($this->request->office_type == 'office')
    	{
    		$name_rule = 'unique:offices,name';
    		$acronym_rule = 'unique:offices,name_acronym';
    	}

    	elseif($this->request->office_type == 'division')
    	{
    		$name_rule = 'unique:divisions,name';
    		$acronym_rule = 'unique:divisions,name_acronym';
    	}

    	Validator::make($this->request->all(), [
    		'name' => 'bail|required|alpha_spaces|max:80|' . $name_rule,
    		'acronym' => 'bail|required|alpha|max:15|' . $acronym_rule,
    		'division_link_only' => 'bail|required_if:office_type,office|boolean',
    		'office' => 'bail|required_if:office_type,division|exists:offices,name_acronym'
    	])->validate();

    	if($this->request->office_type == 'office')
    	{
    		$office = new Office;
    		$office->name = $this->request->name;
    		$office->name_acronym = strtoupper($this->request->acronym);
    		$office->linkable_to_employee = !(bool)$this->request->division_link_only;
    		$office->save();

    		$success_message_prefix = 'Office';
    	}

    	elseif($this->request->office_type == 'division')
    	{
    		$office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);

    		$division = new Division;
    		$division->office_id = $office->office_id;
    		$division->name = $this->request->name;
    		$division->name_acronym = strtoupper($this->request->acronym);
    		$division->save();

    		$success_message_prefix = 'Division';
    	}

    	return back()->with('success', ['header' => $success_message_prefix . ' Added Successfully!']);
    }

    public function editOffice(Office $office)
    {
    	if($this->request->isMethod('get'))
    	{
    		return view('office.edit_office', [
    			'title' => 'Edit Office',
    			'office' => $office
    		]);
    	}

    	elseif($this->request->isMethod('put'))
    	{
			Validator::make($this->request->all(), [
				'name' => 'bail|required|alpha_spaces|max:80|unique:offices,name',
				'acronym' => 'bail|required|alpha|max:15|unique:offices,name_acronym',
				'division_link_only' => 'bail|required|boolean',
			])->validate();

    		$office->name = $this->request->name;
    		$office->name_acronym = strtoupper($this->request->acronym);
    		$office->linkable_to_employee = !(bool)$this->request->division_link_only;
    		$office->save();

	    	return back()->with('success', ['header' => 'Office Edited Successfully!']);
    	}
    }

    public function editDivision(Division $division)
    {
    	if($this->request->isMethod('get'))
    	{
    		return view('office.edit_division', [
    			'title' => 'Edit Division',
    			'division' => $division,
    			'offices_json' => Office::select(['name', 'name_acronym'])->get(),
    		]);
    	}

    	elseif($this->request->isMethod('put'))
    	{
			Validator::make($this->request->all(), [
				'name' => 'bail|required|alpha_spaces|max:80|unique:divisions,name',
				'acronym' => 'bail|required|alpha|max:15|unique:divisions,name_acronym',
				'office' => 'bail|required|exists:offices,name_acronym'
			])->validate();

			$office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);

    		$division->name = $this->request->name;
    		$division->office_id = $office->office_id;
    		$division->name_acronym = strtoupper($this->request->acronym);
    		$division->save();

	    	return back()->with('success', ['header' => 'Division Edited Successfully!']);
    	}
    }

    public function removeOffice(Office $office)
    {
    	$office->delete();
    	return back();
    }

    public function removeDivision(Division $division)
    {
    	$division->delete();
    	return back();
    }
}
