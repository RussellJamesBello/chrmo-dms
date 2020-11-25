@extends('layouts.authenticated')

@section('sub_content')

<div style="overflow: auto;">
	<a href="{{ url('employees/' . $employee->employee_id) }}" class="ui green right floated mini button">Info</a>
</div>

<div class="ui stackable centered grid">
	<form class="ui seven wide column center aligned form {{ $errors->any() ? 'error' : 'success' }}" method="POST" action="{{ url()->current() }}">
		@method('PUT')
		@csrf

		@include('commons.success_message')

		<div class="ui raised segment">
			<select-field
				class="{!! $checker->check('office') !!}
				label="Office" 
				name="office" 
				:options="{{ $offices_and_divisions_json }}"
				:default-is-disabled="true"
				disabled-text="-- Select Office --"
				value="{{ old('office') ? old('office') : $employee->getOffice()->name_acronym }}">
			</select-field>

			<input-field
				class="{!! $checker->check('first_name') !!}
				label="First Name"
				type="text"
				name="first_name"
				value="{{ old('first_name') ? old('first_name') : $employee->first_name }}"
				:dynamic-attribs="{maxlength: 40}">
			</input-field>

			<input-field
				class="{!! $checker->check('middle_name') !!}
				label="Middle Name"
				type="text"
				name="middle_name"
				value="{{ old('middle_name') ? old('middle_name') : $employee->middle_name }}"
				:dynamic-attribs="{maxlength: 30}">
			</input-field>

			<input-field
				class="{!! $checker->check('last_name') !!}
				label="Last Name"
				type="text"
				name="last_name"
				value="{{ old('last_name') ? old('last_name') : $employee->last_name }}"
				:dynamic-attribs="{maxlength: 30}">
			</input-field>

			<select-field
				class="{!! $checker->check('suffix') !!}
				label="Suffix" 
				name="suffix" 
				:options="{{ $suffixes_json }}"
				disabled-text="-- Select Suffix --"
				:default-is-disabled="true"
				value="{{ old('suffix') ? old('suffix') : $employee->suffix_name }}">
			</select-field>

			<animated-button
				type="submit"
				class="fluid basic yellow fade"
				visible-icon="edit"
				hidden-text="Edit Info">
			</animated-button>
		</div>
	</form>
</div>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/edit_employee/options.js') }}"></script>
@endsection

@section('sub_custom_js')
	<script>$('.field').popup();</script>
@endsection