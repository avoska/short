<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


$prefix = Request::segment(1);
if(!in_array($prefix, config('app.locales'))) {
	$prefix = '';
}

Route::group(array('prefix' => $prefix), function () {
	Route::get('/', 'MainController@index')->name('index');
	Route::post('new', 'MainController@create')->name('new');
	Route::get('{hash}', 'MainController@short')->name('short');
	Route::get('{hash}/stats', 'MainController@stats')->name('stats');

});