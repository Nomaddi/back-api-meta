<?php

use App\Http\Controllers\CustomFieldController;
use App\Models\Reporte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ClocalController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NumerosController;
use App\Http\Controllers\PermisoController;

use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ErrorLogController;
use App\Http\Controllers\AplicacionesController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ProgramadosControllers;
use App\Http\Controllers\AIController;
use App\Http\Controllers\OpenAIController;

Route::resource(
    'aplicaciones',
    AplicacionesController::class,
)->names('aplicaciones');

//obteniendo numeros telefonicos
Route::get('numbers', [AplicacionesController::class, 'numbers'])->middleware('can:numbers')->name('numbers');

//numeros
Route::resource(
    'numeros',
    NumerosController::class,
)->names('numeros');

//Contactos
Route::get('contactos', [ContactoController::class, 'index'])->middleware('can:contactos.index')->name('contactos.index');
//mostrar todos los registros
Route::get('contactos/getData', [ContactoController::class, 'getData'])->name('contactos.getData');
Route::post('contactos', [ContactoController::class, 'store'])->name('contactos.store'); //crear un registro
Route::get('contactos/edit/{id}', [ContactoController::class, 'edit']); //obtener datos para editar un registro
Route::put('contactos/update', [ContactoController::class, 'update'])->name('contactos.update'); //actualizare un registro
Route::delete('contactos/{id}', [ContactoController::class, 'destroy'])->name('contactos.destroy'); //borra un registro

// Tags
Route::get('tags', [TagController::class, 'index'])->middleware('can:tags.index')->name('tags.index'); //mostrar todos los registros
Route::post('tags', [TagController::class, 'store'])->name('tags.store'); //crear un registro
Route::put('tags/{id}', [TagController::class, 'update']); //actualizare un registro
Route::delete('tags/{id}', [TagController::class, 'destroy']); //actualizare un registro
Route::get('tags/{id}/contactos', [TagController::class, 'showContacts'])->name('tags.showContacts');

// Users
Route::get('users', [UserController::class, 'index'])->middleware('can:users.index')->name('users.index'); //mostrar todos los registros
Route::post('users', [UserController::class, 'store'])->name('users.store'); //crear un registro
Route::put('users/{id}', [UserController::class, 'update']); //actualizare un registro
Route::delete('users/{id}', [UserController::class, 'destroy']); //actualizare un registro


//roles
Route::get('roles', [RolesController::class, 'index'])->middleware('can:roles.index')->name('roles.index'); //mostrar todos los registros
Route::post('roles', [RolesController::class, 'store'])->name('roles.store'); //crear un registro
Route::put('roles/{id}', [RolesController::class, 'update']); //actualizare un registro
Route::delete('roles/{id}', [RolesController::class, 'destroy']); //actualizare un registro

//permisos
Route::get('permisos', [PermisoController::class, 'index'])->middleware('can:permisos.index')->name('permisos.index'); //mostrar todos los registros
Route::post('permisos', [PermisoController::class, 'store'])->middleware('can:permisos.store')->name('permisos.store'); //crear un registro
Route::put('permisos/{id}', [PermisoController::class, 'update'])->middleware('can:permisos.update')->name('permisos.update'); //actualizare un registro
Route::delete('permisos/{id}', [PermisoController::class, 'destroy'])->middleware('can:permisos.delete')->name('permisos.delete'); //actualizare un registro


// importar contactos
Route::post('upload-contactos', [ContactoController::class, 'uploadUsers'])->middleware('can:importar-contactos')->name('importar-contactos');
// exporta contactos
Route::get('exportar-contactos', [ContactoController::class, 'exportar'])->name('exportar-contactos');


// enviar mensaje plantilla
Route::get('plantillas', [MessageController::class, 'NumbersApps'])->middleware('can:plantillas')->name('plantillas');

Route::get('send-message', [MessageController::class, 'sendMessages']);
Route::get('message-templates', [MessageController::class, 'loadMessageTemplates'])->name('message.templates');
Route::post('send-message-templates', [MessageController::class, 'sendMessageTemplate'])->name('send.message.templates');
Route::post('upload-pdf', [MessageController::class, 'upload'])->middleware('can:upload.pdf')->name('upload.pdf');

//estadisticas
Route::get('estadisticas', [EstadisticasController::class, 'index'])->middleware('can:estadisticas')->name('estadisticas'); //mostrar todos los registros
Route::post('/estadisticas/get-statistics', [EstadisticasController::class, 'getStatistics'])->name('get-statistics');
Route::get('envios-plantillas', [EnvioController::class, 'index'])->middleware('can:envios-plantillas')->name('envios-plantillas'); //mostrar todos los registros
// exporta mensajes
Route::get('exportar-mensajes/{id}', [EstadisticasController::class, 'exportar'])->name('exportar-mensajes');

//programados
Route::get('programados', [ProgramadosControllers::class, 'index'])->middleware('can:programados')->name('programados'); //mostrar todos los registros
Route::get('/descargar-archivo/{id}', [ProgramadosControllers::class, 'descargar'])->name('descargar-archivo');
Route::put('/actualizar-estado/{id}', [ProgramadosControllers::class, 'actualizarEstado'])->name('actualizar-estado');

//contratacion local
Route::get('solicitudes', [ClocalController::class, 'index'])->middleware('can:solicitudes')->name('solicitudes'); //mostrar todos los registroscl
Route::get('solicitudes/send/{id}', [ClocalController::class, 'send'])->name('enviar.solicitud'); //mostrar todos los registroscl
//Cambiar los estados de la tabla de contratacion local
Route::post('update-status', [ClocalController::class, 'updateStatus'])->name('update.status');


//descargar informe
Route::get('download/{id}', function ($id) {
    $reporte = Reporte::findOrFail($id);
    $filePath = storage_path('app/' . $reporte->archivo);

    if (file_exists($filePath)) {
        return response()->download($filePath);
    } else {
        return response()->json(['error' => 'Archivo no encontrado.'], 404);
    }
})->name('download');

// Obtener datos para reportes
Route::get('data', function () {
    try {
        $results = DB::select('CALL GetMessagesReport(?, ?)', ['2024-03-29', '2024-04-15']);
        if (!empty($results)) {
            // Puedes personalizar la salida según tus necesidades
            return response()->json($results);
        } else {
            return response()->json(['message' => 'No se encontraron resultados.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    }
})->name('data');

// Recursos adicionales
Route::resource('messages', MessageController::class);
Route::resource('custom_fields', CustomFieldController::class);

// Chat de mensajes
Route::get('messages-index', [MessageController::class, 'chat'])->name('admin.chat'); //mostrar todos los registroscl

//envios de errores
Route::post('log-client-error', [ErrorLogController::class, 'store'])->middleware('can:log-client-error')->name('log-client-error');

//verificar si no esta inactiva la sesion del usuario


//actualizando el token si este vencio
Route::get('refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->middleware('can:refresh-csrf')->name('refresh-csrf');

// Descargar plantilla
Route::get('descargar-plantilla', [ContactoController::class, 'descargarPlantilla'])->name('descargar-plantilla');

// cargar los archivos
Route::resource('/ais', AIController::class)->names('ais');


//openai
Route::get('/openai', [OpenAIController::class, 'index'])->name('openai.index');
Route::post('/openai-response', [OpenAIController::class, 'getResponse'])->name('openai.response');

Route::get('/openai/csv', [OpenAIController::class, 'uploadCsv'])->name('openai.csv');
Route::post('/openai/csv', [OpenAIController::class, 'uploadCsv']);

Route::get('/openai/pdf', [OpenAIController::class, 'uploadPdf'])->name('openai.pdf');
Route::post('/openai/pdf', [OpenAIController::class, 'uploadPdf']);

Route::get('/openai/chat', [OpenAIController::class, 'chat'])->name('openai.chat');
Route::post('/openai/chat', [OpenAIController::class, 'chat']);

