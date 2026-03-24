<?php

namespace App\Http\Controllers;

use App\Models\Casos\Encargo\ActividadModel;
use App\Models\Casos\Encargo\EncargoModel;
use App\Models\Casos\Encargo\UsuarioEncargoModel;
use App\Models\Casos\Expediente\ConceptoModel;
use App\Models\Casos\Expediente\ExpedienteModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CasosController extends Controller
{
    public function ALL_CONCEPTS(Request $request)
    {
        try {
            $queryConceptos = ConceptoModel::query();

            if (!empty($request->conc_prefijo)) {
                $queryConceptos->where('conc_prefijo', $request->conc_prefijo);
            }

            $queryConceptos->where('conc_estado', 0)
                ->where('conc_correlativo', '!=', 0)
                ->orderBy('conc_nombre');

            $conceptos = $queryConceptos->get();

            return response()->json($conceptos);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener conceptos',
                'detalle' => $th->getMessage()
            ], 500);
        }
    }
    public function ALL_CASES_CLIENTE(Request $request)
    {
        $cliente = auth('cliente')->user()->clie_id;
        if (!$cliente) {
            return response()->json(["error" => "Cliente no autenticado"], 401);
        }
        $listas = $request->listas ?? [];

        $mostrarEncargos = empty($listas) || in_array('encargos', $listas);
        $mostrarCasos    = empty($listas) || in_array('casos', $listas);

        $data = [];

        if ($mostrarEncargos) {
            $queryEncargos = EncargoModel::select([
                "tb_encargo.*",
                DB::raw("CONCAT('ENCA: ', tb_encargo.enca_id, ' // ', tb_encargo.enca_detalle) AS titulo"),
                "tb_conceptos.conc_nombre as materia"
                //"tb_encargo.estado_interno as estado",
            ])
                ->join('sc_expedientes.tb_conceptos', 'tb_conceptos.conc_id', 'tb_encargo.conc_id')
                ->where('clie_id', $cliente);
            if (!empty($request->materia)) {
                $queryEncargos->where('tb_encargo.conc_id', $request->materia);
            }
            if (!empty($request->estado)) {
                $mapEstados = [
                    'En Proceso' => [EncargoModel::ESTADO_PENDIENTE, EncargoModel::ESTADO_PROCESO],
                    'Culminado'  => [EncargoModel::ESTADO_CULMINADO],
                    'Archivado'  => [EncargoModel::ESTADO_ARCHIVADO],
                    'Eliminado'  => [EncargoModel::ESTADO_ELIMINADO],
                ];

                if (isset($mapEstados[$request->estado])) {
                    $queryEncargos->whereIn('enca_estado', $mapEstados[$request->estado]);
                }
            }

            if (!empty($request->buscar)) {
                $queryEncargos->where(function ($q) use ($request) {
                    $q->where('enca_id', 'ilike', '%' . $request->buscar . '%')
                        ->orWhere('enca_detalle', 'ilike', '%' . $request->buscar . '%');
                });
            }

            $encargos = $queryEncargos->get();

            $data = array_merge($data, $encargos->toArray());
        }

        if ($mostrarCasos) {
            $queryCasos = ExpedienteModel::select([
                "tb_expediente.*",
                'expe_indiv_titulo_completo_trigger as titulo',
                "tb_conceptos.conc_nombre as materia"
            ])
                ->join('sc_expedientes.tb_expediente_individual', 'tb_expediente_individual.expe_id', 'tb_expediente.expe_id')
                ->join('sc_expedientes.tb_conceptos', 'tb_conceptos.conc_id', 'tb_expediente_individual.expe_indiv_tmateria')
                ->join('sc_expedientes.tb_cliente_parte', 'tb_cliente_parte.expe_id', 'tb_expediente.expe_id')
                ->where('tb_cliente_parte.clie_id', $cliente);

            if (!empty($request->materia)) {
                $queryCasos->where('tb_conceptos.conc_id', $request->materia);
            }
            if (!empty($request->estado)) {
                $mapEstados = [
                    'En Proceso' => [ExpedienteModel::ESTADO_PROCESO],
                    'Culminado'  => [ExpedienteModel::ESTADO_CULMINADO],
                    'Archivado'  => [ExpedienteModel::ESTADO_ARCHIVADO],
                    'Eliminado'  => [ExpedienteModel::ESTADO_ELIMINADO],
                ];
                if (isset($mapEstados[$request->estado])) {
                    $queryCasos->whereIn('expe_estado', $mapEstados[$request->estado]);
                }
            }
            if (!empty($request->buscar)) {
                $queryCasos->where(function ($q) use ($request) {
                    $q->where('expe_indiv_titulo_completo_trigger', 'ilike', '%' . $request->buscar . '%');
                });
            }

            $casos = $queryCasos->get();

            $data = array_merge($data, $casos->toArray());
        }

        return response()->json([
            "headers" => [
                ["title" => "Titulo", "value" => "titulo", "sortable" => true],
                ["title" => "Materia", "value" => "materia", "sortable" => true],
                ["title" => "Estado", "value" => "estado_texto", "sortable" => true],
                ["title" => "Fecha", "value" => "fecha"]
            ],
            "items" => $data
        ]);
    }



    //ENCARGOS
    public function GET_INFORMACION_ENCARGO(Request $request)
    {
        $enca_id = HelpersController::getDecryptedId($request->id);

        $cliente = auth('cliente')->user()->clie_id;
        if (!$cliente) {
            return response()->json(["error" => "Cliente no autenticado"], 401);
        }

        $encargo = EncargoModel::select([
            "tb_encargo.*",
            "tb_conceptos.conc_nombre as materia",
            "clie_doc",
            DB::raw("TRIM(CONCAT(COALESCE(clie_nombre, ''), ' ',COALESCE(clie_apelpat, ''), ' ', COALESCE(clie_apelmat, ''), ' ',COALESCE(clie_razonsoc, ''))) as cliente_nombre")
        ])->join('sc_expedientes.tb_conceptos', 'tb_conceptos.conc_id', 'tb_encargo.conc_id')
            ->join('sc_seguridad.tb_cliente', 'tb_cliente.clie_id', 'tb_encargo.clie_id')
            ->where('tb_encargo.clie_id', $cliente)
            ->where('enca_id', $enca_id)
            ->first();

        if (!$encargo) {
            return response()->json(["error" => "Encargo no encontrado"], 404);
        }
        $encargo->involucrados = UsuarioEncargoModel::select([
            'tb_usuario_encargo.usua_id',
            'tb_usuario_encargo.usen_id',
            'tb_usuario_encargo.enca_id',
            'tb_usuario_encargo.usen_estado',
            'tb_usuario_encargo.usen_tipo',
            'usua_email',
            DB::raw("CONCAT(tb_usuario.usua_nombre, ' ', tb_usuario.usua_apelpat, ' ', tb_usuario.usua_apelmat) AS nombre_completo"),
            'tb_usuario.usua_siglasdoc',
            'tb_usuario.usua_nombre',
            DB::raw("CONCAT('https://sisega.gamarrafirma.com', tb_usuario.usua_foto) as usua_foto"),
            'tb_usuario.usua_costoh',
            DB::raw("false::boolean AS notificar")

        ])
            ->join('sc_seguridad.tb_usuario', 'tb_usuario.usua_id', 'tb_usuario_encargo.usua_id')
            ->where('usen_estado', 1)
            ->where('usua_estado', 0)
            ->where('enca_id', $encargo->enca_id)
            ->get();

        return response()->json([
            "encargo" => $encargo,
            "message" => "Información del encargo obtenida correctamente",
            "status" => "success"
        ]);
    }
    public function GET_ACTIVITY_ENCARGO(Request $request)
    {
        $Tareas = ActividadModel::selectRaw("
        tb_actividad.*,
        UPPER(TRIM(acti_descripcion)) AS acti_descripcion,
        asignado.usua_nombre AS nombre_usuario_asignado,
        CONCAT('https://sisega.gamarrafirma.com', asignado.usua_foto) as usua_foto,
        asignado.usua_inicial,
        (SELECT COUNT(anac_id) 
            FROM sc_encargos.tb_anexo_actividad 
            WHERE tb_anexo_actividad.acti_id = tb_actividad.acti_id 
            AND anac_estado = 1
        ) AS total_archivos,
        (acti_fechalimite - CURRENT_DATE) AS diff_fecha,
        CASE acti_cumplimiento 
            WHEN 1 THEN acti_fecha_realizado::DATE
            WHEN 0 THEN acti_fechalimite::DATE
        END AS fecha_ordenamiento,
        actividad_superior,
        acti_preview,
        acti_destacado
    ")
            ->leftJoin('sc_seguridad.tb_usuario as asignado', 'asignado.usua_id', '=', 'tb_actividad.asignado_a')
            ->whereNull('actividad_superior')
            ->where('acti_descripcion', '!=', 'CREACION DE ENCARGO')
            ->where('acti_estado', 1)
            ->where('enca_id', $request->id)
            ->orderByRaw('acti_cumplimiento = 0 DESC')
            ->orderByDesc('fecha_ordenamiento')
            ->orderByDesc('acti_id')
            ->get();


        $tiempo_total = round($Tareas->sum('tiempo_ejecucion'), 3);

        $tiempo_usuarios = $Tareas->groupBy('asignado_a')->map(function ($items) {
            $tiempo = round($items->sum('tiempo_ejecucion'), 3);
            return [
                'foto' => $items->first()->usua_foto,
                'nombre' => $items->first()->nombre_usuario_asignado,
                'tiempo' => (float) $tiempo,
            ];
        })->filter(fn($item) => $item['tiempo'] > 0)->values();

        return response()->json([
            'data' => $Tareas,
            'tiempo_total' => (float) $tiempo_total,
            'tiempo_usuarios' => $tiempo_usuarios,
        ]);
    }
}
