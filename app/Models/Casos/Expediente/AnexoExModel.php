<?php

namespace App\Models\Casos\Expediente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class AnexoExModel extends Model
{
    public const ESTADO_ACTIVO  = 0;
    public const ESTADO_ANULADO = 1;

    use HasFactory;

    public $timestamps = false;

    protected $table = 'sc_expedientes.tb_anexo_expediente';

    protected $primaryKey = 'anex_exp_id';

    protected $fillable = [
        "anex_exp_id",
        "anex_exp_nombre",
        "anex_exp_ruta",
        "anex_exp_extension",
        "anex_exp_fecha",
        "anex_exp_descripcion",
        "expe_id",
        "expe_indiv_id",
        "dili_id",
        "esta_id",
        "anex_exp_estado",
    ];


    protected $appends = [
        'estado_texto',
        "icono_extension",
        "ruta",
    ];

    public function getEstadoTextoAttribute()
    {
        return $this->anex_exp_estado == AnexoExModel::ESTADO_ACTIVO ? 'ACTIVO' : 'ELIMINADO';
    }

    public function getIconoExtensionAttribute()
    {
        switch ($this->anex_exp_extension) {
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
                return ["mdi-package-down", "warning", mb_strtoupper($this->anex_exp_extension, 'UTF-8')];
        }
    }


    public function getRutaAttribute()
    {
        //$dominio = URL::to('/');
        $dominio = 'https://sisega.gamarrafirma.com';
        return $dominio . '/' . $this->anex_exp_ruta;
    }
}
