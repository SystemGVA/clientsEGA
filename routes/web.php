<?php

use Illuminate\Support\Facades\Route;

Route::get('/Comprobante-Pago/{tipo}/{id}', [App\Http\Controllers\ComprobanteController::class, "getPdf"]);
Route::post('login', [App\Http\Controllers\AutenticacionController::class, 'login']);
Route::get('/me', function () {
    return auth('cliente')->user();
})->middleware('auth:cliente');
Route::post('/logout', [App\Http\Controllers\AutenticacionController::class, 'logoutCliente'])->middleware('auth:cliente');


Route::prefix('/api')->middleware('auth:cliente')->group(function () {
    Route::post('subject', [App\Http\Controllers\CasosController::class, 'ALL_CONCEPTS']);
    Route::post('/mycases', [App\Http\Controllers\CasosController::class, 'ALL_CASES_CLIENTE']);
    
    Route::prefix('/encargo')->group(function () {
        Route::get('/key/{id}', [App\Http\Controllers\CasosController::class, 'GET_INFORMACION_ENCARGO']);
        Route::post('/activities', [App\Http\Controllers\CasosController::class, 'GET_ACTIVITY_ENCARGO']);
        Route::post('/archives', [App\Http\Controllers\CasosController::class, 'GET_ARCHIVE_ENCARGO']);
    });
    Route::prefix('/expediente')->group(function () {
        Route::get('/key/{id}', [App\Http\Controllers\CasosController::class, 'GET_INFORMACION_EXPEDIENTE']);
        Route::get('/individuales/{expe_id}', [App\Http\Controllers\CasosController::class, 'GET_EXPEDIENTE_INDIVIDUALES']);
        Route::post('/activities', [App\Http\Controllers\CasosController::class, 'GET_ACTIVITY_EXPEDIENTE']);
        Route::post('/archives', [App\Http\Controllers\CasosController::class, 'GET_ARCHIVE_EXPEDIENTE']);
    });
});

Route::get("/{any}", function () {
    return view('app');
})->where('any', '.*');
