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
		<div class="ui vertically divided very compact grid">
			<div class="row">
				<div class="five wide column">
					<h5 class="ui header" style="position: absolute; top: 50%; transform: translateY(-50%);">
						@if(request()->input('search_document'))
							Search results for: {{ request()->input('search_document') }}
						@else
							{{ $documents->count() }} Total Documents
						@endif
					</h5>
				</div>

				<div class="five wide column"></div>

				<div class="six wide column">
					@if(request()->input('search_document'))
						<a class="ui small basic fluid blue button" href="{{ url()->current() }}">Remove Search Document Results</a>
					@else
						<form class="ui form" method="GET" action="{{ url()->current() }}">
							<input-field
								type="text"
								label="Search Document"
								name="search_document"
								placeholder="Type a document name or keyword and press Enter">
							</input-field>
						</form>
					@endif
				</div>
			</div>

			<div class="row">
				
			</div>
		</div>
	</div>

	<div class="fifteen wide column">
		<div class="ui very relaxed divided selection list">
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

					<div class="content" onclick="location.href='{{ url("documents/{$document->document_id}") }}'">
						<a class="header" href="{{ url("documents/{$document->document_id}") }}">
							{{ $document->name }}
						</a>

						<div class="description">
							stored at: {{ getEmployeeFolder($employee, $document->custom_folder_path, $document->name, null, false) }}
							<br>
							@php
								$tags = explode(',', $document->keywords)
							@endphp

							@foreach($tags as $tag)
								<a class="ui small primary basic label">{{ $tag }}</a>
							@endforeach
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

	@if(!request()->input('search_document'))
		<div class="center aligned five wide column">
			{{ $documents->links() }}
		</div>
	@endif
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