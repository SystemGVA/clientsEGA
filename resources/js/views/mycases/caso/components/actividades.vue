<template>
    <v-card rounded="md" elevation="0">
        <v-card-title class="px-3 py-2">
            <v-autocomplete class="pt-1 pb-1" :rules="[(v) => !!v || 'Expediente Individual es requerida']"
                :items="l_ExpedientesIndividuales" item-title="expediente" label="EXPEDIENTE INDIVIDUAL"
                :loading="loadingSelector" item-value="expe_indiv_id" color="primary" density="compact"
                variant="outlined" hide-details rounded chips small-chips @update:modelValue="CHANGE_EXPEDIENTE"
                v-model="expediente_individual_seleccion">
                <template v-slot:chip="{ props, item }">
                    <v-chip v-bind="props" size="small">
                        {{ item.expediente }}
                        &nbsp;
                        <v-menu location="bottom" rounded="xl">
                            <template #activator="{ props: menuProps }">
                                <v-chip color="primary" text-color="white" size="x-small" v-bind="menuProps">
                                    <v-icon>mdi-clock-time-two</v-icon>
                                    &nbsp;
                                    <strong>{{ TiempoTotal }}</strong>
                                </v-chip>
                            </template>
                            <v-list density="compact">
                                <v-list-item v-for="(usuario, index) in TiempoUsuarios" :key="index">
                                    <v-list-item-subtitle>
                                        <v-avatar size="20">
                                            <img :src="usuario.foto" />
                                        </v-avatar>
                                        &nbsp;
                                        <small>
                                            <strong class="primary--text">{{ usuario.nombre }}</strong>
                                            &nbsp;({{ revertirFormatoTiempo(usuario.tiempo) }})
                                        </small>
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-chip>
                </template>
                <template #selection="{ item }">

                    <v-chip small>
                        {{ item.raw.expediente }}&nbsp;
                        <v-menu location="bottom" rounded="lg">
                            <template #activator="{ props }">
                                <v-chip color="primary" text-color="white" size="x-small" v-bind="props">
                                    <v-icon x-small left>mdi-clock-time-two</v-icon>
                                    <strong>
                                        {{ TiempoTotal }}
                                    </strong>
                                </v-chip>
                            </template>
                            <v-list dense>
                                <v-list-item v-for="(usuario, index) in TiempoUsuarios" :key="index">
                                    <v-list-item-subtitle>
                                        <v-avatar size="20" left small>
                                            <img loading="lazy" :src='usuario.foto' />
                                        </v-avatar>&nbsp;<strong class="primary--text">{{ usuario.nombre
                                        }}</strong>&nbsp;({{
                                                revertirFormatoTiempo(usuario.tiempo) }})
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-chip>
                </template>
            </v-autocomplete>
        </v-card-title>
        <v-card-text v-if="actividades" class="heightEncargo d-flex flex-column">
            <div v-if="loading" class="flex-grow-1 d-flex align-center justify-center">
                <v-progress-circular color="primary" indeterminate size="48" />
            </div>
            <v-alert style="font-size: 12pt" color="primary" class="text-center" variant="outlined"
                v-else-if="actividades.length == 0">
                <v-icon color="primary" size="50px">
                    mdi-timeline-clock-outline
                </v-icon>
                <hr class="mt-2 mb-2" />
                NO HAY NINGUN ACTIVIDAD REGISTRADO POR EL MOMENTO
            </v-alert>
            <perfect-scrollbar v-else class="mr-n2 pr-3 flex-grow-1" :options="{ suppressScrollX: true }">
                <v-timeline side="end" density="compact">
                    <template v-for="item in actividades" :key="item.esta_dili_id">
                        <v-timeline-item v-if="isBitacora(item)">
                            
                        </v-timeline-item>
                        <v-timeline-item v-else-if="isEstado(item)">
                            <template v-slot:icon>
                                <v-tooltip location="bottom">
                                    <template v-slot:activator="{ props }">
                                        <v-avatar style="height: 38px; min-width: 38px; width: 38px">
                                            <img :src="item.usua_foto" loading="lazy" v-bind="props" />
                                        </v-avatar>
                                    </template>
                                    <span style="margin: 0px; padding: 0px">{{ item.nombre_usuario_asignado }}</span>
                                </v-tooltip>
                            </template>
                            <v-card class="mt-2 time-estado" style="background-color: rgb(var(--v-theme-primary),0.10) ;" elevation="10" rounded="md"
                                size="large" density="compact">
                                <v-container fluid>
                                    <v-row density="compact">
                                        <v-col cols="12" class="d-flex align-center">
                                            <small style="color: gray; font-weight: 100">
                                                #{{ item.esta_dili_id }}
                                            </small>
                                            &nbsp;
                                            <i style="color: rgb(var(--v-theme-primary)); font-weight: bold;">
                                                <small>
                                                    {{
                                                        (item.esta_dili_atipico == 1 ? item.nombre_estado
                                                            : (item.esta_dili_atipico == 2 ? item.estado_nombre_atipico :
                                                                'DE\nTRÁMITE')).toUpperCase()
                                                    }}
                                                </small>
                                            </i>

                                            <v-spacer></v-spacer>
                                            <v-badge v-if="item.esta_dili_archivos > 0"
                                                :content="item.esta_dili_archivos" :value="item.esta_dili_archivos"
                                                color="primary" overlap>
                                                <v-icon>
                                                    mdi-paperclip
                                                </v-icon>
                                            </v-badge>
                                            &nbsp;
                                            &nbsp;
                                            <v-tooltip location="bottom">
                                                <template #activator="{ props }">
                                                    <v-icon :color="item.esta_dili_destacado === 0 ? '#FDD835' : 'grey'" v-bind="props">
                                                        {{ item.esta_dili_destacado === 0 ? "mdi-star" : "mdi-star-outline" }}</v-icon>
                                                </template>
                                                <span>DESTACADO</span>
                                            </v-tooltip>
                                        </v-col>
                                        <v-col cols="2" class="my-n1">
                                            <small>
                                                <strong> FECHA </strong>
                                            </small>
                                        </v-col>
                                        <v-col cols="10" class="my-n1">
                                            <small>{{ formatearFecha(item.esta_fecha_dili_fechainicio) }}</small>
                                            &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                            <v-chip outlined size="x-small">
                                                <small>
                                                    <strong> RESOLUCIÓN </strong>
                                                </small>
                                                &nbsp;&nbsp;
                                                <small>
                                                    {{ item.estado_resolucion }}
                                                </small>
                                            </v-chip>
                                            &nbsp;&nbsp;
                                            <v-tooltip location="bottom">
                                                <template #activator="{ props }">
                                                    <v-icon v-bind="props">
                                                        mdi-gavel
                                                    </v-icon>
                                                </template>
                                                <span>Estado</span>
                                            </v-tooltip>
                                        </v-col>
                                        <template v-if="item.esta_resultado_dili_descripcion !== null && item.esta_resultado_dili_descripcion.trim() !== ''">
                                            <v-col cols="2" class="my-n1">
                                                <small>
                                                    <strong> RESULTADO </strong>
                                                </small>
                                            </v-col>
                                            <v-col cols="10" class="my-n1">
                                                <small>
                                                    {{ item.esta_resultado_dili_descripcion }}
                                                </small>
                                            </v-col>
                                        </template>
                                        <template v-if="item.esta_dili_detalle !== null && item.esta_dili_detalle.trim() !== ''">
                                            <v-col cols="2" class="my-n1">
                                                <small>
                                                    <strong>DETALLE</strong>
                                                </small>
                                            </v-col>
                                            <v-col cols="10" class="my-n1" style="">
                                                <small>
                                                    {{ item.esta_dili_detalle }}
                                                </small>
                                            </v-col>
                                        </template>
                                    </v-row>
                                </v-container>
                            </v-card>
                        </v-timeline-item>
                        <v-timeline-item v-else-if="isEventoDiligencia(item)" class="ma-1" size="large">
                            <template v-slot:icon>
                                <v-tooltip location="bottom">
                                    <template v-slot:activator="{ props }">
                                        <v-avatar style="height: 38px; min-width: 38px; width: 38px">
                                            <img :src="item.usua_foto" loading="lazy" v-bind="props" />
                                        </v-avatar>
                                    </template>
                                    <span style="margin: 0px; padding: 0px">{{ item.nombre_usuario_asignado }}</span>
                                </v-tooltip>
                            </template>
                            <v-card :class="isEvent(item) ? 'time-evento' : 'time-diligencia'" elevation="10"
                                rounded="md" size="large" density="compact" :outlined="!isEvent(item)"
                                :loading="item.loading">
                                <v-container fluid>
                                    <v-row>
                                        <v-col cols="12" lg="11">
                                            <v-row density="compact">
                                                <v-col cols="12" class="d-flex align-center">
                                                    <v-tooltip v-if="tooltipStatus(item)" location="bottom">
                                                        <template v-slot:activator="{ props }">
                                                            <v-icon v-bind="props" v-if="tooltipStatus(item)"
                                                                style="font-size: 20px !important"
                                                                :color="tooltipStatus(item)?.iconColor">
                                                                {{ tooltipStatus(item)?.icon }}
                                                            </v-icon>
                                                        </template>
                                                        <span>{{ tooltipStatus(item).label }}</span>
                                                    </v-tooltip>
                                                    &nbsp;
                                                    <small>#{{ item.esta_dili_id }} </small>&nbsp
                                                    <strong>
                                                        <small>
                                                            {{
                                                                (isEvent(item) ? item.dili_even_titulo || "Sin título"
                                                                    : item.esta_resultado_dili_descripcion ||
                                                                    "Sin descripción").toUpperCase()
                                                            }}
                                                        </small>


                                                        &nbsp;
                                                        <v-chip
                                                            v-if="item.diligencia_status == 2 || item.diligencia_status == 3"
                                                            :color="isEvent(item) ? 'secondary' : 'primary'"
                                                            size="x-small" text-color="white">
                                                            <v-icon dark start>mdi-clock-time-eight-outline</v-icon>
                                                            {{ revertirFormatoTiempo(item.tiempo_ejecucion) }}
                                                        </v-chip>
                                                        <!-- &nbsp;
                                                        <v-badge v-if="item.esta_dili_gastos > 0"
                                                            :content="item.esta_dili_gastos"
                                                            :value="item.esta_dili_gastos" bordered>
                                                            <v-icon color="primary"> mdi-cart </v-icon>
                                                        </v-badge> -->
                                                    </strong>
                                                    &nbsp;
                                                    <v-badge v-if="item.esta_dili_archivos > 0"
                                                        :content="item.esta_dili_archivos"
                                                        :value="item.esta_dili_archivos" color="primary" bordered>
                                                        <v-icon> mdi-paperclip </v-icon>
                                                    </v-badge>
                                                </v-col>
                                                <v-col cols="3" lg="2" class="my-n1">
                                                    <small>
                                                        <strong>FECHA</strong>
                                                    </small>
                                                </v-col>
                                                <v-col cols="9" :lg="isEvent(item) ? '5' : '10'" class="my-n1">
                                                    <small>
                                                        {{
                                                            formatearFecha(item.esta_fecha_dili_fechainicio) ===
                                                                formatearFecha(item.esta_fecha_dili_fechacump)
                                                                ? formatearFecha(item.esta_fecha_dili_fechacump)
                                                                : `${formatearFecha(item.esta_fecha_dili_fechainicio)} -
                                                        ${formatearFecha(item.esta_fecha_dili_fechacump)}`
                                                        }}
                                                        <template v-if="isEvent(item)">
                                                            &nbsp;{{ item.dili_even_hora_ini }}
                                                        </template>
                                                        &nbsp;
                                                        <span v-if="item.diligencia_status == 5">
                                                            <v-chip :color="calcularEstiloChip(item).color"
                                                                size="x-small" text-color="white">
                                                                {{ calcularEstiloChip(item).texto }}
                                                            </v-chip>
                                                        </span>
                                                    </small>
                                                </v-col>
                                                <template v-if="isEvent(item)">
                                                    <v-col cols="3" lg="2" class="my-n1">
                                                        <small>
                                                            <strong>UBICACIÓN</strong>
                                                        </small>
                                                    </v-col>
                                                    <v-col cols="9" lg="3" class="my-n1">
                                                        <small style="">
                                                            {{ item.dili_even_ubicacion }}
                                                        </small>
                                                    </v-col>
                                                </template>
                                                <template v-if="getContenido(item)">
                                                    <v-col cols="3" lg="2" class="my-n1">
                                                        <small>
                                                            <strong>{{ getTitulo(item) }}</strong>
                                                        </small>
                                                    </v-col>
                                                    <v-col cols="9" lg="10" class="my-n1">
                                                        <small>
                                                            {{ getContenido(item) }}
                                                        </small>
                                                    </v-col>
                                                </template>
                                                <template
                                                    v-if="item.diligencia_status == 3 || item.diligencia_status == 4">
                                                    <v-col cols="3" lg="2" class="my-n1">
                                                        <small>
                                                            <strong style="color: red"> SUSTENTO </strong>
                                                        </small>
                                                    </v-col>
                                                    <v-col cols="9" lg="10" class="my-n1">
                                                        <small style="color: red">
                                                            {{ item.esta_dili_sustento }}
                                                        </small>
                                                    </v-col>
                                                </template>
                                            </v-row>
                                        </v-col>
                                        <v-col cols="12" lg="1" class="d-flex justify-end">
                                            <v-tooltip bottom v-if="currentTooltip(item)">
                                                <template v-slot:activator="{ props }">
                                                    <v-icon v-bind="props">
                                                        {{ currentTooltip(item)?.icon }}
                                                    </v-icon>
                                                </template>
                                                <span>{{ currentTooltip(item)?.text }}</span>
                                            </v-tooltip>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card>
                        </v-timeline-item>
                    </template>
                </v-timeline>
            </perfect-scrollbar>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

//DATA
const expe_id = ref(null);
const expe_indi_id = ref(null);

const l_ExpedientesIndividuales = ref([]);
const expediente_individual_seleccion = ref(null);

const actividades = ref([]);
const loadingSelector = ref(true);
const loading = ref(true);
const TiempoTotal = ref('0M');
const TiempoUsuarios = ref([]);
const activityStatuses = [
    {
        value: 1,
        label: "REALIZADA",
        icon: "mdi-check-circle",
        color: "light-green",
        iconColor: "light-green",
    },
    {
        value: 2,
        label: "NO REALIZADA",
        icon: "mdi-close-circle",
        color: "red",
        iconColor: "red",
    },
    {
        value: 3,
        label: "FRUSTRADA",
        icon: "mdi-stop-circle",
        color: "cyan",
        iconColor: "black",
    },
];


//VALIDADORES
const isBitacora = item => !!item.biex_id;

const isEstado = item =>
    !item.biex_id &&
    item.estado_nombre_atipico !== 'diligencia';

const isEventoDiligencia = item =>
    !item.biex_id &&
    item.estado_nombre_atipico === 'diligencia' &&
    item.dili_tipo >= 0;

//WATCH
/* watch(() => props.encargo, (newVal) => {
    if (newVal?.enca_id) {
        GET_ACTIVIDADES();
        GET_DATA_EXP_INDI();
    }
}); */


//METODS    
const tooltipStatus = (item) => {
    const status = activityStatuses.find(
        (s) => s.value === item.acti_cumplimiento
    );
    return status || null;
};

const currentTooltip = (item) => {
    const tooltips = {
        3: { icon: "mdi-bullseye-arrow", text: "Seguimiento" },
        1: { icon: "mdi-flash", text: "Escrito menor" },
        4: { icon: "mdi-chart-timeline-variant", text: "Reportes o requerimientos" },
        2: { icon: "mdi-chart-donut-variant", text: "Escrito mayor" },
        0: { icon: "mdi-calendar-clock", text: "Evento" },
    };
    return tooltips[item.dili_tipo] || null;
};

const diasRestantesFn = (item) => {
    if (!item.esta_fecha_dili_fechacump) return 0;
    const fechaLimite = new Date(item.esta_fecha_dili_fechacump);
    const fechaActual = new Date();
    const diferenciaTiempo = fechaLimite - fechaActual;
    return Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));
};

const formatearFecha = (fecha) => {
    if (!fecha) return '-';

    return new Date(fecha).toLocaleDateString('es-PE', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};


const INIT = async (info) => {

    console.log(expediente_individual_seleccion.value);
    expe_id.value = info.expe_id;
    expe_indi_id.value = info.expediente_individual;

    await GET_DATA_EXP_INDI();
    expediente_individual_seleccion.value = info.expediente_individual;

    await GET_ACTIVIDADES();
}

const GET_ACTIVIDADES = async () => {
    loading.value = true;
    try {
        const res = await axios.post(`/api/expediente/activities`, {
            expe_indi_id: expe_indi_id.value
        });

        actividades.value = res.data.data;
        TiempoTotal.value = revertirFormatoTiempo(res.data.tiempo_total);
        TiempoUsuarios.value = res.data.tiempo_usuarios;

    } catch (error) {
        console.error('Error al obtener actividades:', error);
    } finally {
        loading.value = false;
    }
};

const GET_DATA_EXP_INDI = async () => {
    loadingSelector.value = true;
    try {
        const res = await axios.get(`/api/expediente/individuales/${expe_id.value}`);
        let lista_expe_individual_previo = res.data.data;
        let nueva_lista_expediente = [];

        lista_expe_individual_previo.forEach((item) => {
            let expediente = "";

            if (item.expe_indiv_tipo == 1) {
                expediente = `ADM : ${item.expediente} (${item.expe_indiv_numero}-${item.expe_indiv_periodo})`;
            } else if (item.expe_indiv_tipo == 2) {
                expediente = `JUD : ${item.expediente} (${item.expe_indiv_numero}-${item.expe_indiv_periodo})`;
            } else if (item.expe_indiv_tipo == 3) {
                let nombre_carpeta = item.expe_indiv_tcarpeta
                    ? item.expe_indiv_tcarpeta + "-"
                    : "";
                expediente = `C.FIS : ${item.expediente} (${nombre_carpeta}${item.expe_indiv_num_carpetafis} - ${item.expe_indiv_anioc})`;
            }
            nueva_lista_expediente.push({
                ...item,
                expediente,
            });
        });

        l_ExpedientesIndividuales.value = nueva_lista_expediente;
    } catch (error) {
        console.error('Error al obtener lista de expedientesS:', error);
    } finally {
        loadingSelector.value = false;
    }

}

const CHANGE_EXPEDIENTE = async (item) => {
    expe_indi_id.value = item;
    await GET_ACTIVIDADES();
}

const revertirFormatoTiempo = (tiempoConvertido) => {
    tiempoConvertido = parseFloat(tiempoConvertido);
    const horas = Math.floor(tiempoConvertido);
    const minutos = Math.round((tiempoConvertido - horas) * 60);

    if (horas > 0 && minutos > 0) {
        return `${horas}H ${minutos}M`;
    } else if (horas > 0) {
        return `${horas}H`;
    } else if (minutos > 0) {
        return `${minutos}M`;
    } else {
        return "0M";
    }
};

//DILIGENGIAS
const isEvent = (item) => {
    return item.dili_tipo == 0;
};
const getTitulo = (item) => {
    return item.dili_tipo === 0 ? 'RESEÑA' : 'DETALLE';
};
const getContenido = (item) => {
    const campo = item.dili_tipo === 0
        ? item.dili_even_resena
        : item.esta_dili_detalle;

    return campo?.trim() || null;
};
const calcularEstiloChip = (item) => {
    const diasRestantes = diasRestantesFn(item); // tu función existente

    let color = "";
    let texto = "";

    if (diasRestantes === 0) {
        color = "primary";
        texto = "Último día";
    } else if (diasRestantes > 0) {
        color = "green";
        texto = `Te quedan ${diasRestantes} días`;
    } else if (diasRestantes < 0) {
        color = "error";
        texto = `Vencido ${Math.abs(diasRestantes)} días`;
    }

    return { color, texto };
};

defineExpose({
    INIT
});
</script>

<style scoped>
.heightEncargo {
    height: calc(100vh - 203px);
}



/* .time-diligencia:hover {
    box-shadow: 0px 0px 5px 2px var(--color-sys-657DF9),
        0px 0px 10px 6px rgb(0 0 0 / 14%) !important;
}

.time-evento:hover {
    box-shadow: 0px 0px 5px 2px #17383f, 0px 0px 10px 6px hsla(0, 0%, 0%, 0.14) !important;
} */
</style>