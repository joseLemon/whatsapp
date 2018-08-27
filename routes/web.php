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

Route::get('/conektaTest', 'ConektaController@test')->name('test');

Route::name('conekta.')->group(function () {
    Route::post('/createOrder', 'ConektaController@createOrder')->name('create');
});