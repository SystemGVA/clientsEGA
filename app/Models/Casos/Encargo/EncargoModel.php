<?php

namespace App\Models\Casos\Encargo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncargoModel extends Model
{
    public const ESTADO_PENDIENTE   = 0;
    public const ESTADO_PROCESO     = 1;
    public const ESTADO_CULMINADO    = 2;
    public const ESTADO_ARCHIVADO   = 3;
    public const ESTADO_ELIMINADO   = 4;

    protected $appends = ['estado_texto', 'expediente', 'encrypted_id'];

    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_encargos.tb_encargo';

    protected $primaryKey = 'enca_id';

    protected $fillable = [
        "enca_id",
        "enca_fecha",
        "enca_detalle",
        "enca_fechaaten",
        "enca_estado",
        "enca_fechasol",
        "enca_prioridad",
        "enca_progreso",
        "enca_conformidad",
        "enca_descripcion",
        "enca_fechaaprox",
        "usua_id",
        "clie_id",
        "enca_instruccion",
        "enca_temporal",
        "conc_id",
        "enca_estado_atencion",
        "is_important",
        "estado_interno",
    ];

    public function getEstadoTextoAttribute()
    {
        switch ($this->enca_estado) {
            case EncargoModel::ESTADO_PENDIENTE:
            case EncargoModel::ESTADO_PROCESO:
                return "PROCESO";
                break;
            case EncargoModel::ESTADO_CULMINADO:
                return "CULIMINADO";
                break;
            case EncargoModel::ESTADO_ARCHIVADO:
                return "ARCHIVADO";
                break;
            case EncargoModel::ESTADO_ELIMINADO:
                return "ELIMINADO";
                break;
            default:
                return "DEFAULT";
                break;
        }
    }
    public function getExpedienteAttribute()
    {
        return "encargo";
    }
    public function getEncryptedIdAttribute()
    {
        if (isset($this->attributes['enca_id'])) {
            return Crypt::encryptString($this->attributes['enca_id']);
        }
        return null;
    }


    /* public function responsables()
    {
        return $this->hasMany(UsuarioEncargoModel::class, 'enca_id')
            ->join('sc_seguridad.tb_usuario', 'tb_usuario.usua_id', '=', 'tb_usuario_encargo.usua_id')
            ->select(['enca_id','tb_usuario.usua_id', "usua_nombre AS nombre_completo", 'usua_foto','usen_estado'])
            ->whereIn('tb_usuario_encargo.usen_tipo', [0]) //1: RESPONSABLE, 0: SUPERVISOR
            ->where('usen_estado', UsuarioEncargoModel::ESTADO_ACTIVO);
    } */
}
