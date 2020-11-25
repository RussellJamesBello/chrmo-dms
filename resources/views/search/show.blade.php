@extends('layouts.authenticated')

@section('sub_content')

<div class="ui top attached tabular menu">
	<a class="active item" data-tab="first">Employees</a>
	<a class="item" data-tab="second">Documents</a>
</div>
<div class="ui bottom attached active tab segment" data-tab="first" style="margin-bottom: 0;">
	<div class="ui very relaxed divided list">
		@foreach($employees as $employee)
			<div class="item">
				<i class="large user outline middle aligned icon"></i>
				<div class="content">
					<a href="{{ route('employee', ['employee' => $employee->employee_id]) }}" class="header">{{ $employee->getWholeName() }}</a>
					<div class="description">{{ $employee->getOffice()->name }}</div>
				</div>
			</div>
		@endforeach
	</div>
</div>
<div class="ui bottom attached tab segment" data-tab="second">
	<div class="ui very relaxed divided list">
		@foreach($documents as $document)
			@php
				$tags = explode(',', $document->keywords);
			@endphp

			<div class="item">
				<i class="large file outline middle aligned icon"></i>
				<div class="content">
					<a href="{{ route('document', ['document' => $document->document_id]) }}" class="header">{{ $document->name }}</a>
					<div class="description" style="margin-bottom: 5px;">Owner:<b style="margin-left: .8em;">{{ $document->employee->getWholeName() }}</b></div>
					<div class="description">
						<span style="margin-right: 1.5em;">Tags:</span>
						@foreach($tags as $tag)
							<a class="ui small blue label">{{ $tag }}</a>
						@endforeach
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>

@endsection

@section('sub_custom_js')

<script>
	$('.menu .item').tab();
</script>

@endsection