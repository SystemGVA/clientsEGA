<template>
    <v-card rounded="md" elevation="0">
        <v-card-title class="px-3 py-2">
            <strong class="primary--text mr-2">
                <small>ACTIVIDADES DE ENCARGO</small>
            </strong>
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
            <v-spacer></v-spacer>
        </v-card-title>
        <v-card-text v-if="actividades">
            <perfect-scrollbar class="mr-n2 pr-3 heightEncargo" :options="{ suppressScrollX: true }">
                <v-alert style="font-size: 12pt" color="primary" class="text-center" variant="outlined"
                    v-if="actividades.length == 0">
                    <v-icon color="primary" size="50px">
                        mdi-timeline-clock-outline
                    </v-icon>
                    <hr class="mt-2 mb-2" />
                    NO HAY NINGUN ACTIVIDAD REGISTRADO POR EL MOMENTO
                </v-alert>
                <v-timeline v-else side="end" density="compact">
                    <v-timeline-item class="ma-1" v-for="item in actividades" :key="item.acti_id" size="large">
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
                        <v-card :class="isEvent(item) ? 'time-evento' : 'time-diligencia'" elevation="10" rounded="md"
                            size="large" density="compact" :outlined="!isEvent(item)" :loading="item.loading">
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
                                                <!-- <small>#{{ item.acti_id }} </small>&nbsp; -->
                                                <strong>
                                                    <small>
                                                        {{
                                                            (item.acti_descripcion || "Sin descripción").toUpperCase()
                                                        }}
                                                    </small>
                                                    &nbsp;
                                                    <template>
                                                        <!--  <span v-if="item.total_gastos > 0" class="primary--text"
                                                            style="position: absolute; transform: translate(14px, -8px);">
                                                            <small>
                                                                {{ item.total_gastos }}
                                                            </small>
                                                        </span> -->
                                                        <v-icon size="small" color="primary">
                                                            mdi-cart
                                                        </v-icon>
                                                    </template>
                                                    &nbsp;
                                                    <v-chip
                                                        v-if="item.acti_cumplimiento == 1 || item.acti_cumplimiento == 3"
                                                        :color="isEvent(item) ? 'secondary' : 'primary'" size="x-small"
                                                        text-color="white">
                                                        <v-icon dark start>mdi-clock-time-eight-outline</v-icon>
                                                        {{ revertirFormatoTiempo(item.tiempo_ejecucion) }}
                                                    </v-chip>
                                                </strong>
                                                &nbsp;
                                                <v-badge v-if="item.total_archivos > 0" :content="item.total_archivos"
                                                    :value="item.total_archivos" color="primary" bordered>
                                                    <v-icon> mdi-paperclip </v-icon>
                                                </v-badge>
                                            </v-col>
                                            <v-col cols="3" lg="2" class="pt-0 pb-0">
                                                <small>
                                                    <strong>FECHA</strong>
                                                </small>
                                            </v-col>
                                            <v-col cols="9" lg="10" class="pt-0 pb-0">
                                                <small>
                                                    <span style="color: red !important;"
                                                        v-if="item.acti_cumplimiento == 0 && item.acti_fecha_last_reprog != '' && item.acti_fecha_last_reprog != null">
                                                        <del>{{
                                                            formatearFecha(
                                                                item.acti_fecha_realizado
                                                            )
                                                        }}</del>&nbsp;&nbsp;-&nbsp;
                                                    </span>
                                                    {{
                                                        formatearFecha(item.acti_fechauser) ===
                                                            formatearFecha(item.acti_fechalimite)
                                                            ? formatearFecha(item.acti_fechalimite)
                                                            : `${formatearFecha(item.esta_fecha_dili_fechainicio)} -
                                                    ${formatearFecha(item.esta_fecha_dili_fechacump)}`
                                                    }}

                                                    &nbsp;
                                                    <span v-if="item.acti_cumplimiento == 0">
                                                        <v-chip :color="calcularEstiloChip(item).color" size="x-small"
                                                            text-color="white">
                                                            {{ calcularEstiloChip(item).texto }}
                                                        </v-chip>
                                                    </span>
                                                </small>
                                            </v-col>
                                            <template v-if="
                                                item.diligencia_status == 3 || item.diligencia_status == 4
                                            ">
                                                <v-col cols="3" lg="2" class="pt-0 pb-0">
                                                    <small>
                                                        <strong style="color: red"> SUSTENTO </strong>
                                                    </small>
                                                </v-col>
                                                <v-col cols="9" lg="10" class="pt-0 pb-0">
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
                </v-timeline>
            </perfect-scrollbar>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
//PROPS
const props = defineProps({
    encargo: Object
});

//DATA
const actividades = ref([]);
const loading = ref(false);
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


//WATCH
watch(() => props.encargo, (newVal) => {
    if (newVal?.enca_id) {
        GET_ACTIVIDADES();
    }
});


//METODS    
const isEvent = (item) => {
    return item.dili_tipo == 0;
};

const tooltipStatus = (item) => {
    const status = activityStatuses.find(
        (s) => s.value === item.acti_cumplimiento
    );
    return status || null;
};

const currentTooltip = (item) => {
    const tooltips = {
        1: { icon: "mdi-bullseye-arrow", text: "Seguimiento" },
        2: { icon: "mdi-flash", text: "Escrito menor" },
        3: { icon: "mdi-chart-timeline-variant", text: "Reportes o requerimientos" },
        4: { icon: "mdi-chart-donut-variant", text: "Escrito mayor" },
        5: { icon: "mdi-calendar-clock", text: "Evento" },
    };
    return tooltips[item.acti_nivel] || null;
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
const diasRestantesFn = (item) => {
    if (!item.acti_fechalimite) return 0;
    const fechaLimite = new Date(item.acti_fechalimite);
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



const GET_ACTIVIDADES = async () => {
    console.log('Obteniendo actividades para encargo ID:', props.encargo.enca_id);
    if (!props.encargo?.enca_id) return;

    loading.value = true;

    try {
        const res = await axios.post(`/api/encargo/actividad`, {
            id: props.encargo.enca_id
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
</script>

<style scoped>
.heightEncargo {
    height: calc(100vh - 230px);
}



/* .time-diligencia:hover {
    box-shadow: 0px 0px 5px 2px var(--color-sys-657DF9),
        0px 0px 10px 6px rgb(0 0 0 / 14%) !important;
}

.time-evento:hover {
    box-shadow: 0px 0px 5px 2px #17383f, 0px 0px 10px 6px hsla(0, 0%, 0%, 0.14) !important;
} */
</style>