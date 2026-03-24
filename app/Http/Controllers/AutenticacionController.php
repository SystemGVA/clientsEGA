<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\PersonalAccessToken;
use App\Mail\OtpMail;
use App\Models\ClienteModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AutenticacionController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);
        $cliente = ClienteModel::where('clie_doc', $request->usuario)->first();
        if (!$cliente) {
            return response()->json([
                "error" => "Cliente no existe"
            ], 401);
        }
        if ($cliente->clie_is_delete == 1) {
            return response()->json([
                "error" => "Cliente eliminado"
            ], 403);
        }
        if ($cliente->clie_estado == 1) {
            return response()->json([
                "error" => "Cliente inactivo"
            ], 403);
        }
        if (!Auth::guard('cliente')->attempt([
            'clie_doc' => $request->usuario,
            'password' => $request->password
        ])) {
            return response()->json([
                "error" => "Credenciales incorrectas"
            ], 401);
        }
        return response()->json([
            "ok" => true,
            "cliente" => Auth::guard('cliente')->user()
        ]);
    }

    public function logoutCliente()
    {
        Auth::guard('cliente')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return response()->json([
            "ok" => true
        ]);
    }
}
