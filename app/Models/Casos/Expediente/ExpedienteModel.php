<?php

namespace App\Models\Casos\Expediente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ExpedienteModel extends Model
{
    use HasFactory;


    public const ESTADO_PROCESO     = 0;
    public const ESTADO_ELIMINADO   = 1;
    public const ESTADO_ARCHIVADO   = 2;
    public const ESTADO_CULMINADO   = 3;

    public $timestamps = false;

    protected $table = 'sc_expedientes.tb_expediente';

    protected $primaryKey = 'expe_id';
    protected $appends = ['estado_texto', 'expediente', 'encrypted_id'];

    protected $fillable = [
        "expe_id",
        "expe_fechacrea",
        "expe_estado",
        "usua_id",
        "expe_fecha",
        "expe_tipocliente", //
        "expe_tipopartec", //
        "expe_pretenmonto", //
        "expe_tmateria",
        "expe_numero",
        "expe_tproceso",
        "expe_tjuzgado",
        "expe_tcarpeta",
        "expe_fiscalia",
        "enca_id",
        "expe_partecontra",
        "expe_pretension", //
        "expe_cliente", //
        "expe_periodo",
        "expe_anioc",
        "expe_tipo",
        "expe_entidad",
        "expe_codigo_jud_fis",
        "expe_num_carpetafis",
        "expe_fecha_vence",
        "expe_dependencia",
        "expe_instruccion",
        "expe_titulo",
        "is_important",
        "is_detenido",
        "motivo_detenido",
        "expe_correlativo",
        "estado_interno",
        "expe_domicilio",
        "expe_fecha_ultimo_movimiento",
        "expe_qr",
    ];


    protected $attributes = [
        'expe_estado' => 0,
    ];

    public function getEstadoTextoAttribute()
    {
        switch ($this->enca_estado) {
            case ExpedienteModel::ESTADO_PROCESO:
                return "PROCESO";
                break;
            case ExpedienteModel::ESTADO_CULMINADO:
                return "CULIMINADO";
                break;
            case ExpedienteModel::ESTADO_ARCHIVADO:
                return "ARCHIVADO";
                break;
            case ExpedienteModel::ESTADO_ELIMINADO:
                return "ELIMINADO";
                break;
            default:
                return "DEFAULT";
                break;
        }
    }
    public function getExpedienteAttribute()
    {
        return "expediente";
    }
    public function getEncryptedIdAttribute()
    {
        if (isset($this->attributes['expe_id'])) {
            return Crypt::encryptString($this->attributes['expe_id']);
        }
        return null;
    }


    /*  public function relacion_materia()
    {
        return $this->belongsTo(ConceptoModel::class, 'expe_tmateria');
    }
    public function relacion_proceso()
    {
        return $this->belongsTo(ConceptoModel::class, 'expe_tproceso');
    } */
}
