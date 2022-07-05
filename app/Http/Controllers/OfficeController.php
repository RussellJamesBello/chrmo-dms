<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Custom\OfficeDivisionQuerier;
use App\Office;
use App\Division;
use App\Employee;
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
            'office_type' => 'bail|required|in:office,division',
    		'name' => 'bail|required|alpha_spaces|max:80|unique:offices,name|unique:divisions,name',
    		'acronym' => 'bail|required|alpha|max:15|unique:offices,name_acronym|unique:divisions,name_acronym',
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
				'name' => 'bail|required|alpha_spaces|max:80|unique:offices,name,' . $office->office_id . ',office_id|unique:divisions,name',
				'acronym' => 'bail|required|alpha|max:15|unique:offices,name_acronym,' . $office->office_id . ',office_id|unique:divisions,name_acronym',
				'division_link_only' => 'bail|required|boolean',
			])->validate();

            $old_path = getScannedFolderName() . $office->name_acronym;

    		$office->name = $this->request->name;
    		$office->name_acronym = strtoupper($this->request->acronym);
    		$office->linkable_to_employee = !(bool)$this->request->division_link_only;
    		$office->save();

            $new_path = getScannedFolderName() . $office->name_acronym;

            if(Storage::exists($old_path) && $old_path != $new_path)
                Storage::move($old_path, $new_path);

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
				'name' => 'bail|required|alpha_spaces|max:80|unique:divisions,name,' . $division->division_id . ',division_id|unique:offices,name',
				'acronym' => 'bail|required|alpha|max:15|unique:divisions,name_acronym,' . $division->division_id . ',division_id|unique:offices,name_acronym',
				'office' => 'bail|required|exists:offices,name_acronym'
			])->validate();

			$office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);
            $old_path = getScannedFolderName() . $division->name_acronym;

    		$division->name = $this->request->name;
    		$division->office_id = $office->office_id;
    		$division->name_acronym = strtoupper($this->request->acronym);
    		$division->save();

            $new_path = getScannedFolderName() . $division->name_acronym;

            if(Storage::exists($old_path) && $old_path != $new_path)
                Storage::move($old_path, $new_path);

	    	return back()->with('success', ['header' => 'Division Edited Successfully!']);
    	}
    }

    public function transferEmployeesBeforeRemove($office)
    {
        $office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $office);

        if($this->request->isMethod('get'))
        {
            $offices_divisions = resolve(OfficeDivisionQuerier::class)->getAllOfficeAndDivision(true, false);
            $offices_divisions = $offices_divisions->where('name', '!=', $office->name)->values();

            return view('office.transfer_employees_office', [
                'title' => "Transfer Employees from $office->name_acronym before office deletion",
                'offices_and_divisions_json' => $offices_divisions->toJson(),
                'employees' => $office->employees
            ]);
        }

        elseif($this->request->isMethod('put'))
        {
            $validator = Validator::make($this->request->all(), [
                'employees' => 'bail|required|array',
                'employees.*.id' => 'bail|required|distinct|exists:employees,employee_id',
                'employees.*.new_office' => 'bail|sometimes|required',
                'employees.*.remove_employee' => 'bail|sometimes|in:on'
            ], [
                'employees.*.new_office.required' => 'This new office field is required.'
            ]);

            $validator->after(function($validator) use($office) {
                $total_transfer_employees = count($this->request->employees);

                if($office instanceof Office)
                    $column = 'office_id';

                else
                    $column = 'division_id';

                for($i = 1; $i <= $total_transfer_employees; $i++)
                {
                    if(Employee::find($this->request->employees[$i]['id'])->$column != $office->$column)
                        $validator->errors()->add("employees.$i.id", 'The employee does not belong to the selected office to be removed.');

                    if(isset($this->request->employees[$i]['new_office']))
                    {
                        $new_office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->employees[$i]['new_office']);

                        //new office validation
                        if($new_office == null)
                            $validator->errors()->add("employees.$i.new_office", 'The selected office does not exist.');

                        elseif($new_office->name_acronym == $office->name_acronym)
                            $validator->errors()->add("employees.$i.new_office", 'The selected office is the office being transferred from.');
                    }
                }
            });

            $validator->validate();
            
            foreach($this->request->employees as $transfer_employee)
            {
                $employee = Employee::find($transfer_employee['id']);

                if(isset($transfer_employee['new_office']))
                {
                    $office_to_transfer_to = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $transfer_employee['new_office']);

                    $old_path = getEmployeeFolder($employee);

                    resolve(OfficeDivisionQuerier::class)->insertEmployeeOffice($employee, $office_to_transfer_to);

                    $employee->save();

                    //refresh the model otherwise getEmployeeFolder() will return the directory for the old office. Don't know why this happens.
                    $employee->refresh();
                    $new_path = getEmployeeFolder($employee);

                    Storage::move($old_path, $new_path);
                }
                
                elseif(isset($transfer_employee['remove_employee']))
                {
                    $deleted = Storage::deleteDirectory(getEmployeeFolder($employee));

                    if($deleted)
                        $employee->delete();
                }
            }

            Storage::deleteDirectory(getScannedFolderName() . $office->name_acronym);
            $office->delete();

            return redirect('offices');
        }

        else
            return response()->json([], 403);
    }

    public function removeOffice(Office $office)
    {
        /*$divisions = $office->divisions;

        if($divisions->isNotEmpty())
            foreach($divisions as $division)
                Storage::deleteDirectory(getScannedFolderName() . $division->name_acronym);*/

        Storage::deleteDirectory(getScannedFolderName() . $office->name_acronym);
        $office->delete();

    	return back();
    }

    public function removeDivision(Division $division)
    {
        Storage::deleteDirectory(getScannedFolderName() . $division->name_acronym);
    	$division->delete();

    	return back();
    }
}
