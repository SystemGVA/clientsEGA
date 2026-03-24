<?php

namespace App\Models\Casos\Expediente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptoModel extends Model
{
    use HasFactory;

    public const ACTIVO     = 0; 
    public const ELIMINADO  = 1;

    protected $table = 'sc_expedientes.tb_conceptos'; 

    protected $primaryKey = 'conc_id'; 

    public $timestamps = true;

    protected $fillable = [
        'conc_id',
        'conc_prefijo',
        'conc_correlativo',
        'conc_nombre',
        'conc_abreviatura',
        'conc_estado',
        'conc_codigo',
        'conc_clase',
        'mate_id',
        'etap_id',
        'dist_id',
    ];
    
    protected $attributes = [
        'conc_estado' => 0,
    ];
}
