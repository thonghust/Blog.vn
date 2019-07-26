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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/', 'HomeController@index');

//Route::group(array('middleware' => 'admin'), function() {
	Route::resource('users', 'UserController');
	Route::resource('roles', 'RoleController');
	Route::resource('permissions', 'PermissionController');
//});
Route::resource('articles', 'ArticleController');

/*
Route::get('news/export', 'NewController@index')->name('news.export');
Route::resource('news', 'NewsController');
*/