<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Custom\OfficeDivisionQuerier;
use Illuminate\Support\Facades\Storage;
use App\Custom\SuffixesTrait;
use App\Employee;
use App\Office;
use App\Division;
use App\Document;
use Validator;

class EmployeeController extends Controller
{
    use SuffixesTrait;
    protected $request;

    public function __construct(Request $request)
    {
    	$this->request = $request;
    }

    public function getEmployeeList()
    {
    	$per_page = 100;

    	if($this->request->office)
    	{
    		$office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);

    		if($office instanceof Office)
    			$employees = Employee::where('office_id', $office->office_id)->paginate($per_page);

    		else if($office instanceof Division)
    			$employees = Employee::where('division_id', $office->division_id)->paginate($per_page);

            else
                $employees = null;

            $total_employee = $employees->total();
    	}

    	else
        {
    		$employees = Employee::with('office', 'division')->orderBy('updated_at', 'desc')->paginate($per_page);
            $total_employee = Employee::all()->count();
        }

    	return view('employee.list', [
    		'title' => $this->request->office ? "Employee List : {$this->request->office}" : 'Employee List',
    		'employees' => $employees,
            'total_employee' => $total_employee,
            'office' => $this->request->office,
            'offices_and_divisions_json' => resolve(OfficeDivisionQuerier::class)->getAllOfficeAndDivision()
    	]);
    }

    public function getEmployeeInfo(Employee $employee)
    {
        if($this->request->search_document != null)
            $documents = Document::where('employee_id', '=', $employee->employee_id)
                            ->search($this->request->search_document, ['name' => 2, 'keywords' => 1])
                            ->get();

        else
            $documents = Document::where('employee_id', $employee->employee_id)->paginate(50);

        return view('employee.info', [
            'title' => "Employee Info",
            'employee' => $employee,
            'documents' => $documents,
            'office' => $employee->getOffice()
        ]);
    }

    public function editEmployee(Employee $employee)
    {
        if($this->request->isMethod('get'))
        {
            return view('employee.edit', [
                'title' => 'Edit Employee Info',
                'employee' => $employee,
                'offices_and_divisions_json' => resolve(OfficeDivisionQuerier::class)->getAllOfficeAndDivision(),
                'suffixes_json' => collect($this->name_suffixes)->toJson(),
            ]);
        }

        elseif($this->request->isMethod('put'))
        {
            $office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);

            $validator = Validator::make($this->request->all(), [
                'office' => 'bail|required',
                'first_name' => 'bail|required|alpha_spaces|max:40',
                'middle_name' => 'nullable|bail|alpha_spaces|max:30',
                'last_name' => 'bail|required|alpha_spaces|max:30',
                'suffix' => 'nullable|in:' . implode(',', collect($this->name_suffixes)->flatten()->toArray())
            ]);

            $validator->after(function($validator) use($office){
                if($office == null)
                    $validator->errors()->add('office', 'The selected office is invalid.');
            });

            $validator->validate();

            $old_path = getEmployeeFolder($employee);

            //code for updating employee here
            resolve(OfficeDivisionQuerier::class)->insertEmployeeOffice($employee, $office);

            $employee->first_name = $this->request->first_name;
            $employee->middle_name = $this->request->middle_name;
            $employee->last_name = $this->request->last_name;
            $employee->suffix_name = $this->request->suffix;
            $employee->save();

            //refresh the model otherwise getEmployeeFolder() will return the directory for the old office. Don't know why this happens.
            $employee->refresh();
            $new_path = getEmployeeFolder($employee);

            if($old_path != $new_path)
                Storage::move($old_path, $new_path);

            return back()->with('success', ['header' => "Employee's Info has been updated successfully."]);
        }
    }

    public function searchEmployee()
    {
        return ['results' => Employee::search($this->request->keyword, ['first_name' => 4, 'middle_name' => 2, 'last_name' => 3, 'suffix_name' => 1])
                ->with(['office', 'division'])
                ->get()
                ->transform(function($item, $key){
                    return collect([
                                    'emp_id' => $item->employee_id,
                                    'emp_office' => $item->getOffice()->name_acronym,
                                    'emp_first_name' => $item->first_name,
                                    'emp_middle_name' => $item->middle_name ? $item->middle_name : '',
                                    'emp_last_name' => $item->last_name,
                                    'emp_suffix' => $item->suffix_name ? $item->suffix_name : '',

                                    'whole_name' => $item->getWholeName(),
                                    'office' => $item->getOffice()->name
                    ]); 
                })
            ];
    }

    public function removeEmployee(Employee $employee)
    {
        $deleted = Storage::deleteDirectory(getEmployeeFolder($employee));

        if($deleted)
            $employee->delete();

        return redirect('employees');
    }
}
