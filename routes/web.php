<?php

use Illuminate\Support\Facades\Route;

Route::get('/Comprobante-Pago/{tipo}/{id}', [App\Http\Controllers\ComprobanteController::class, "getPdf"]);
Route::get("/{any}", function () {
    return view('app');
})->where('any', '.*');



