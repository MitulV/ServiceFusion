<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


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
    
    //  if(Carbon\Carbon::parse('2020-08-25T00:00:00+00:00')->gt('2020-10-01T00:00:00+00:00')){
    //     return "yes";
    // }else{
    //     return "no";
    // }
    return view('welcome');
});

Route::get('/customers',[HomeController::class,'getCustomers']);
