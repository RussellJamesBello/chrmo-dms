@extends('layouts.authenticated')

@section('sub_content')

<div style="overflow: auto;">
	<a href="{{ url('users') }}" class="ui grey right floated mini button">Back</a>
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
				value="{{ !old('name') ? $user->name : old('name') }}"
				:dynamic-attribs="{maxlength: 80, required: true}">
			</input-field>

			<input-field
				class="{!! $checker->check('username') !!}
				label="Username"
				type="text"
				name="username"
				value="{{ !old('username') ? $user->username : old('username') }}"
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
				hidden-text="Edit User">
			</animated-button>
		</div>
	</form>
</div>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/edit_user/options.js') }}"></script>
@endsection

@section('sub_custom_js')

<script>
	$('.field').popup();
</script>

@endsection