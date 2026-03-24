<template>
    <v-card rounded="md" elevation="0">
        <v-card-text  v-if="encargo">
             <perfect-scrollbar class="mr-n2 pr-3 heightEncargo"  :options="{ suppressScrollX: true }">
                 <span class="mb-4 d-flex align-center justify-center">
                     <p class="mb-0 mr-1 primary--text">
                         <small>
                             <strong>ENCA:</strong>&nbsp;{{
                                 encargo.enca_id
                             }}
                         </small>
                     </p>
                     <v-spacer />
                     <v-tooltip bottom>
                         <template v-slot:activator="{ props }">
                             <v-icon color="error" dark v-bind="props">mdi-file-pdf-box</v-icon>
                         </template>
                         <span>REPORTE INDIVIDUAL</span>
                     </v-tooltip>
                 </span>
                 <div class="div-info-caso py-1">
                     <v-textarea variant="outlined" density="compact" hide-details color="primary"
                         v-model="encargo.enca_detalle" label="ENCARGO" rows="1" auto-grow />
                 </div>
                 <div class="div-info-caso py-1">
                     <v-select chips small-chips density="compact" label="MATERIA" color v-model="encargo.materia"
                         item-text="conc_nombre" item-value="conc_id" hide-details placeholder="MATERIA" variant="outlined"
                         rounded />
                 </div>
                 <div class="div-info-caso py-1">
                     <section class="pa-2" style="border: 0.4px solid #0006; width: 100%; border-radius: 12px;">
                         <strong class="d-flex justify-center align-center">
                             <small>
                                 <v-icon small>mdi-calendar</v-icon> PERIODO
                             </small>
                         </strong>
                         <small>
                             <strong>INICIO DE ACTIVIDAD:</strong>
                             {{ formatearFecha(encargo.enca_fechasol) }}
                         </small>
                         <small v-if="encargo.enca_estado === 2">
                             <br />
                             <strong>FIN DE ACTIVIDAD:</strong>
                             {{ formatearFecha(encargo.enca_fechaaten) }} </small>
                     </section>
                 </div>
                 <div class="div-info-caso py-1">
                     <v-col cols="12" class="pb-0 pt-2 font-weight-bold primary--text">
                         <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>ESTADO</small>&nbsp;
                     </v-col>
                     <v-col cols="12" class="pb-0 pt-2">
                         <template v-if="encargo.enca_estado === 1 || encargo.enca_estado === 0">
                             <!--  <v-chip :color="chipEstado.color" x-small>
                                 <v-icon x-small left>{{ chipEstado.icon }}</v-icon>
                                 {{ chipEstado.text }}
                             </v-chip> -->
                             <v-chip color="success" size="small">
                                 PROCESO
                             </v-chip>
                         </template>
                         <template v-else>
                             <v-chip color="primary darken-3" v-if="encargo.enca_estado === 2" size="small">
                                 CULMINADO
                             </v-chip>
                             <v-chip color="warning white-text" v-else-if="encargo.enca_estado === 3" size="small">
                                 ARCHIVADO
                             </v-chip>
                             <v-chip color="error white--text" v-else-if="encargo.enca_estado === 4" size="small">
                                 ELIMINADO
                             </v-chip>
                         </template>
                     </v-col>
                 </div>
                 <div class="div-info-caso py-1">
                     <v-col cols="12" class="pb-0 pt-2 font-weight-bold primary--text">
                         <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>USUARIOS
                             INVOLUCRADOS</small>&nbsp;
                     </v-col>
                     <v-col cols="12" class="pb-0 pt-2">
                         <span>
                             <v-menu v-for="involucrado in encargo.involucrados" :key="involucrado.usua_id" location="bottom"
                                 max-width="300">
                                 <template #activator="{ props }">
                                     <v-btn v-if="[0, 1].includes(involucrado.usen_tipo)" v-bind="props" icon size="small"
                                         :color="involucrado.usen_tipo == 0 ? 'lime' : 'primary'" class="mr-1">
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
                                         <v-chip size="small" class="mt-1" :color="roles[involucrado.usen_tipo].color">
                                             {{ roles[involucrado.usen_tipo].label }}
                                         </v-chip>
                                     </v-card-text>
                                 </v-card>
                             </v-menu>
                         </span>
     
                     </v-col>
                 </div>
                 <div class="div-info-caso py-1" v-if="encargo.enca_instruccion">
                     <v-col cols="12" class="pb-0 pt-2 font-weight-bold primary--text">
                         <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>ABSTRACT</small>&nbsp;
                     </v-col>
                     <v-col cols="12" class="pb-0 pt-2">
                         <small v-html="encargo.enca_instruccion"></small>
                     </v-col>
                 </div>
             </perfect-scrollbar>
        </v-card-text>
    </v-card>
</template>

<script setup>
//PROPS
const props = defineProps({
    encargo: Object
});
//DATOS
const roles = {
    0: { label: "SUPERVISOR", color: "warning", icon: "mdi-account-eye" },
    1: { label: "RESPONSABLE", color: "primary", icon: "mdi-account-check" },
    2: { label: "INVOLUCRADO", color: "grey-darken-2", icon: "mdi-account" },
};
//METHODS
const formatearFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString('es-PE', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};
</script>
<style scoped>
.heightEncargo {
    height: calc(100vh - 230px);
}
</style>