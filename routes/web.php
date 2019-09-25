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
//    echo phpversion();
//
//    $user = new \App\User();
//
//    $user->password = \Illuminate\Support\Facades\Hash::make('asdfasdfadsfasd');
//    $user->name = 'test';
//    $user->email = 'tlajiosdijfest@dafkasdfa.df';
//
//    $user->save();
});
