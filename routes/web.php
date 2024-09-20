<?php

use App\Events\Webhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ClocalController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\AplicacionesController;
use App\Http\Controllers\AIController;

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
Route::get('/send-message', [MessageController::class, 'sendMessages']);

Route::get('/check-session', function () {
    return response()->json(['is_logged_in' => auth()->check()]);
});

// Agrupando rutas y aplicando el middleware 'auth'
Route::middleware(['auth'])->group(function () {
    Route::get('/statistics', [MessageController::class, 'getStatistics']);
});


// cargar los archivos

Route::get('/upload-pdf', [AIController::class, 'showUploadForm'])->name('pdf.upload');
Route::post('/upload-pdf', [AIController::class, 'uploadPDF'])->name('pdf.upload.post');

// pausar y reanudar la AI
Route::post('/pause-ia', [AIController::class, 'pauseIA'])->name('ia.pause');
Route::post('/resume-ia', [AIController::class, 'resumeIA'])->name('ia.resume');

//Rutas (web.php):
Route::get('/config-horarios', [AIController::class, 'showScheduleForm'])->name('ia.schedule');
Route::post('/config-horarios', [AIController::class, 'storeSchedule'])->name('ia.schedule.store');
