<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'MainController@index');
Route::get('todo', 'MainController@todo');
Route::get('create', 'MainController@create');
Route::post('', 'MainController@store');
Route::get('post/{id}', 'MainController@show');
Route::get('post/{id}/edit', 'MainController@edit');
Route::patch('post/{id}', 'MainController@update');

//Route::resource('', 'MainController');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);