<template>
    <v-col cols="12" class="pb-2 primary--text font-weight-bold">
        <v-icon size="smaller" color="primary">mdi-apps</v-icon>&nbsp;<small>ARCHIVOS ADJUNTOS</small>&nbsp;
    </v-col>
    <v-col cols="12" class="pb-2">
        <v-text-field v-model="searchArchivo" label="BUSCAR ARCHIVO..." color="primary" append-inner-icon="mdi-magnify"
            density="compact" variant="outlined" rounded clearable hide-details class="mb-2" />
    </v-col>
    <div class="heightEncargo d-flex flex-column">
        <div v-if="loading" class="flex-grow-1 d-flex align-center justify-center">
            <v-progress-circular color="primary" indeterminate size="48" />
        </div>
        <v-alert style="font-size: 12pt" color="primary" class="text-center" variant="outlined"
            v-else-if="archives.length == 0">
            <v-icon color="primary" size="50px">
                mdi-timeline-clock-outline
            </v-icon>
            <hr class="mt-2 mb-2" />
            NO HAY NINGUN ACTIVIDAD REGISTRADO POR EL MOMENTO
        </v-alert>
        <perfect-scrollbar v-else class="mr-n2 pr-3 flex-grow-1" :options="{ suppressScrollX: true }">
            <v-card v-for="item in filteredArchivos" :key="item.anex_exp_id" class="mr-1 mb-2"
                :style="archivo_seleccionado == item.anex_exp_id ? 'background-color: #d3f7ff !important' : ''"
                style="cursor: pointer; border: #1fabda 1.5px solid;" @click="showArchivActivity(item)" outlined
                elevation="0">
                <v-list-item class="primary--text">
                    <v-list-item-title>
                        <small class="font-weight-bold">{{ item.dili_descripcion ? 'ACT' : (item.dili_even_titulo ?
                            'EVEN ' : 'EST') }}</small>
                        &nbsp;
                        <small>
                            <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <span v-bind="props">
                                        {{ item.dili_descripcion || item.dili_even_titulo || item.estado_descripcion }}
                                    </span>
                                </template>
                                <small> {{ item.dili_descripcion || item.dili_even_titulo || item.estado_descripcion
                                }}</small>
                            </v-tooltip>
                        </small>
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        <small class="font-weight-bold">{{ item.anex_exp_nombre }}</small>
                    </v-list-item-subtitle>
                    <v-list-item-subtitle class="d-flex align-center">

                        <v-icon v-on="on" small :color="item.icono_extension[1]">
                            {{ item.icono_extension[0] }}
                        </v-icon>
                        <small class="ml-2"><strong>SUBIDO:</strong> {{ item.anex_exp_fecha }}</small>
                    </v-list-item-subtitle>
                    <v-list-item-action class="archivo-actions">
                        <!--   <v-icon color="error" @click.stop="deleteFileActivity(item)">mdi-delete</v-icon>
                            <v-icon color="primary"
                                @click.stop="DownLoadArchive('/' + item.anex_exp_ruta)">mdi-download</v-icon> -->
                    </v-list-item-action>
                </v-list-item>
            </v-card>
        </perfect-scrollbar>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

//DATOS
const expe_id = ref(null);
const expe_indi_id = ref(null);
const archives = ref([]);
const loading = ref(false);
const archivo_seleccionado = ref(null);
const searchArchivo = ref('');

//METHODS
const formatearFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString('es-PE', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

//COMPUTED
const filteredArchivos = computed(() => {
    if (!searchArchivo.value) return archives.value;

    const searchTerm = searchArchivo.value.toString().toLowerCase().trim();
    if (!isNaN(searchTerm)) {
        return archives.value.filter(item =>
            item.esta_id?.toString().includes(searchTerm) ||
            item.dili_id?.toString().includes(searchTerm)
        );
    }
    return archives.value.filter(item =>
        (item.dili_descripcion || "").toLowerCase().includes(searchTerm) ||
        (item.dili_even_titulo || "").toLowerCase().includes(searchTerm) ||
        (item.estado_descripcion || "").toLowerCase().includes(searchTerm) ||
        (item.anex_exp_nombre || '').toLowerCase().includes(searchTerm)
    );
});


//METODS    
const INIT = async (info) => {
    expe_id.value = info.expe_id;
    expe_indi_id.value = info.expediente_individual;
    GET_ARCHIVE();
};

const GET_ARCHIVE = async () => {
    loading.value = true;
    try {
        const res = await axios.post('/api/expediente/archives', {
            expe_id: expe_id.value,
            expe_indi_id: expe_indi_id.value
        });
        archives.value = res.data.data;
    } catch (error) {
        console.error('Error al obtener los archivos:', error);
    } finally {
        loading.value = false;
    }
};

const showArchivActivity = (item) => {
    archivo_seleccionado.value = item.anex_exp_id;
};

defineExpose({
    INIT
});
</script>
<style scoped>
.heightEncargo {
    height: calc(100vh - 313px);
}
</style>