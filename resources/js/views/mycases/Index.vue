<template>
  <v-card variant="flat" rounded="md" class="px-4 py-3 mb-4">
    <v-row class="align-center">
      <v-col cols="12" md="4">
        <v-text-field color="primary" variant="outlined" density="compact" hide-details label="Buscar"
          v-model="filtros.buscar">
          <template #prepend>
            <v-menu bottom left rounded="xl" :close-on-content-click="false" :nudge-width="350" offset-y
              max-width="350px">
              <template v-slot:activator="{ props }">
                <v-btn color="primary" size="small" icon v-bind="props">
                  <v-icon>mdi-filter</v-icon>
                </v-btn>
              </template>
              <v-sheet class="my-2" elevation="1" rounded="md">
                <v-container fluid>
                  <v-row>
                    <v-col cols="12" class="py-1 text-center">
                      <small>
                        <strong class="primary--text">FILTROS</strong>
                      </small>
                    </v-col>
                  </v-row>
                </v-container>
              </v-sheet>
            </v-menu>
          </template>
        </v-text-field>
      </v-col>
      <v-col md="3" cols="12">
        <v-select color="primary" variant="outlined" density="compact" hide-details label="Materia" clearable chips
          :items="conceptoStore.materias" item-title="conc_nombre" item-value="conc_id" v-model="filtros.materia" />
      </v-col>
      <v-col md="2" cols="12">
        <v-chip-group v-model="filtros.listas" column multiple color="primary">
          <v-chip text="Encargos" size="small" variant="outlined" filter value="encargos" />
          <v-chip text="Casos" size="small" variant="outlined" filter value="casos" />
        </v-chip-group>
        <!-- <v-btn color="primary" size="small" icon @click="getCasos">
          <v-icon>mdi-magnify</v-icon>
        </v-btn> -->
      </v-col>
      <v-col md="3" cols="12">
        <v-select color="primary" variant="outlined" density="compact" hide-details label="Estado" :items="lEstado" chips
          v-model="filtros.estado">
          <template #append>
            <v-btn color="primary" size="small" icon @click="getCasos" :loading="loading">
              <v-icon>mdi-magnify</v-icon>
            </v-btn>
          </template>
        </v-select>
      </v-col>
    </v-row>
  </v-card>
  <v-card rounded="md" elevation="0" class="pa-4">
    <!-- <div class="d-flex align-center">
      <h4 class="text-h4">Mis Casos</h4>
      <div class="ml-auto">
        <v-menu transition="slide-y-transition">
          <template v-slot:activator="{ props }">
            <v-btn color="primary" size="small" icon rounded="sm" variant="text" v-bind="props">
              <v-icon>mdi-dots-horizontal</v-icon>
            </v-btn>
          </template>
<v-sheet rounded="md" width="150" class="elevation-10">
  <v-list>
    <v-list-item value="1">
      <v-list-item-title>Proceso</v-list-item-title>
    </v-list-item>
  </v-list>
</v-sheet>
</v-menu>
</div>
</div>
<v-divider />
<div class="mt-1"> -->
    <v-data-table :headers="headers" :items="items" height="calc(100vh - 280px)" fixed-header multi-sort :loading="loading" color="primary"
      mobile-breakpoint="md" @click:row="VIEW_CASE">
      <template v-slot:item.titulo="{ item }">
        <small>{{ item.titulo }}</small>
      </template>
      <template v-slot:item.estado_texto="{ item }">
        <small>{{ item.estado_texto }}</small>
      </template>
      <template v-slot:item.materia="{ item }">
        <v-chip dark size="x-small">{{ item.materia }}</v-chip>
      </template>
    </v-data-table>
    <!--  </div> -->
  </v-card>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useConceptoStore } from '@/stores/concepto';
import axios from "@/plugins/axios";

const router = useRouter();

const authStore = useAuthStore();
const conceptoStore = useConceptoStore();

//DATOS
const headers = ref([]);
const items = ref([]);
const loading = ref(false);
const filtros = ref({
  estado: 'En Proceso',
  buscar: '',
  listas: ["encargos", "casos"]
});
const lEstado = [
  'En Proceso',
  'Culminado',
  'Archivado',
  'Eliminado'
];

//MOUNTED
onMounted(() => {
  getCasos();
});

//METODOS
const getCasos = async () => {
  loading.value = true;

  try {
    const res = await axios.post('/api/mycases', {
      ...filtros.value,
      //clie_id: authStore.user.clie_id
    });

    headers.value = res.data.headers;
    items.value = res.data.items;

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const VIEW_CASE = (event, { item }) => {
  const expe = item.expediente;
  const id = item.encrypted_id;

  if (expe === 'encargo') {
    router.push({
      name: 'VerEncargoHash',
      params: { id }
    });
  } else if (expe === 'expediente') {
    router.push({
      name: 'VerCasoHash',
      params: { id }
    });
  }

};
</script>