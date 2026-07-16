<?php

namespace App\Models\Casos\Encargo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class AnexoEnModel extends Model
{
    public const ESTADO_ACTIVO  = 1;
    public const ESTADO_ANULADO = 0;

    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_encargos.tb_anexo_actividad';

    protected $primaryKey = 'anac_id';

    protected $fillable = [
        "anac_id",
        "anac_ruta",
        "anac_estado",
        "acti_id",
        "anac_tipo_archivo",
        "usua_id",
        "anac_fecha",
        "anac_nombre_auxiliar",
        "enca_id",
    ];

    protected $appends = [
        'estado_texto',
        "icono_extension",
        "ruta",
    ];

    public function getEstadoTextoAttribute()
    {
        return $this->anac_estado == AnexoEnModel::ESTADO_ACTIVO ? 'ACTIVO' : 'ELIMINADO' ;
    }

    public function getIconoExtensionAttribute()
    {
        switch ($this->anac_tipo_archivo) {
            case 'docx':
            case 'doc':
            case 'docm':
                return ["mdi-microsoft-word", "info", "WORD"];
                break;
            case 'pdf':
                return ["mdi-file-pdf-box", "red", "PDF"];
                break;
            case 'xls':
            case 'xlsx':
            case 'xlsm':
            case 'xlsb':
                return ["mdi-microsoft-excel", "green", "EXCEL"];
                break;
            case 'ppt':
            case 'pptx':
            case 'pptm':
            case 'potx':
                return ["mdi-microsoft-powerpoint", "orange", "POWERPOINT"];
                break;
            case 'one':
                return ["mdi-microsoft-onenote", "purple", "NOTES"];
                break;
            case 'png':
            case 'jpeg':
            case 'jpg':
            case 'tif':
            case 'jfif':
                return ["mdi-file-image", "orange", "IMAGEN"];
                break;
            default:
                return ["mdi-package-down", "warning", mb_strtoupper($this->anex_exp_extension,'UTF-8') ];
        }
    }


    public function getRutaAttribute()
    {
        //$dominio = URL::to('/');
        $dominio = 'https://sisega.gamarrafirma.com';
        return $dominio . '/storage/actividad/' . $this->anac_ruta;
    }
    
}
