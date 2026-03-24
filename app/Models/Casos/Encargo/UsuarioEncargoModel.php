<?php

namespace App\Models\Casos\Encargo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioEncargoModel extends Model
{
    public const ESTADO_ACTIVO  = 1;
    public const ESTADO_ANULADO = 0;

    public const RESPONSABLE    = 1;
    public const INVOLUCRADO    = 0;

    protected $appends = ['estado_texto'];

    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_encargos.tb_usuario_encargo';

    protected $primaryKey = 'usen_id';

    protected $fillable = [
        "usen_id",
        "usua_id",
        "enca_id",
        "usen_tipo",
        "usen_estado",
        "usen_fecha",
    ];

    public function getEstadoTextoAttribute(){
        return $this->usen_estado == UsuarioEncargoModel::ESTADO_ACTIVO ? 'ACTIVO' : 'ELIMINADO' ;
    }
}
