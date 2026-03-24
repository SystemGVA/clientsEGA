<?php

namespace App\Models\Casos\Encargo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadModel extends Model
{
    public const ESTADO_ACTIVO      = 1;
    public const ESTADO_ELIMINADA   = 0;

    public const NIVEL_MENOR        = 2;
    public const NIVEL_MAYOR        = 4;
    public const NIVEL_SEGUIMIENTO  = 1;
    public const NIVEL_REPORTE_REQ  = 3;
    public const NIVEL_EVENTO       = 5;

    public const TIPO_POGRAMADA = 5;
    public const TIPO_EJECUTADA = 1;

    public const PENDIENTE      = 0;
    public const REALIZADA      = 1;
    public const NO_REALIZADA   = 2;
    public const FRUSTRADA      = 3;

    protected $appends = ['estado_texto'];

    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_encargos.tb_actividad';

    protected $primaryKey = 'acti_id';

    protected $fillable = [
        "acti_id",
        "acti_descripcion",
        "acti_valor",
        "acti_tipo",
        "acti_fecha",
        "enca_id",
        "usua_id",
        "acti_estado",
        "acti_fechauser",
        "acti_fechalimite",
        "acti_cumplimiento",
        "acti_sustento",
        "acti_hora_inicio",
        "acti_hora_fin",
        "acti_ubicacion",
        "acti_isurl",
        "asignado_a",
        "acti_fecha_realizado",
        "acti_nivel",
        "acti_es_evento",
        "acti_fecha_last_reprog",
        "actividad_superior",
    ];

    public function getEstadoTextoAttribute()
    {
        return $this->acti_estado == ActividadModel::ESTADO_ACTIVO ? 'ACTIVO' : 'ELIMINADO';
    }

    public function getPeriodoAttribute()
    {
        return \Carbon\Carbon::parse($this->acti_fechauser)->format('d/m/Y') . " AL " . \Carbon\Carbon::parse($this->acti_fechalimite)->format('d/m/Y');
    }




   /*  public function relacion_archivos()
    {
        return $this->hasMany(AnexoModel::class, 'acti_id')->where('anac_estado', 1);
    } */
}
