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
use App\Http\Controllers\ClocalController;



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
Route::get('tags', [TagController::class, 'index'])->name('tags.index'); //mostrar todos los registros
Route::post('tags', [TagController::class, 'store'])->name('tags.store'); //crear un registro
Route::put('tags/{id}', [TagController::class, 'update']); //actualizare un registro
Route::delete('tags/{id}', [TagController::class, 'destroy']); //actualizare un registro
Route::delete('tags/{id}', [TagController::class, 'destroy']); //actualizare un registro
Route::get('tags/{id}/contactos', [TagController::class, 'showContacts'])->name('tags.showContacts');



// importar contactos
Route::post('upload-contactos', [ContactoController::class, 'uploadUsers'])->name('importar-contactos');
// exporta contactos
Route::get('exportar-contactos', [ContactoController::class,'exportar'])->name('exportar-contactos');


// enviar mensaje plantilla
Route::get('plantillas', [MessageController::class, 'NumbersApps']);

Route::get('send-message', [MessageController::class, 'sendMessages']);
Route::get('message-templates', [MessageController::class, 'loadMessageTemplates'])->name('message.templates');
Route::post('send-message-templates', [MessageController::class, 'sendMessageTemplate'])->name('send.message.templates');
Route::post('upload-pdf', [MessageController::class, 'upload'])->name('upload.pdf');

//estadisticas
Route::get('estadisticas', [EstadisticasController::class, 'index']); //mostrar todos los registros
Route::post('/estadisticas/get-statistics', [EstadisticasController::class, 'getStatistics'])->name('get-statistics');
Route::get('envios-plantillas', [EnvioController::class, 'index']); //mostrar todos los registros
// exporta mensajes
Route::get('exportar-mensajes/{id}', [EstadisticasController::class,'exportar'])->name('exportar-mensajes');

//programados
Route::get('programados', [ProgramadosControllers::class, 'index']); //mostrar todos los registros
Route::get('/descargar-archivo/{id}', [ProgramadosControllers::class, 'descargar'])->name('descargar-archivo');
Route::put('/actualizar-estado/{id}', [ProgramadosControllers::class, 'actualizarEstado'])->name('actualizar-estado');

//contratacion local
Route::get('solicitudes', [ClocalController::class, 'index']); //mostrar todos los registroscl
Route::get('solicitudes/send/{id}', [ClocalController::class, 'send'])->name('enviar.solicitud'); //mostrar todos los registroscl



Route::resource(
    'messages',
    MessageController::class
);

Route::get('messages-index', [MessageController::class, 'chat']); //mostrar todos los registroscl

