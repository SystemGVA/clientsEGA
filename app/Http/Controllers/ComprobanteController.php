<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ComprobanteController extends Controller
{
    public function getPdf(Request $request, $tipo, $id)
    {
        $url = "https://acsyso.gamarrafirma.com/apiSunat/Comprobante/$tipo-$id";

        $url = Http::get($url)->json();

        header('Content-Description: File Transfer');
        header("Content-type: application/pdf");
        readfile($url);

    }
}
