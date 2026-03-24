<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;

class HelpersController extends Controller
{

    //ENCRIPTADOR
    public static function getDecryptedId($encryptedId)
    {
        try {
            $decryptedId = Crypt::decryptString($encryptedId);
            return $decryptedId;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return null;
        }
    }
    public static function getEncryptedId($Id)
    {
        try {
            $encryptedId = Crypt::encryptString($Id);

            return $encryptedId;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return null;
        }
    }


 
}
