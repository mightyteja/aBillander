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

// Route::get('/', 'DemoPrintHomeController@index');
Route::get('dphome', 'DemoPrintHomeController@index');
Route::get('dphome/index', 'DemoPrintHomeController@index');
Route::get('dphome/samples', 'DemoPrintHomeController@samples');
Route::get('dphome/printersinfo', 'DemoPrintHomeController@printersinfo');
Route::get('DemoPrintFile', 'DemoPrintFileController@index');
Route::get('DemoPrintFileController', 'DemoPrintFileController@printFile');
Route::get('DemoPrintFilePDF', 'DemoPrintFilePDFController@index');
Route::get('DemoPrintFilePDFController', 'DemoPrintFilePDFController@printFile');
Route::get('DemoPrintCommands', 'DemoPrintCommandsController@index');
Route::get('DemoPrintCommandsController', 'DemoPrintCommandsController@printCommands');
Route::any('WebClientPrintController', 'WebClientPrintController@processRequest');