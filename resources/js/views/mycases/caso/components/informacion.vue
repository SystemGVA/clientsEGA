<template>
    <div class="heightEncargo d-flex flex-column">
        <perfect-scrollbar v-if="expediente" class="mr-n2 pr-3 flex-grow-1" :options="{ suppressScrollX: true }">
            <span class="mb-1 d-flex align-center justify-center">
                <p class="mb-0 mr-1 primary--text">
                    <small>
                        <strong>N.I:</strong>&nbsp;{{
                            expediente.expe_correlativo
                        }}
                    </small>
                </p>
                <v-spacer />
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-icon color="error" dark v-bind="props">mdi-file-pdf-box</v-icon>
                    </template>
                    <span>REPORTE INDIVIDUAL</span>
                </v-tooltip>
            </span>
            <div class="div-info-caso py-1">
                <section class="pa-2" style="background-color: aliceblue; width: 100%; border-radius: 12px;">
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon color="primary" v-bind="props">mdi-account-star</v-icon>
                            </template>
                            <small>CLIENTE</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small>
                            {{ expediente.clientes }}
                        </small>
                    </div>
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props" color="red">mdi-target-account</v-icon>
                            </template>
                            <small>PARTE CONTRARIA</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small>
                            {{ expediente.clientes_contraria }}
                        </small>
                    </div>
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props">mdi-folder-key</v-icon>
                            </template>
                            <small>PRETENSION</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small>
                            {{ expediente.nombre_pretension }}
                            ({{ expediente.abreviatura_materia }})
                        </small>
                    </div>
                    <hr />
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props">mdi-image-filter-hdr</v-icon>
                            </template>
                            <small>ETAPA</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small v-if="expediente.estado_final == null">NINGUNO</small>
                        <small v-else>
                            {{ expediente.etapa_final }}
                        </small>
                    </div>
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props">mdi-gavel</v-icon>
                            </template>
                            <small>ESTADO</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small v-if="expediente.estado_final == null">NINGUNO</small>
                        <small v-else>
                            {{ expediente.estado_final }}
                            ({{ expediente.esta_fecha }})
                        </small>
                    </div>
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props">mdi-calendar-check</v-icon>
                            </template>
                            <small>ULTIMA DILIGENCIA EJECUTADA</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small v-if="expediente.diligencia_tipo_ejecutada == 0">
                            {{ expediente.ultima_diligencia_evento }}
                            ({{ expediente.dili_fechainicio }})
                        </small>
                        <small v-else-if="expediente.diligencia_tipo_ejecutada > 0">
                            {{ expediente.ultima_diligencia }}
                            ({{ expediente.dili_fechainicio }}
                            <small v-if="
                                expediente.dili_fechainicio !==
                                expediente.dili_fechacump
                            ">
                                - {{ expediente.dili_fechacump }} </small>)
                        </small>
                        <small v-else>: NINGUNO</small>
                    </div>
                    <hr />
                    <div class="div-info-caso">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props">mdi-chart-timeline</v-icon>
                            </template>
                            <small>DILIGENCIAS PROGRAMADAS</small>
                        </v-tooltip>
                        &nbsp;&nbsp;
                        <small>(
                            {{
                                expediente.cantidad_diligencia +
                                expediente.cantidad_eventos
                            }}
                            ) DILIGENCIAS
                        </small>
                    </div>
                    <div v-if="expediente.is_detenido == 1" class="div-info-caso d-flex justify-center mt-2"
                        style="color: white !important; background-color: black; border-radius: 12px;padding: 2px !important;">
                        <template v-if="expediente.is_detenido == 1">
                            <v-icon @click.stop="DetencionDeCaso(expediente)" color="yellow">
                                mdi-play
                            </v-icon>
                            &nbsp;
                        </template>
                        {{
                            expediente.motivo_detenido
                                ? expediente.motivo_detenido.toUpperCase()
                                : "NO COLOCARON MOTIVO DE DETENCIÓN"
                        }}
                    </div>
                </section>
            </div>

            <div class="div-info-caso pl-2 pt-1">

                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-icon v-bind="props">mdi-calendar</v-icon>
                    </template>
                    <small>FECHA DE CREACION</small>
                </v-tooltip>
                &nbsp;
                &nbsp;
                <small>
                    {{ formatearFecha(expediente.fecha_expediente) }}
                </small>
            </div>
            <div class="div-info-caso pt-1">
                <v-col cols="12" class="pb-0 pt-2 font-weight-bold primary--text">
                    <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>ESTADO</small>&nbsp;
                </v-col>
                <v-col cols="12" class="pb-0 pt-2">
                    <v-chip color="success" v-if="expediente.expe_estado === 0" size="small">
                        PROCESO
                    </v-chip>
                    <v-chip color="primary darken-3" v-else-if="expediente.expe_estado === 3" size="small">
                        CULMINADO
                    </v-chip>
                    <v-chip color="warning white-text" v-else-if="expediente.expe_estado === 2" size="small">
                        ARCHIVADO
                    </v-chip>
                    <v-chip color="error white--text" v-else-if="expediente.expe_estado === 1" size="small">
                        ELIMINADO
                    </v-chip>
                </v-col>
            </div>
            <div class="div-info-caso pt-1">
                <v-col cols="12" class="pb-0 pt-2 font-weight-bold primary--text">
                    <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>USUARIOS
                        INVOLUCRADOS</small>&nbsp;
                </v-col>
                <v-col cols="12" class="pb-0 pt-2">
                    <span>
                        <v-menu v-for="involucrado in expediente.involucrados" :key="involucrado.usua_id"
                            location="bottom" max-width="300">
                            <template #activator="{ props }">
                                <v-btn v-if="[0, 2].includes(involucrado.usua_expe_tipo)" v-bind="props" icon
                                    size="small" :color="involucrado.usua_expe_tipo == 0 ? 'lime' : 'primary'"
                                    class="mr-1">
                                    <v-avatar size="30">
                                        <img :src="involucrado.usua_foto" loading="lazy" />
                                    </v-avatar>
                                </v-btn>
                                <v-avatar v-else v-bind="props" size="31" class="mr-1">
                                    <img :src="involucrado.usua_foto" loading="lazy" />
                                </v-avatar>
                            </template>
                            <v-card class="px-3" rounded="lg">
                                <v-card-text class="text-center py-2">
                                    <v-avatar size="60" class="mx-auto">
                                        <img :src="involucrado.usua_foto" loading="lazy" />
                                    </v-avatar>
                                    <h4>{{ involucrado.nombre_completo }}</h4>
                                    <a class="py-2" :href="'mailto:' + involucrado.usua_email" target="_blank">
                                        <v-icon color="blue" class="mr-1" size="small">mdi-email-plus</v-icon>
                                        <small>{{ involucrado.usua_email }}</small>
                                    </a>
                                    <br />
                                    <v-chip size="small" class="mt-1" :color="roles[involucrado.usua_expe_tipo].color">
                                        {{ roles[involucrado.usua_expe_tipo].label }}
                                    </v-chip>
                                </v-card-text>
                            </v-card>
                        </v-menu>
                    </span>

                </v-col>
            </div>
            <div class="div-info-caso pt-1" v-if="expediente.expe_instruccion">
                <v-col cols="12" class="pb-0 pt-2 font-weight-bold primary--text">
                    <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>ABSTRACT</small>&nbsp;
                </v-col>
                <v-col cols="12" class="pb-0 pt-2">
                    <small v-html="expediente.expe_instruccion"></small>
                </v-col>
            </div>
        </perfect-scrollbar>
        <div v-else class="flex-grow-1 d-flex align-center justify-center">
            <v-progress-circular color="primary" indeterminate size="48" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

//DATOS
const expediente = ref(null);
const roles = {
    0: { label: "SUPERVISOR", color: "warning", icon: "mdi-account-eye" },
    1: { label: "INVOLUCRADO", color: "grey-darken-2", icon: "mdi-account" },
    2: { label: "RESPONSABLE", color: "primary", icon: "mdi-account-check" },
};
//METHODS


const INIT = (info) => {
    expediente.value = info;
};
const formatearFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString('es-PE', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};



defineExpose({
    INIT
});
</script>
<style scoped>
.heightEncargo {
    height: calc(100vh - 230px);
}
</style>