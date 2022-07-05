@extends('layouts.authenticated')

@section('sub_content')

@php
	if($errors->isNotEmpty() || isset(session('success')['header']))
	{
		$first_tab = '';
		$third_tab = 'active';
	}
	else
	{
		$first_tab = 'active';
		$third_tab = '';
	}
@endphp

<div class="ui top attached tabular menu">
	<a class="item {{ $first_tab }}" data-tab="first">Offices</a>
	<a class="item" data-tab="second">Divisions</a>
	<a class="item {{ $third_tab }}" data-tab="third">Add Office/Division</a>
</div>

<div class="ui bottom attached tab segment {{ $first_tab }}" data-tab="first" style="margin-bottom: 0;">
	<generic-table class="blue celled compact selectable striped center aligned">
		<template slot="head">
			<tr>
				<th>Office</th>
				<th>Acronym</th>
				<th>For Division Links Only</th>
				<th>Options</th>
			</tr>
		</template>

		<template slot="body">
			@foreach($offices as $office)
				<tr>
					<td>{{ $office->name }}</td>
					<td>{{ $office->name_acronym }}</td>
					<td>{{ $office->linkable_to_employee ? 'No' : 'Yes' }}</td>
					<td class="collapsing">
						<simple-dropdown class="small">
							<template slot="label">
								<i class="cogs icon"></i>
								<i class="dropdown icon"></i>
							</template>

							<template slot="items">
								<a href="{{ url("offices/$office->office_id/edit") }}" class="item">
									Edit
								</a>

								@php
									$has_division_employees = App\Employee::whereIn('division_id', $office->divisions->pluck('division_id')->toArray())->get()->isNotEmpty();
								@endphp

								@if($office->employees->isEmpty() && !$has_division_employees)
								<a @click="changeCurrentRemove('{{ url("offices/$office->office_id") }}', '{{ str_replace('\'', '\\\'', $office->name) }}')" href="#" class="item">
									Remove
								</a>
								@elseif($has_division_employees)
								<a href="#" class="item" data-tooltip="{{ $office->name_acronym }} cannot be deleted because it still has existing divisions." data-position="left center">
									Remove
								</a>
								@else
								<a href="{{ url("offices/{$office->name_acronym}/employee-transfer") }}" class="item">
									Remove
								</a>
								@endif
							</template>
						</simple-dropdown>
					</td>
				</tr>
			@endforeach
		</template>
	</generic-table>
</div>

<div class="ui bottom attached tab segment" data-tab="second">
	<generic-table class="blue celled compact selectable striped center aligned">
		<template slot="head">
			<tr>
				<th>Division</th>
				<th>Acronym</th>
				<th>Division Under</th>
				<th>Options</th>
			</tr>
		</template>

		<template slot="body">
			@foreach($divisions as $division)
				<tr>
					<td>{{ $division->name }}</td>
					<td>{{ $division->name_acronym }}</td>
					<td>{{ $division->office->name_acronym }}</td>
					<td class="collapsing">
						<simple-dropdown class="small">
							<template slot="label">
								<i class="cogs icon"></i>
								<i class="dropdown icon"></i>
							</template>

							<template slot="items">
								<a href="{{ url("divisions/$division->division_id/edit") }}" class="item">
									Edit
								</a>

								@if($division->employees->isEmpty())
								<a @click="changeCurrentRemove('{{ url("divisions/$division->division_id") }}', '{{ str_replace('\'', '\\\'', $division->name) }}')" href="#" class="item">
									Remove
								</a>
								@else
								<a href="{{ url("offices/{$division->name_acronym}/employee-transfer") }}" class="item">
									Remove
								</a>
								@endif
							</template>
						</simple-dropdown>
					</td>
				</tr>
			@endforeach
		</template>
	</generic-table>
</div>

<div class="ui bottom attached tab segment {{ $third_tab }}" data-tab="third">
	<div class="ui stackable centered grid">
		<form class="ui seven wide column center aligned form {{ $errors->any() ? 'error' : 'success' }}" method="POST" action="{{ url()->current() }}">
			@method('POST')
			@csrf

			@include('commons.success_message')

			<div class="ui raised segment">
				<div style="justify-content: center" class="inline fields {!! $checker->check('office_type') !!}>
					<label for="office_type">Office Type:</label>

					<radio-checkbox-field
						label="Office"
						name="office_type"
						value="office"
						:dynamic-attribs="{required: true}"
						@input="changeOfficeType(true)">
					</radio-checkbox-field>

					<radio-checkbox-field
						label="Division"
						name="office_type"
						value="division"
						:dynamic-attribs="{required: true}"
						@input="changeOfficeType(false)">
					</radio-checkbox-field>
				</div>

				<input-field
					class="{!! $checker->check('name') !!}
					label="Name"
					type="text"
					name="name"
					value="{{ old('name') }}"
					:dynamic-attribs="{maxlength: 80, required: true}">
				</input-field>

				<input-field
					class="{!! $checker->check('acronym') !!}
					label="Acronym"
					type="text"
					name="acronym"
					value="{{ old('acronym') }}"
					:dynamic-attribs="{maxlength: 15, required: true}">
				</input-field>

				<div v-show="isOffice == true">
					<br>
					<i id="division_link_tip" data-position="top center" data-content="If selected option is Yes, the office will only be used for linking divisions and not be included as an option in the office field when adding a document or editing an employee. Otherwise, the office can be used as an option in the said functions and can also be linked to divisions." class="question circle outline icon"></i>
				</div>

				<select-field
					label="For Division Links Only" 
					name="division_link_only" 
					:options="[{text: 'Yes', value: 1}, {text: 'No', value: 0}]"
					:default-is-disabled="true"
					disabled-text=""
					:dynamic-attribs="{required: true}"
					v-if="isOffice == true">
				</select-field>

				<select-field
					label="Office" 
					name="office" 
					:options="{{ $offices_json }}"
					:default-is-disabled="true"
					disabled-text="-- Select Office --"
					:dynamic-attribs="{required: true}"
					v-if="isOffice == false">
				</select-field>

				<animated-button
					type="submit"
					class="fluid basic blue fade"
					visible-icon="plus"
					hidden-text="Add Office/Division">
				</animated-button>
			</div>
		</form>
	</div>
</div>

<delete-modal
	:form-action="current_form_action"
	id="remove_modal"
	modal-title="Office/Division"
	:delete-name="current_delete_name"
	@close="reinitializeValues()">
	@csrf
	@method('DELETE')
</delete-modal>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/office_dashboard/options.js') }}"></script>
@endsection

@section('sub_custom_js')

<script src="{{ mix('/js/office_dashboard/logic.js') }}"></script>

@endsection