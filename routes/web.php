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

Route::get('/', 'UploadController@create')->name('index');
Route::post('/upload/store', 'UploadController@store')->name('upload-store');

Route::get('/excel/show/{id}', 'ExcelController@show')->name('excel-show');
Route::post('/excel/store', 'ExcelController@store')->name('excel-store');
Route::post('/excel/check_field', 'ExcelController@check_field');