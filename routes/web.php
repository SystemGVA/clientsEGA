<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/Comprobante-Pago/{tipo}/{id}', [App\Http\Controllers\ComprobanteController::class, "getPdf"]);

