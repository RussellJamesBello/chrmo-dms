@extends('layouts.authenticated')

@section('sub_content')

<form id="transfer_employees_form" method="POST" action="{{ url()->current() }}">
	@method('PUT')
	@csrf
</form>

<div style="overflow: auto;">
	<a href="{{ url('offices') }}" class="ui grey right floated mini button">Back</a>
</div>

<generic-table class="blue celled striped center aligned">
	<template slot="head">
		<tr>
			<th>Name</th>
			<th class="collapsing">New Office</th>
			<th class="collapsing">Remove Employee</th>
		</tr>
	</template>

	<template slot="body">
		@php
			$remove_employee_checkbox_statuses = collect();
		@endphp

		@foreach($employees as $employee)
			<tr class="{{ !$errors->has('employees.' . $loop->iteration . '.new_office') ?: 'error' }}">
				<td>
					{{ $employee->getWholeName() }}
					<input type="hidden" name="employees[{{ $loop->iteration }}][id]" value="{{ $employee->employee_id }}" form="transfer_employees_form">
				</td>

				<td>
					<select-field
						class="{!! $checker->check('employees.' . $loop->iteration . '.new_office') !!}
						label=""
						value="{{ isset(old('employees')[$loop->iteration]['new_office']) ? old('employees')[$loop->iteration]['new_office'] : '' }}"
						name="employees[{{ $loop->iteration }}][new_office]"
						:dynamic-attribs="{form: 'transfer_employees_form', disabled: new_offices_status[{!! $loop->index !!}]}"
						:options="{{ $offices_and_divisions_json }}">
					</select-field>
				</td>

				<td>
					<checkbox-field
						class="{!! $checker->check('employees.' . $loop->iteration . '.remove') !!}
						label=""
						name="employees[{{ $loop->iteration }}][remove_employee]"
						:dynamic-attribs="{form: 'transfer_employees_form'}"
						:checked="{!! isset(old('employees')[$loop->iteration]['remove_employee']) ? 'true' : 'false' !!}"
						@change="disableNewOfficeCheckbox({!! $loop->index !!})"
					>
					</checkbox-field>
				</td>
			</tr>

			@php
				if(isset(old('employees')[$loop->iteration]['remove_employee']))
					$remove_employee_checkbox_statuses->push(true);
				else
					$remove_employee_checkbox_statuses->push(false);
			@endphp
		@endforeach
	</template>

	<template slot="foot">
		<tr>
			<td colspan="3">
				<animated-button
					type="submit"
					class="fluid basic blue fade"
					visible-icon="large save"
					hidden-text="Transfer Employees"
					form="transfer_employees_form">
				</animated-button>
			</td>
		</tr>
	</template>
</generic-table>

@endsection

@section('vue_options')
	<script>
		var checkbox_statuses = {!! $remove_employee_checkbox_statuses->toJson() !!}
	</script>
	<script src="{{ mix('/js/transfer_employees/options.js') }}"></script>
@endsection

@section('sub_custom_js')
<script>
	$('.field').popup();
	$('.ui.checkbox').checkbox();
</script>
@endsection