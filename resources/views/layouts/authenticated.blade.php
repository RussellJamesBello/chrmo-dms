<?php /*
	This file extends the main.blade.php blade file. This is used by pages that are authenticated.
	For child views that extend this file and are specifying their own css, unlike before, it is no longer needed
	to section the custom_css that is being yielded in main.blade.php. 
	Unless authenticated.blade.php sections a yielded section in main.blade.php,the child view can directly section
	the yielded section in main.blade.php. Such example is that, here, it does not section custom_css, so child views
	extending this file and that needs to specify their own css may directly section custom_css from main.blade.php.
	On the other hand, it sections content and custom_js from main.blade.php with their definitions specific to this file. 
	So if the child view extending this file needs to define its content or js, it must go through 
	authenticated.blade.php's sub_content or sub_custom_js and then it will flow through main.blade.php's yielded
	content or custom_js.
*/ ?>

@extends('layouts.main')

@section('content')
	@php
		$current_url = request()->path();
	@endphp
	
	<div id="main_menu" class="ui small blue inverted top fixed stackable menu">
		<div class="ui container">
			<h4 id="menu_brand" class="header item">
				<img src="/images/lc_logo.png">
				CHRMO - DMS
			</h4>

			<a class="item {{ $current_url != '/' ?: 'active' }}" href="{{ url('/') }}">
				<i class="add square icon"></i>
				Add A Document
			</a>

			<a class="item {{ $current_url != '/employees' ?: 'active' }}" href="{{ url('/employees') }}">
				<i class="list icon"></i>
				Employee List
			</a>

			<div class="right menu">
				<form method="GET" action="{{ url('search') }}" id="search_form" class="item">
					<div class="ui action input">
						<input type="text" name="search" placeholder="Search...">
						<button class="ui icon button">
    						<i class="search icon"></i>
  						</button>
					</div>
				</form>

				@role('administrator')
					<div id="admin_dropdown" class="ui right dropdown item">
						<i class="user secret icon"></i>
						<span>Admin Tools <i class="dropdown icon"></i></span>

						<div class="menu">
							<a class="item" href="{{ url('users') }}">
								User Administration
							</a>

							<a class="item" href="{{ url('offices') }}">
								Office Administration
							</a>
						</div>
					</div>
				@endrole
    
    			<a class="item"  href="#" onclick="event.preventDefault(); document.getElementById('logout_form').submit();">
    				<i class="sign out icon"></i>
    				Sign Out
    			</a>

    			<form id="logout_form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        			{{ csrf_field() }}
    			</form>
  			</div>
		</div>
	</div>

	<div id="main_container" class="ui grid container">
		<div id="main_content" class="sixteen wide column ui raised segment">
			<h3 id="main_header" class="ui top attached header blue center aligned">
				<u>{{ $title }}</u>
			</h3>

			<div id="specific_content" class="ui attached stacked segment">
				@yield('sub_content')
			</div>
		</div>
	</div>

	<div id="footer" class="ui stackable bottom fixed inverted small menu">
		<div class="item">
			<i class="copyright icon"></i>
			Copyright {{ date('Y') }}. All rights reserved.
		</div>

		<div class="right menu">
			<a class="item">
				<i class="user icon"></i>
				You are logged in as {{ Auth::user()->username }}.
			</a>

			<a id="about" class="item">
				<i class="help circle icon"></i>
				About
			</a>
		</div>
	</div>
@endsection

@section('custom_js')
	@yield('sub_custom_js')

	<script src="{{ mix('/js/authenticated.js') }}"></script>

	<div id="about_modal" class="ui tiny modal text_center" style="text-align: center;">
		<h2 class="ui icon header">
			<i class="question circle outline icon"></i>
			<div class="content">
				About the Developer
			</div>
		</h2>
		<div class="content">
			<h3>
				<i>
					The <u>City Human Resource Management Office - Document Management System</u> (CHRMO-DMS) is proudly<br> 
					<i class="blue code icon" title="Coded"></i> with all the 
					<i class="red heart icon" title="love"></i> in the 
					<i class="green world icon" title="world"></i> by the 
					<i class="spy icon" title="developer (Russell James Funtila Bello)"></i>.
				</i>
			</h3>
			<h4>You can contact me through the following:</h4>
			<a href="https://www.facebook.com/russelljames.bello" target="_blank"><i class="big blue facebook icon"></i> /russelljames.bello</a>
			<br><br>
			<a href="#"><i class="big red mail icon"></i> russellbello24@gmail.com</a>
		</div>
		<div class="actions">
			<div class="ui grey deny button">
				Close
			</div>
		</div>
	</div>
@endsection