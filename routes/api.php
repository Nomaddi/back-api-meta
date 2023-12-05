<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\AplicacionesController;
use App\Http\Controllers\NumerosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/send-message', [MessageController::class, 'sendMessages']);
Route::get('/whatsapp-webhook', [MessageController::class, 'verifyWebhook']);
Route::post('/whatsapp-webhook', [MessageController::class, 'processWebhook']);
Route::apiResources([
    'messages' => MessageController::class,
]);
Route::get('/message-templates', [MessageController::class, 'loadMessageTemplates']);
Route::post('/send-message-templates', [MessageController::class, 'sendMessageTemplate']);

//estadisticas

Route::get('/statistics', [MessageController::class, 'getStatistics']);

//Contactos
Route::get('/contactos', 'App\Http\Controllers\ContactoController@index');//mostrar todos los registros
Route::post('/contactos', 'App\Http\Controllers\ContactoController@store');//crear un registro
Route::put('/contactos/{id}', 'App\Http\Controllers\ContactoController@update');//actualizare un registro
Route::delete('/contactos/{id}', 'App\Http\Controllers\ContactoController@destroy');//actualizare un registro

// Tags
Route::get('/tags', 'App\Http\Controllers\TagController@index');//mostrar todos los registros
Route::post('/tags', 'App\Http\Controllers\TagController@store');//crear un registro
Route::put('/tags/{id}', 'App\Http\Controllers\TagController@update');//actualizare un registro
Route::delete('/tags/{id}', 'App\Http\Controllers\TagController@destroy');//actualizare un registro

//import
// Route::get('/import-users', [ContactoController::class, 'importUsers'])->name('import');
Route::post('/upload-contactos', [ContactoController::class, 'uploadUsers']);

Route::apiResources([
    'aplicaciones' => AplicacionesController::class,
]);
//obteniendo numeros telefonicos
Route::get('/numbers', [AplicacionesController::class, 'numbers']);

Route::apiResources([
    'numeros' => NumerosController::class,
]);