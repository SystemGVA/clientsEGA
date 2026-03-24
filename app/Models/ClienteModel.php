<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ClienteModel extends Authenticatable
{
    use HasFactory;

    public const DNI             = '1';
    public const RUC             = '6';
    public const EXTRANJERIA     = '4';
    public const PASAPORTE       = '7';
    public const CEDULA          = 'A';
    public const NO_DOMICILIADO  = '0';


    public $timestamps = false;

    protected $table = 'sc_seguridad.tb_cliente';

    protected $appends = [
        "nombre_cliente",
        "clie_icon",
        "tipo_doc_texto",
    ];

    protected $primaryKey = 'clie_id';

    protected $fillable = [
        "clie_id",
        "clie_cargo",
        "clie_origen",
        "clie_fecha",
        "clie_escliente",
        "clie_tipo",
        "clie_tipo_doc",
        "clie_abbr",
        "clie_estado",
        "pers_id",
        "grcl_id",
        "cliente_mas",
        "password",
        "email",
        "clie_apelpat",
        "clie_apelmat",
        "clie_nombre",
        "clie_razonsoc",
        "clie_is_delete",
        "clie_doc"
    ];

     protected $hidden = [
        'password'
    ];

    public function getClieIconAttribute()
    {
        if (in_array($this->clie_tipo_doc, [self::RUC, self::NO_DOMICILIADO])) {
            return [
                'icon' => 'mdi-domain',
                'color' => 'blue darken-4'
            ];
        }

        return [
            'icon' => 'mdi-account-tie',
            'color' => 'deep-purple lighten-3',
        ];
    }
    public function getTipoDocTextoAttribute()
    {
        switch ($this->clie_tipo_doc) {
            case self::DNI:
                return "DNI";
            case self::RUC:
                return "RUC";
            case self::EXTRANJERIA:
                return "Carné de Extranjería";
            case self::PASAPORTE:
                return "Pasaporte";
            case self::CEDULA:
                return "Cédula Diplomática";
            case self::NO_DOMICILIADO:
                return "Doc. no domiciliado";
            default:
                return "Desconocido";
        }
    }

    public function getNombreClienteAttribute()
    {
        return trim($this->clie_nombre . $this->clie_razonsoc . " " . $this->clie_apelpat . " " . $this->clie_apelmat);
    }
}
