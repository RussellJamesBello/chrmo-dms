@extends('layouts.authenticated')

@section('sub_content')

<div style="overflow: auto;">
	<a href="{{ url('offices') }}" class="ui grey right floated mini button">Back</a>
</div>

<div class="ui stackable centered grid">
	<form class="ui seven wide column center aligned form {{ $errors->any() ? 'error' : 'success' }}" method="POST" action="{{ url()->current() }}">
		@method('PUT')
		@csrf

		@include('commons.success_message')

		@php
			if(old('division_link_only') != null)
				$division_link_value = old('division_link_only');

			else
			{
				if($office->linkable_to_employee)
					$division_link_value = 0;

				else
					$division_link_value = 1;
			}
		@endphp

		<div class="ui raised segment">
			<input-field
				class="{!! $checker->check('name') !!}
				label="Name"
				type="text"
				name="name"
				value="{{ !old('name') ? $office->name : old('name') }}"
				:dynamic-attribs="{maxlength: 80, required: true}">
			</input-field>

			<input-field
				class="{!! $checker->check('acronym') !!}
				label="Acronym"
				type="text"
				name="acronym"
				value="{{ !old('acronym') ? $office->name_acronym : old('acronym') }}"
				:dynamic-attribs="{maxlength: 15, required: true}">
			</input-field>

			<select-field
				class="{!! $checker->check('division_link_only') !!}
				label="For Division Links Only" 
				name="division_link_only" 
				:options="[{text: 'Yes', value: 1}, {text: 'No', value: 0}]"
				:default-is-disabled="true"
				disabled-text=""
				value="{{ $division_link_value }}"
				:dynamic-attribs="{required: true}">
			</select-field>

			<animated-button
				type="submit"
				class="fluid basic blue fade"
				visible-icon="edit"
				hidden-text="Edit Office">
			</animated-button>
		</div>
	</form>
</div>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/edit_office/options.js') }}"></script>
@endsection

@section('sub_custom_js')

<script>
	$('.field').popup();
</script>

@endsection