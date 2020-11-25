@extends('layouts.authenticated')

@section('sub_content')

<div class="ui form text_center">
	<div class="fields">
		<div class="six wide field"></div>
		<select-field
			class="four"
			label="Filter Office"
			:options="{{ $offices_and_divisions_json }}"
			:default-is-disabled="true"
			@input="requestEmployeesFromSpecificOffice"
			disabled-text="-- Select Office --">
		</select-field>

		@if(request()->office)
			<div class="three wide field">
				<a class="ui green button" href="{{ url('employees') }}" style="margin-top: 23px;">Remove Office Filtering</a>
			</div>
		@endif
	</div>
</div>

@if($employees)
	<div class="ui mini right floated horizontal statistic">
		<div class="value">
			{{ $total_employee }}
		</div>

		<div class="label">
			Total Employees
		</div>
	</div>

	<generic-table class="blue celled compact selectable striped center aligned">
		<template slot="head">
			<tr>
				<th>Name</th>
				<th>Office</th>
				<th>Options</th>
			</tr>
		</template>

		<template slot="body">
			@foreach($employees as $employee)
				<tr>
					<td>{{ $employee->getWholeName() }}</td>
					<td>{{ $employee->office ? $employee->office->name_acronym : $employee->division->name_acronym }}</td>
					<td class="collapsing">
						<simple-dropdown class="small">
							<template slot="label">
								<i class="cogs icon"></i>
								<i class="dropdown icon"></i>
							</template>

							<template slot="items">
								<a href="{{ url("employees/$employee->employee_id") }}" class="item">
									Info
								</a>
								<a href="{{ url("employees/$employee->employee_id/edit") }}" class="item">
									Edit
								</a>
								@role('administrator')
									<a @click="changeCurrentRemove('{{ url("employees/$employee->employee_id") }}', '{{ $employee->getWholeName() }}')" href="#" class="item">
										Delete
									</a>
								@endrole
							</template>
						</simple-dropdown>
					</td>
				</tr>
			@endforeach
		</template>

		<template slot="foot">
			@if($employees->hasPages())
				<tr>
					<th colspan="3">{{ $employees->appends(['office' => $office])->links() }}</th>
				</tr>
			@endif
		</template>
	</generic-table>
@else
	<div class="ui negative message">
		<div class="header">
			Invalid Office.
		</div>
		<p>You supplied a wrong Office. Please try again.</p>
	</div>
@endif

<delete-modal
	:form-action="current_form_action"
	id="remove_modal"
	modal-title="Remove Employee"
	:delete-name="current_delete_name"
	@close="reinitializeValues()">
	@csrf
	@method('DELETE')
</delete-modal>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/employee_list/options.js') }}"></script>
@endsection