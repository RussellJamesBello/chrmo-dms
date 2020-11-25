@extends('layouts.authenticated')

@section('sub_content')

<a href="{{ url('employees') }}" class="ui grey right floated mini button">Back</a>
<a href="{{ url("employees/{$employee->employee_id}/edit") }}" class="ui yellow right floated mini button">Edit</a>
@role('administrator')
	<a @click="changeCurrentRemove('{{ url("employees/$employee->employee_id") }}', '{{ $employee->getWholeName() }}')" href="#" class="ui red right floated mini button">
		Delete
	</a>
@endrole

<br>

<info-divider>Employee</info-divider>

<div class="ui stackable centered grid">
	<header-data-view class="eleven wide">
		Name:
		<template slot="value">
			{{ $employee->getWholeName() }}
		</template>
	</header-data-view>

	<header-data-view class="four wide" title="{{ $office->name }}">
		Office:
		<template slot="value">
			{{ $office->name_acronym }}
		</template>
	</header-data-view>
</div>

<br>
<br>

<info-divider>Document</info-divider>

<div class="ui stackable centered grid">
	<div class="fifteen wide column">
		<div class="ui very relaxed divided list">
			@foreach($documents as $document)
				<div class="item">
					<div class="right floated content">
						<simple-dropdown class="small">
							<template slot="label">
								<i class="cogs icon"></i>
								<i class="dropdown icon"></i>
							</template>

							<template slot="items">
								<a href="{{ url("documents/$document->document_id/edit") }}" class="item">
									Edit
								</a>
								@role('administrator')
									<a @click="changeCurrentRemove('{{ url("documents/$document->document_id") }}', '{{ $document->name }}')" href="#" class="item">
										Delete
									</a>
								@endrole
							</template>
						</simple-dropdown>
					</div>

					<i class="middle aligned file icon"></i>

					<div class="content">
						<a class="header" href="{{ url("documents/{$document->document_id}") }}">
							{{ $document->name }}
						</a>

						<div class="description">
							stored at: {{ getEmployeeFolder($employee, $document->custom_folder_path, $document->name, null, false) }}
						</div>
					</div>
				</div>
			@endforeach
		</div>

		{{ $documents->links() }}
	</div>
</div>

<delete-modal
	:form-action="current_form_action"
	id="remove_modal"
	modal-title="Remove Document"
	:delete-name="current_delete_name"
	@close="reinitializeValues()">
	@csrf
	@method('DELETE')
</delete-modal>

@endsection

@section('vue_options')

<script src="{{ mix('/js/employee_info/options.js') }}"></script>

@endsection