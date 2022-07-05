<?php
	function getScannedFolderName()
	{
		return 'scan/';
	}

	function getEmployeeFolder($employee, $custom_directory = null, $document_name = null, $file_name = null, $use_root_folder = true, $use_absolute_path = false)
	{
		if($employee->office_id != null)
			$office = $employee->office->name_acronym;
		else
			$office = $employee->division->name_acronym;

		$root_folder = $use_root_folder ? getScannedFolderName() : '';

		if($use_absolute_path && $use_root_folder)
			$absolute_path = storage_path('app/');

		elseif($use_absolute_path && !$use_root_folder)
			$absolute_path = storage_path('app/') . getScannedFolderName();

		else
			$absolute_path = '';

		$path = $absolute_path . $root_folder . $office . '/' . str_replace(' ', '', $employee->employee_id . $employee->first_name . $employee->middle_name . $employee->last_name) . 
				str_replace('.', '', $employee->suffix_name);

		if($custom_directory != null)
			$path .=  '/' . $custom_directory;

		if($document_name != null)
		{
			$path .=  '/' . $document_name;

			if($file_name != null)
				$path .=  '/' . $file_name;
		}
		
		return $path;
	}
?>