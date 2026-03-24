<template>
  <v-row>
    <v-col cols="12" md="4">
      <v-card class="mb-2 pa-2" rounded="md" elevation="0"  color="primary">
          <v-row class="pa-0 altura-menu" align="center" justify="space-around">
            <!-- INFO -->
            <v-tooltip location="bottom" text="INFORMACIÓN DE CASO">
              <template #activator="{ props }">
                <v-icon v-bind="props" size="small">
                  mdi-home
                </v-icon>
              </template>
            </v-tooltip>
            <!-- ARCHIVOS -->
            <v-tooltip location="bottom" text="ARCHIVOS ADJUNTOS">
              <template #activator="{ props }">
                <v-icon v-bind="props" size="small">
                  mdi-file-document-multiple
                </v-icon>
              </template>
            </v-tooltip>
            <!-- GASTOS -->
            <v-tooltip location="bottom" text="GASTOS">
              <template #activator="{ props }">
                <v-icon v-bind="props" size="small">
                  mdi-account-cash
                </v-icon>
              </template>
            </v-tooltip>
          </v-row>
      </v-card>
      <informacion :encargo="encargo" />
    </v-col>
    <v-col cols="12" md="8">
      <actividades :encargo="encargo" />
    </v-col>
  </v-row>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

//COMPONENTS
import informacion from './components/informacion.vue';
import actividades from './components/actividades.vue';

const route = useRoute();
const id = route.params.id;

//DATA
const encargo = ref(null);
const loading = ref(false);

//MOUNTED
onMounted(() => {
  GET_ENCARGO();
});

//METODS    
const GET_ENCARGO = async () => {
  loading.value = true;

  try {
    const res = await axios.get(`/api/encargo/key/${id}`);

    encargo.value = res.data.encargo;
  } catch (error) {
    console.error('Error al obtener el encargo:', error);
  } finally {
    loading.value = false;
  }
};



</script>