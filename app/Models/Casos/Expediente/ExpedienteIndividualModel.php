<?php

namespace App\Models\Casos\Expediente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteIndividualModel extends Model
{
    public const PRINCIPAL     = 1;
    public const NO_PRINCIPAL  = 0;
    
    public const ADMINISTRATIVO  = 1;
    public const JUDICIAL        = 2;
    public const CARPETA_FISCAL  = 3;
    
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_expedientes.tb_expediente_individual';

    protected $primaryKey = 'expe_indiv_id';

    protected $fillable = [
        "expe_indiv_id",
        "expe_id",
        "expe_indiv_fechacrea",
        "expe_indiv_estado",
        "usua_id",
        "expe_indiv_fecha",
        "expe_indiv_pretenmonto",
        "expe_indiv_tmateria",
        "expe_indiv_numero",
        "expe_indiv_tproceso",
        "expe_indiv_tjuzgado",
        "expe_indiv_tcarpeta",
        "expe_indiv_fiscalia",
        "expe_indiv_periodo",
        "expe_indiv_anioc",
        "expe_indiv_tipo",
        "expe_indiv_entidad",
        "expe_indiv_codigo_jud_fis",
        "expe_indiv_num_carpetafis",
        "expe_indiv_fecha_vence",
        "expe_indiv_dependencia",
        "expe_indiv_titulo",
        "expe_indiv_principal",
        "expe_indiv_titulo_completo_trigger",
    ];

    protected $attributes = [
        'expe_indiv_estado' => 0,
    ];
}
