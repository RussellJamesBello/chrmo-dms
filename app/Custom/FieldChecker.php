<?php
namespace App\Custom;

use Illuminate\Support\ViewErrorBag;

class FieldChecker
{
	protected $errors;

	public function __construct(ViewErrorBag $errors)
	{
		$this->errors = $errors;
	}

	public function check($field, $popup_orientation = 'top center')
	{
		return !$this->errors->has($field) ? '"' : ' error" data-content="' . $this->errors->first($field) . '" data-position="' . $popup_orientation . '"';
	}

	public function selected_option($old_data, $options, $existing_data = null)
	{
		
		if($old_data == null && $existing_data == null)
			return;

		$data_to_use = $old_data != null ? $old_data : $existing_data;

		foreach($options as $option)
		{
			if($option == $data_to_use)
				return $option;
		}
	}
}
?>