<?php

namespace App\Models\Casos\Expediente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraExpedienteModel extends Model
{
    public const ESTADO_ACTIVO  = 1;
    public const ESTADO_ANULADO = 0;

    protected $appends = ['estado_texto','esta_dili_fecha_registro'];

    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_expedientes.tb_bitacora_expediente';

    protected $primaryKey = 'biex_id';

    protected $fillable = [
        "biex_id",
        "biex_fecha_registro",
        "biex_fecha",
        "biex_descripcion",
        "biex_estrategia",
        "biex_estado",
        "expe_indiv_id",
        "usua_id",
    ];

    public function getEstadoTextoAttribute()
    {
        return $this->biex_estado == BitacoraExpedienteModel::ESTADO_ACTIVO ? 'ACTIVO' : 'ELIMINADO' ;
    }
    public function getEstaDiliFechaRegistroAttribute()
    {
        return $this->biex_fecha." 00:00";
    }
}
