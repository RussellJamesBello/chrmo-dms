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

		<div class="ui raised segment">
			<input-field
				class="{!! $checker->check('name') !!}
				label="Name"
				type="text"
				name="name"
				value="{{ !old('name') ? $division->name : old('name') }}"
				:dynamic-attribs="{maxlength: 80, required: true}">
			</input-field>

			<input-field
				class="{!! $checker->check('acronym') !!}
				label="Acronym"
				type="text"
				name="acronym"
				value="{{ !old('acronym') ? $division->name_acronym : old('acronym') }}"
				:dynamic-attribs="{maxlength: 15, required: true}">
			</input-field>

			<select-field
				label="Office" 
				name="office" 
				:options="{{ $offices_json }}"
				:default-is-disabled="true"
				disabled-text="-- Select Office --"
				value="{{ !old('office') ? $division->office->name_acronym : old('office') }}"
				:dynamic-attribs="{required: true}">
			</select-field>

			<animated-button
				type="submit"
				class="fluid basic blue fade"
				visible-icon="edit"
				hidden-text="Edit Division">
			</animated-button>
		</div>
	</form>
</div>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/edit_division/options.js') }}"></script>
@endsection

@section('sub_custom_js')

<script>
	$('.field').popup();
</script>

@endsection