@extends('layouts.authenticated')

@section('sub_content')

@php

	if($errors->isNotEmpty() || session('success')['header'] != null)
	{
		$first_tab = '';
		$second_tab = 'active';
	}
	else
	{
		$first_tab = 'active';
		$second_tab = '';
	}

@endphp

<div class="ui top attached tabular menu">
	<a class="item {{ $first_tab }}" data-tab="first">User List</a>
	<a class="item {{ $second_tab }}" data-tab="second">Add User</a>
</div>

<div class="ui bottom attached tab segment {{ $first_tab }}" data-tab="first" style="margin-bottom: 0;">
	<generic-table class="blue celled compact selectable striped center aligned">
		<template slot="head">
			<tr>
				<th>Name</th>
				<th>User Name</th>
				<th>Options</th>
			</tr>
		</template>

		<template slot="body">
			@foreach($users as $user)
				<tr>
					<td>{{ $user->name }}</td>
					<td>{{ $user->username }}</td>
					<td class="collapsing">
						<simple-dropdown class="small">
							<template slot="label">
								<i class="cogs icon"></i>
								<i class="dropdown icon"></i>
							</template>

							<template slot="items">
								<a href="{{ url("users/$user->user_id/edit") }}" class="item">
									Edit
								</a>
								<a href="#" onclick="event.preventDefault(); document.getElementById('remove_form_{{ $loop->index }}').submit();" class="item">
									Remove
								</a>

				    			<form id="remove_form_{{ $loop->index }}" action="{{ url("users/$user->user_id") }}" method="POST" style="display: none;">
				        			@csrf
				        			@method('DELETE')
				    			</form>
							</template>
						</simple-dropdown>
					</td>
				</tr>
			@endforeach
		</template>
	</generic-table>
</div>

<div class="ui bottom attached tab segment {{ $second_tab }}" data-tab="second">
	<div class="ui stackable centered grid">
		<form class="ui seven wide column center aligned form {{ $errors->any() ? 'error' : 'success' }}" method="POST" action="{{ url()->current() }}">
			@method('POST')
			@csrf

			@include('commons.success_message')

			<div class="ui raised segment">
				<input-field
					class="{!! $checker->check('name') !!}
					label="Name"
					type="text"
					name="name"
					value="{{ old('name') }}"
					:dynamic-attribs="{maxlength: 80, required: true}">
				</input-field>

				<input-field
					class="{!! $checker->check('username') !!}
					label="Username"
					type="text"
					name="username"
					value="{{ old('username') }}"
					:dynamic-attribs="{maxlength: 15, required: true}">
				</input-field>

				<input-field
					class="{!! $checker->check('password') !!}
					label="Password"
					type="password"
					name="password"
					:dynamic-attribs="{maxlength: 20, required: true}">
				</input-field>

				<input-field
					class="{!! $checker->check('password') !!}
					label="Password Confirmation"
					type="password"
					name="password_confirmation"
					:dynamic-attribs="{maxlength: 20, required: true}">
				</input-field>

				<animated-button
					type="submit"
					class="fluid basic blue fade"
					visible-icon="user"
					hidden-text="Add User">
				</animated-button>
			</div>
		</form>
	</div>
</div>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/user_dashboard/options.js') }}"></script>
@endsection

@section('sub_custom_js')

<script>
	$('.menu .item').tab();
	$('.field').popup();
</script>

@endsection