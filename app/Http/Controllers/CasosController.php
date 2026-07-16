<?php

namespace App\Http\Controllers;

use App\Models\Casos\Encargo\ActividadModel;
use App\Models\Casos\Encargo\AnexoEnModel;
use App\Models\Casos\Encargo\EncargoModel;
use App\Models\Casos\Encargo\UsuarioEncargoModel;
use App\Models\Casos\Expediente\AnexoExModel;
use App\Models\Casos\Expediente\BitacoraExpedienteModel;
use App\Models\Casos\Expediente\ConceptoModel;
use App\Models\Casos\Expediente\ExpedienteIndividualModel;
use App\Models\Casos\Expediente\ExpedienteModel;
use App\Models\Casos\Expediente\UsuarioExpedienteModel;
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
            ->where('acti_preview',0)
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
    public function GET_ARCHIVE_ENCARGO(Request $request)
    {
        $archives = AnexoEnModel::select([
            'tb_anexo_actividad.anac_id AS id',
            'tb_anexo_actividad.anac_id',
            'tb_anexo_actividad.anac_ruta AS anac_nombre',
            'tb_anexo_actividad.anac_ruta',
            'tb_anexo_actividad.anac_tipo_archivo',
            'tb_anexo_actividad.anac_fecha',
            'enca_id',
            'tb_anexo_actividad.anac_estado',
            DB::raw('UPPER(tb_actividad.acti_descripcion) AS acti_descripcion'),
            'tb_actividad.acti_fecha_realizado'
        ])
            ->join('sc_encargos.tb_actividad', 'tb_actividad.acti_id', 'tb_anexo_actividad.acti_id')
            ->where('enca_id', $request->enca_id)
            ->where('anac_estado', 1)
            ->orderBy('acti_fecha_realizado', 'desc')
            ->get();

        return response()->json([
            'data' => $archives,
            "message" => "Lista de archivos obtenida correctamente",
            "status" => "success"
        ]);
    }



    //EXPEDIENTES
    public function GET_INFORMACION_EXPEDIENTE(Request $request)
    {
        $expe_id = HelpersController::getDecryptedId($request->id);

        $cliente = auth('cliente')->user()->clie_id;
        if (!$cliente) {
            return response()->json(["error" => "Cliente no autenticado"], 401);
        }

        $expediente = ExpedienteModel::select(
            'tb_expediente.expe_id',
            'tb_expediente.expe_correlativo',
            'tb_expediente.expe_tmateria as id_materia',
            'tb_expediente.expe_tcarpeta as numero_carpeta',
            'tb_expediente.expe_periodo as anio_expediente',
            'tb_expediente.expe_anioc as anio_fiscal',
            'tb_expediente.expe_fecha as fecha_expediente',
            DB::raw('(select sec.conc_nombre from sc_expedientes.tb_conceptos sec where sec.conc_id=(SELECT dee.pretension_id FROM sc_expedientes.tb_derecho_economico dee where dee.expe_id=tb_expediente.expe_id and dee.dili_id iS NULL and dee.esta_id iS NULL and dee.expe_indiv_id IS NULL order by dee.deec_id desc limit 1)) as nombre_pretension'),
            DB::raw('(select sec.conc_abreviatura from sc_expedientes.tb_conceptos sec where sec.conc_id = tb_expediente.expe_tmateria) as abreviatura_materia'),
            'tb_expediente.expe_fiscalia as id_fiscalia',
            'tb_expediente.expe_tjuzgado as id_juzgado',
            DB::raw('(SELECT c.clie_id FROM sc_expedientes.tb_cliente_parte c WHERE c.expe_id=tb_expediente.expe_id and c.tipo_parte_num=0 order by c.clie_part_id desc limit 1) as cliente_id'),
            DB::raw("(SELECT UPPER(CONCAT(c.clie_nombre,' (',xx.conc_abreviatura,')')) FROM sc_expedientes.tb_cliente_parte c LEFT JOIN sc_expedientes.tb_conceptos AS xx ON (tipo_parte_id = xx.conc_id) WHERE c.expe_id=tb_expediente.expe_id and c.tipo_parte_num=0 order by c.clie_part_id desc limit 1) as clientes"),
            DB::raw("(SELECT UPPER(CONCAT(c.clie_nombre,' (',xx.conc_abreviatura,')')) FROM sc_expedientes.tb_cliente_parte c LEFT JOIN sc_expedientes.tb_conceptos AS xx ON (tipo_parte_id = xx.conc_id) WHERE c.expe_id=tb_expediente.expe_id and c.tipo_parte_num=1 order by c.clie_part_id desc limit 1) as clientes_contraria"),
            'tb_expediente.expe_tipo as expe_tipo',
            'tb_expediente.expe_entidad as expe_entidad',
            'tb_expediente.expe_codigo_jud_fis as expe_codigo_jud_fis',
            'tb_expediente.expe_num_carpetafis as expe_num_carpetafis',
            'tb_expediente.expe_dependencia as expe_dependencia',
            'tb_expediente.expe_fecha_vence as expe_fecha_vence',
            DB::raw('(SELECT COUNT(*) FROM sc_expedientes.tb_estado e WHERE e.expe_id = tb_expediente.expe_id) as cantidad_estados'),
            DB::raw('(SELECT COUNT(*) FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_proceso = 2 AND d.dili_status = 5 AND d.dili_tipo <> 0 AND d.dili_estado = 0) as cantidad_diligencia'),
            DB::raw('(SELECT COUNT(*) FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_proceso = 2 AND d.dili_status = 5 AND d.dili_tipo = 0 AND d.dili_estado = 0) as cantidad_eventos'),
            DB::raw('(SELECT COUNT(*) FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_status <> 5 AND d.dili_estado = 0) as cantidad_diligencia_normal'),
            'tb_expediente.expe_instruccion',
            'tb_expediente.expe_titulo',
            DB::raw("(SELECT d.dili_fechacump FROM sc_expedientes.tb_diligencia d WHERE d.dili_status=5 and d.dili_proceso=2 and d.expe_id=tb_expediente.expe_id order by d.dili_fecha_registro desc limit 1) as fecha_vence_diligencia"),
            DB::raw("(SELECT (SELECT c.conc_nombre FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1) as estado_final"),
            DB::raw("(SELECT to_char(e.esta_fecha, 'DD/MM/YYYY') FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1) as esta_fecha"),
            DB::raw("(SELECT (SELECT c.conc_id FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1) as id_estado"),
            DB::raw("(SELECT (SELECT c.conc_nombre FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1) as estado"),
            DB::raw("(SELECT co.conc_id FROM sc_expedientes.tb_conceptos co WHERE co.conc_id=(SELECT (SELECT c.etap_id FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1)) as id_etapa"),
            DB::raw("(SELECT co.conc_nombre FROM sc_expedientes.tb_conceptos co WHERE co.conc_id=(SELECT (SELECT c.etap_id FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1)) as etapa_final"),
            DB::raw('(SELECT d.dili_descripcion FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_estado = 0 AND d.dili_status = 2 AND d.dili_proceso = 1 AND d.dili_f_ultima_ejecutada IS NOT NULL ORDER BY d.dili_f_ultima_ejecutada DESC LIMIT 1) as ultima_diligencia'),
            DB::raw('(SELECT d.dili_even_titulo FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_estado = 0 AND d.dili_status = 2 AND d.dili_proceso = 1 AND d.dili_f_ultima_ejecutada IS NOT NULL ORDER BY d.dili_f_ultima_ejecutada DESC LIMIT 1) as ultima_diligencia_evento'),
            DB::raw("(SELECT d.dili_tipo FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_estado=0 AND d.dili_status=2 AND d.dili_proceso=1 AND dili_f_ultima_ejecutada IS NOT NULL order by dili_f_ultima_ejecutada DESC limit 1) as diligencia_tipo_ejecutada"),
            DB::raw("(SELECT to_char(d.dili_fechainicio, 'DD/MM/YYYY') FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_status=2 AND d.dili_proceso=1 AND dili_f_ultima_ejecutada IS NOT NULL order by dili_f_ultima_ejecutada DESC limit 1) as dili_fechainicio"),
            DB::raw("(SELECT to_char(d.dili_fechacump, 'DD/MM/YYYY') FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_status=2 AND d.dili_proceso=1 AND dili_f_ultima_ejecutada IS NOT NULL order by dili_f_ultima_ejecutada DESC limit 1) as dili_fechacump"),

            DB::raw("((SELECT co.conc_nombre FROM sc_expedientes.tb_conceptos co WHERE co.conc_id=(SELECT (SELECT c.etap_id FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1)) || ' - ' || 
            (SELECT (SELECT c.conc_nombre FROM sc_expedientes.tb_conceptos c where c.conc_id=e.esta_testado) FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=1 order by e.esta_fecha_registro desc limit 1) || ' - ' || (SELECT d.dili_descripcion FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id order by dili_fecha_registro desc limit 1)) as estado_proceso"),
            DB::raw("((SELECT d.dili_descripcion FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_status=5 AND d.dili_proceso=2 AND d.dili_estado=0 order by d.dili_fechacump asc limit 1)) as dili_programada"),

            DB::raw('(SELECT d.dili_fechacump FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_status = 5 AND d.dili_proceso = 2 AND d.dili_estado = 0 ORDER BY d.dili_fechacump ASC LIMIT 1) as fecha_dili_programada_texto'),
            DB::raw("(SELECT CAST(CAST(to_char(d.dili_f_ultima_ejecutada, 'YYYY-MM-DD') AS DATE) + CAST('14 days' AS INTERVAL) as DATE) FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_status<>5 AND d.dili_estado=0 and d.dili_f_ultima_ejecutada is not null order by d.dili_f_ultima_ejecutada desc limit 1) as fecha_ultima_ejecutada"),
            DB::raw('(SELECT d.dili_tipo FROM sc_expedientes.tb_diligencia d WHERE d.expe_id = tb_expediente.expe_id AND d.dili_estado = 0 AND d.dili_proceso = 2 AND d.dili_status = 5 ORDER BY d.dili_fechacump ASC LIMIT 1) as dili_tipo'),
            DB::raw('(SELECT e.etiq_id FROM sc_expedientes.tb_etiqueta_expediente e WHERE e.expe_id = tb_expediente.expe_id LIMIT 1) as etiq_id'),
            DB::raw('(SELECT e.etiq_descripcion FROM sc_expedientes.tb_etiqueta e WHERE e.etiq_id = (SELECT ex.etiq_id FROM sc_expedientes.tb_etiqueta_expediente ex WHERE ex.expe_id = tb_expediente.expe_id LIMIT 1)) as etiq_descripcion'),
            DB::raw('(SELECT e.etiq_color FROM sc_expedientes.tb_etiqueta e WHERE e.etiq_id = (SELECT ex.etiq_id FROM sc_expedientes.tb_etiqueta_expediente ex WHERE ex.expe_id = tb_expediente.expe_id) LIMIT 1) as etiq_color'),
            DB::raw("(SELECT d.dili_tipo FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_estado=0 AND d.dili_proceso=2 AND d.dili_status=5 order by d.dili_fechacump asc limit 1) as dili_tipo"),
            DB::raw(" (SELECT d.dili_even_titulo FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_estado=0 AND d.dili_status=5 AND d.dili_proceso=2 AND d.dili_tipo=0 order by d.dili_fechacump asc limit 1) as dili_even_titulo"),
            DB::raw("(SELECT to_char(d.dili_fechainicio, 'DD/MM/YYYY') FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_estado=0 AND d.dili_status=5 AND d.dili_proceso=2 AND d.dili_tipo=0 order by d.dili_fechacump asc limit 1) as fecha_evento"),
            DB::raw("(SELECT d.dili_fechainicio FROM sc_expedientes.tb_diligencia d WHERE d.expe_id=tb_expediente.expe_id AND d.dili_estado=0 AND d.dili_status=5 AND d.dili_proceso=2 AND d.dili_tipo=0 order by d.dili_fechacump asc limit 1) as fecha_evento_texto"),
            DB::raw("(SELECT e.esta_nombre_atipico FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=2 order by e.esta_fecha_registro desc limit 1) as estado_atipico"),
            DB::raw("(SELECT to_char(e.esta_fecha, 'DD/MM/YYYY') FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id and e.esta_tipo_atipico=2 order by e.esta_fecha_registro desc limit 1) as fecha_estado_atipico"),
            DB::raw("(SELECT e.esta_tipo_atipico FROM sc_expedientes.tb_estado e WHERE e.expe_id=tb_expediente.expe_id order by e.esta_fecha_registro desc limit 1) as tipo_estado"),
            DB::raw('current_date as fecha_hoy'),
            'tb_expediente.is_detenido',
            'tb_expediente.motivo_detenido',
            DB::raw('(SELECT i.expe_indiv_id FROM sc_expedientes.tb_expediente_individual i WHERE i.expe_id = tb_expediente.expe_id ORDER BY i.expe_indiv_id ASC LIMIT 1) as expediente_individual'),
            DB::raw('(SELECT i.expe_indiv_tproceso FROM sc_expedientes.tb_expediente_individual i WHERE i.expe_id = tb_expediente.expe_id ORDER BY i.expe_indiv_id ASC LIMIT 1) as id_tipo_proceso'),
            //'tb_expediente.expe_tproceso as id_tipo_proceso',
            'tb_expediente.expe_estado'
        )
            ->where('tb_expediente.expe_id', $expe_id)
            ->orderBy('tb_expediente.expe_id', 'desc')
            ->first();
        if (!$expediente) {
            return response()->json(["error" => "Expediente no encontrado"], 404);
        }
        $expediente->involucrados = UsuarioExpedienteModel::select(
            'tb_usuario_expediente.usua_expe_id',
            'tb_usuario_expediente.usua_id',
            'tb_usuario_expediente.expe_id',
            'tb_usuario.usua_siglasdoc',
            'tb_usuario.usua_email',
            'tb_usuario.usua_estado',
            'tb_usuario_expediente.usua_expe_tipo',
            DB::raw("CONCAT(tb_usuario.usua_nombre, ' ', tb_usuario.usua_apelpat, ' ', tb_usuario.usua_apelmat) AS nombre_completo"),
            'tb_usuario.usua_nombre',
            DB::raw("CONCAT('https://sisega.gamarrafirma.com',tb_usuario.usua_foto) as usua_foto"),
            'tb_usuario.usua_costoh'
        )
            ->join('sc_seguridad.tb_usuario as tb_usuario', 'tb_usuario_expediente.usua_id', '=', 'tb_usuario.usua_id')
            ->where('tb_usuario_expediente.expe_id', $expediente->expe_id)
            ->where('tb_usuario_expediente.usua_expe_estado', 0)
            ->where('tb_usuario.usua_estado', 0)
            ->orderBy('tb_usuario_expediente.usua_expe_id', 'DESC')
            ->get();

        $expediente->MotivoArchi = DB::table('sc_expedientes.tb_operacion')
            ->where('expe_id', $expe_id)
            ->where('ope_estado', 0)
            ->orderByDesc('ope_id')
            ->first();
        return response()->json([
            "expediente" => $expediente,
            "message" => "Información del expediente obtenida correctamente",
            "status" => "success"
        ]);
    }
    public function GET_EXPEDIENTE_INDIVIDUALES(Request $request)
    {
        $data =  ExpedienteIndividualModel::select(
            '*',
            DB::raw('UPPER(expe_indiv_titulo) as expediente')
        )->where('expe_id', $request->expe_id)->get();

        return response()->json([
            "data" => $data,
            "message" => "Lista de expediente obtenida correctamente",
            "status" => "success"
        ]);
    }
    public function GET_ARCHIVE_EXPEDIENTE(Request $request)
    {
        $archives = AnexoExModel::select([
            'tb_anexo_expediente.anex_exp_id AS id',
            'tb_anexo_expediente.anex_exp_id',
            'tb_anexo_expediente.anex_exp_nombre',
            'tb_anexo_expediente.anex_exp_ruta',
            'tb_anexo_expediente.anex_exp_extension',
            'tb_anexo_expediente.anex_exp_fecha',
            'tb_anexo_expediente.anex_exp_descripcion',
            'tb_anexo_expediente.expe_id',
            'tb_anexo_expediente.dili_id',
            'tb_anexo_expediente.anex_exp_estado',
            'tb_anexo_expediente.esta_id',
            'tb_anexo_expediente.expe_indiv_id',
            'tb_diligencia.dili_descripcion',
            'tb_diligencia.dili_even_titulo',
            'tb_estado.esta_testado',
            'tb_estado.esta_nombre_atipico',
            DB::raw("
            CASE 
                WHEN tb_diligencia.dili_id IS NOT NULL THEN tb_diligencia.dili_fecha_registro
                ELSE tb_estado.esta_fecha_registro
            END AS fecha_registro")
        ])
            ->leftJoin('sc_expedientes.tb_diligencia', 'tb_diligencia.dili_id', '=', 'tb_anexo_expediente.dili_id')
            ->leftJoin('sc_expedientes.tb_estado', 'tb_estado.esta_id', '=', 'tb_anexo_expediente.esta_id')
            ->where('tb_anexo_expediente.expe_id', $request->expe_id)
            ->where('tb_anexo_expediente.expe_indiv_id', $request->expe_indi_id)
            ->where('tb_anexo_expediente.anex_exp_estado', '0')
            ->orderBy('fecha_registro', 'desc')
            ->get()
            ->map(function ($archive) {
                if ($archive->esta_testado == 0 &&  $archive->esta_nombre_atipico != null) {
                    $archive->estado_descripcion = $archive->esta_nombre_atipico;
                } else if ($archive->esta_testado != 0) {
                    $archive->estado_descripcion = ConceptoModel::where('conc_id', $archive->esta_testado)
                        ->value('conc_nombre');
                } else {
                    $archive->estado_descripcion = 'DE TRÁMITE';
                }
                return $archive;
            });

        return response()->json([
            'data' => $archives,
            "message" => "Lista de archivos obtenida correctamente",
            "status" => "success"
        ]);
    }
    public function GET_ACTIVITY_EXPEDIENTE(Request $request)
    {

        $conceptos = collect(DB::select("SELECT *, usua_nombre AS nombre_usuario_asignado, CONCAT('https://sisega.gamarrafirma.com', usua_foto) AS usua_foto
            FROM sc_expedientes.fn_listar_estados_diligencias(?)", [$request->expe_indi_id]))->where('esta_dili_preview', 0);
        $tiempo_total = round($conceptos->sum('tiempo_ejecucion'), 3);
        $tiempo_usuarios = $conceptos->groupBy('usuario_id')->map(function ($items) {
            $tiempo = round($items->sum('tiempo_ejecucion'), 3);
            return [
                'foto' => $items->first()->usua_foto,
                'nombre' => $items->first()->usua_nombre,
                'tiempo' => (float) $tiempo,
            ];
        })->filter(fn($item) => $item['tiempo'] > 0)->values();


        $Bitacoras =  BitacoraExpedienteModel::join('sc_seguridad.tb_usuario', "tb_usuario.usua_id", "=", "tb_bitacora_expediente.usua_id")
            ->where("expe_indiv_id", "=", $request->expe_indi_id)->get([
                "tb_bitacora_expediente.*",
                DB::raw("TRIM(UPPER(CONCAT(usua_apelpat, ' ' , usua_apelmat, ' ', usua_nombre))) AS nombre_usuario"),
                "usua_foto"
            ])->where('biex_estado', BitacoraExpedienteModel::ESTADO_ACTIVO)->all();

        $Conjunto = $conceptos->merge(collect($Bitacoras));


        $Conjunto = $Conjunto->sortByDesc(function ($item) {
            return strtotime($item->esta_dili_fecha_registro);
        })->values();



        return response()->json([
            'data' => $Conjunto,
            'tiempo_total' => (float) $tiempo_total,
            'tiempo_usuarios' => $tiempo_usuarios,
        ]);
    }
}
