<?php
namespace App\Custom;

use App\Office;
use App\Division;

class OfficeDivisionQuerier
{
	public function getAllOfficeAndDivision($linkable_to_employee = true, $as_json = true)
	{
		$offices = Office::select(['name', 'name_acronym'])->where('linkable_to_employee', '=', $linkable_to_employee)->get();
        $divisions = Division::select(['name', 'name_acronym'])->get();
        $offices_and_divisions = $offices->concat($divisions);

        if(!$as_json)
        	return $offices_and_divisions;

        return $offices_and_divisions->toJson();
	}

	public function getFromOfficeOrDivision($column, $value)
    {
        $office = Office::where($column, $value)->first();

        if($office instanceof Office)
            return $office;

        $division = Division::where($column, $value)->first();

        if($division instanceof Division)
            return $division;

        return null;
    }

    public function insertEmployeeOffice(&$employee, $office)
    {
        if($office instanceof Office)
        {
            $employee->office_id = $office->office_id;
            $employee->division_id = null;
        }
        else
        {
            $employee->division_id = $office->division_id;
            $employee->office_id = null;
        }
    }
}
?>