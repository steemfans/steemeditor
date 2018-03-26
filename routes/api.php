<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('material', 'Api\MaterialController@create')->name('material_create');
Route::get('materials', 'Api\MaterialController@index')->name('material_list');
Route::get('material/detail', 'Api\MaterialController@detail')->name('material_detail');
Route::post('material/remove', 'Api\MaterialController@remove')->name('material_remove');
Route::post('material/update', 'Api\MaterialController@update')->name('material_update');
Route::get('material/tags', 'Api\MaterialController@tags')->name('material_tags');
