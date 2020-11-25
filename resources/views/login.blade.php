@extends('layouts.main')

@section('content')

<div id="login_content" class="ui piled raised padded text container segment center aligned">
	<h2 class="ui icon header">
		<div class="content">
			City Human Resource Management Office - Document Management System
		</div>
	</h2>

	<div class="ui divider"></div>

	<form id="specific_content" action="{{ url()->current() }}" method="POST" class="ui form {{ $errors->any() ? 'error' : 'success' }}">
		{{ csrf_field() }}

		<input-field
			class="ten block_center{!! $checker->check($errors->has('compound_error') ? 'compound_error' : 'username') !!}
			label="Username"
			name="username"
			type="text"
			value="{{ old('username') }}"
			placeholder="Username">
		</input-field>
  
		<input-field
			class="ten block_center{!! $checker->check($errors->has('compound_error') ? 'compound_error' : 'password') !!}
			label="Password"
			name="password"
			type="password"
			value="{{ old('password') }}"
			placeholder="Password">
		</input-field>

		<br>

		<div class="ten wide field block_center">
			<button type="submit" class="ui fluid inverted blue button">SIGN IN</button>
		</div>
	</form>
</div>

@endsection

@section('vue_options')
<script src="{{ mix('/js/login/options.js') }}"></script>
@endsection

@section('custom_js')
<script>
	$('.field').popup();
</script>
@endsection