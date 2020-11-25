<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Laravel defaults its unauthenticated response to a route with a name of 'login' so I named the route below with 'login'
Route::match(['get', 'post'], 'login', 'AuthenticationController@login')->name('login')->middleware('guest');

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', 'DocumentController@showAddDocument');
	Route::post('/add-document', 'DocumentController@addDocument'); //tried using _ instead of - in the URL but it throws 403 error when requesting through AJAX. Dunno why, at least it works when using -.
	Route::get('/documents/{document}', 'DocumentController@getDocument')->name('document');
	Route::match(['get', 'put'], 'documents/{document}/edit', 'DocumentController@editDocument');
	Route::get('documents/{document}/pages/{document_content}', 'DocumentController@getPage')->name('page');

	Route::get('/employees', 'EmployeeController@getEmployeeList');
	Route::get('/employees/{employee}', 'EmployeeController@getEmployeeInfo')->name('employee');
	Route::match(['get', 'put'], 'employees/{employee}/edit', 'EmployeeController@editEmployee');
	Route::get('employee-search', 'EmployeeController@searchEmployee');

	Route::get('tags', 'DocumentController@getTags');
	Route::get('search', 'SearchController@searchQuery');

	Route::group(['middleware' => ['role:administrator']], function(){
		Route::get('users', 'UserController@showUserManagement');
		Route::post('users', 'UserController@AddUser');
		Route::match(['get', 'put'], 'users/{user}/edit', 'UserController@EditUser');
		Route::delete('users/{user}', 'UserController@removeUser');

		Route::get('offices', 'OfficeController@showOfficeManagement');
		Route::post('offices', 'OfficeController@AddOfficeDivision');
		Route::match(['get', 'put'], 'offices/{office}/edit', 'OfficeController@editOffice');
		Route::match(['get', 'put'], 'divisions/{division}/edit', 'OfficeController@editDivision');
		Route::delete('offices/{office}', 'OfficeController@removeOffice');
		Route::delete('divisions/{division}', 'OfficeController@removeDivision');

		Route::delete('documents/{document}', 'DocumentController@removeDocument');
		Route::delete('employees/{employee}', 'EmployeeController@removeEmployee');
	});

	Route::post('logout', 'AuthenticationController@logout');
});