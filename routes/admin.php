<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NumerosController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\AplicacionesController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ProgramadosControllers;



Route::resource(
    'aplicaciones',
    AplicacionesController::class,
);

//obteniendo numeros telefonicos
Route::get('numbers', [AplicacionesController::class, 'numbers']);

//numeros
Route::resource(
    'numeros',
    NumerosController::class,
);

//Contactos
Route::get('contactos', [ContactoController::class, 'index'])->name('contactos.index');; //mostrar todos los registros
Route::get('contactos/getData', [ContactoController::class, 'getData'])->name('contactos.getData');
Route::post('contactos', [ContactoController::class, 'store'])->name('contactos.store'); //crear un registro
Route::get('contactos/edit/{id}', [ContactoController::class, 'edit']); //obtener datos para editar un registro
Route::post('contactos/update', [ContactoController::class, 'update'])->name('contactos.update'); //actualizare un registro
Route::get('contactos/delete/{id}', [ContactoController::class, 'destroy'])->name('contactos.delete'); //actualizare un registro

// Tags
Route::get('tags', [TagController::class, 'index']); //mostrar todos los registros
Route::post('tags', [TagController::class, 'store'])->name('tags.store'); //crear un registro
Route::put('tags/{id}', [TagController::class, 'update']); //actualizare un registro
Route::delete('tags/{id}', [TagController::class, 'destroy']); //actualizare un registro

// importar contactos
Route::post('upload-contactos', [ContactoController::class, 'uploadUsers'])->name('importar-contactos');

// enviar mensaje plantilla
Route::get('plantillas', [MessageController::class, 'NumbersApps']);

Route::get('send-message', [MessageController::class, 'sendMessages']);
Route::get('message-templates', [MessageController::class, 'loadMessageTemplates']);
Route::post('send-message-templates', [MessageController::class, 'sendMessageTemplate']);

//estadisticas
Route::get('estadisticas', [EstadisticasController::class, 'index']); //mostrar todos los registros
Route::post('/estadisticas/get-statistics', [EstadisticasController::class, 'getStatistics'])->name('get-statistics');
Route::get('envios-plantillas', [EnvioController::class, 'index']); //mostrar todos los registros

//programados
Route::get('programados', [ProgramadosControllers::class, 'index']); //mostrar todos los registros
Route::get('/descargar-archivo/{id}', [ProgramadosControllers::class, 'descargar'])->name('descargar-archivo');
Route::put('/actualizar-estado/{id}', [ProgramadosControllers::class, 'actualizarEstado'])->name('actualizar-estado');

Route::resource(
    'messages',
    MessageController::class
);

