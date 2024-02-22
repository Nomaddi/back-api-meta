<?php

use App\Events\Webhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\AplicacionesController;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/whatsapp-webhook', [MessageController::class, 'verifyWebhook']);
Route::post('/whatsapp-webhook', [MessageController::class, 'processWebhook']);
// Agrupando rutas y aplicando el middleware 'auth'
Route::middleware(['auth'])->group(function () {


    //estadisticas

    Route::get('/statistics', [MessageController::class, 'getStatistics']);



    //import
// Route::get('/import-users', [ContactoController::class, 'importUsers'])->name('import');



});
