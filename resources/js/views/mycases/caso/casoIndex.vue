<template>
    <v-row>
        <v-col cols="12" md="4">
            <v-card class="mb-2 pa-2" rounded="md" elevation="0" color="primary">
                <v-row class="pa-0 altura-menu" align="center" justify="space-around">
                    <!-- INFO -->
                    <v-tooltip location="bottom" text="INFORMACIÓN DE CASO">
                        <template #activator="{ props }">
                            <v-icon class="my_cursor" v-bind="props" size="small" @click="INIT_INFORMACION()">
                                mdi-home
                            </v-icon>
                        </template>
                    </v-tooltip>
                    <!-- ARCHIVOS -->
                    <v-tooltip location="bottom" text="ARCHIVOS ADJUNTOS">
                        <template #activator="{ props }">
                            <v-icon class="my_cursor" v-bind="props" size="small" @click="INIT_ARCHIVOS()">
                                mdi-file-document-multiple
                            </v-icon>
                        </template>
                    </v-tooltip>
                    <!-- GASTOS -->
                    <!-- <v-tooltip location="bottom" text="GASTOS">
            <template #activator="{ props }">
              <v-icon class="my_cursor" v-bind="props" size="small">
                mdi-account-cash
              </v-icon>
            </template>
          </v-tooltip> -->
                </v-row>
            </v-card>
            <v-card rounded="md" elevation="0">
                <v-card-text>
                    <informacion v-if="const_tabs.informacion == tab" ref="informacionRef" />
                    <archivos v-if="const_tabs.archivos == tab" ref="archivosRef" />
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="8">
            <actividades ref="actividadesRef" v-show="vista_actual == 0" />
        </v-col>
    </v-row>
</template>
<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

//COMPONENTS
import informacion from './components/informacion.vue';
import archivos from './components/archivos.vue';
import actividades from './components/actividades.vue';

const route = useRoute();
const id = route.params.id;

//REFS
const informacionRef = ref(null);
const archivosRef = ref(null);
const actividadesRef = ref(null);

//DATA
const vista_actual = ref(0);
const expediente = ref(null);
const loading = ref(false);
const tab = ref('informacion');
const const_tabs = ref({
    informacion: "informacion",
    archivos: "archivos",
    gastos: "gastos"
});
//MOUNTED
onMounted(() => {
    GET_EXPEDIENTE();
});

//METODS    
const GET_EXPEDIENTE = async () => {
    loading.value = true;

    try {
        const res = await axios.get(`/api/expediente/key/${id}`);
        expediente.value = res.data.expediente;
        await INIT_INFORMACION();
    } catch (error) {
        console.error('Error al obtener el expediente:', error);
    } finally {
        actividadesRef.value.INIT(expediente.value);
        loading.value = false;
    }
};

const INIT_INFORMACION = async () => {
    tab.value = const_tabs.value.informacion;

    await nextTick();
    informacionRef.value.INIT(expediente.value);
};
const INIT_ARCHIVOS = async () => {
    tab.value = const_tabs.value.archivos;

    await nextTick();
    archivosRef.value.INIT(expediente.value);
};

</script>

<style scoped>
.my_cursor {
    cursor: pointer;
}
</style>
