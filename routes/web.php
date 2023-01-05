<?php

use App\Http\Controllers\HomeController;
use Carbon\Carbon;
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
    return (Carbon::now()->subDays(365))->lt('2022-10-01T00:00:00+00:00');
    return view('welcome');
});

Route::get('file-upload', [App\Http\Controllers\FileUploadController::class,'fileUpload'])->name('file.upload');
Route::post('file-upload', [App\Http\Controllers\FileUploadController::class,'fileUploadPost'])->name('file.upload.post');

Route::get('/customers',[HomeController::class,'getCustomers']);
