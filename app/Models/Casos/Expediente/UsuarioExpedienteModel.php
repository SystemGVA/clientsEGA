<?php

namespace App\Models\Casos\Expediente;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioExpedienteModel extends Model
{
    use HasFactory;
  
    protected $table = 'sc_expedientes.tb_usuario_expediente';

    protected $primaryKey = 'usua_expe_id';


    public $timestamps = false;

    protected $fillable = [
        'usua_expe_estado',
        'usua_id',
        'expe_id',
        'usua_expe_tipo',
        'temporal',
        'usua_expe_creacion',
    ];

    protected $attributes = [
        'temporal' => 0, 
        'usua_expe_estado' => 0,
    ];
    
    public function relacion_expediente()
    {
        return $this->belongsTo(ExpedienteModel::class, 'expe_id', 'expe_id');
    }

    public function relacion_usuario()
    {
        return $this->belongsTo(User::class, 'usua_id', 'usua_id');
    }
}
