<script setup>
import { ref, computed  } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2";


const router = useRouter();

const swt1 = ref(true);
const swt2 = ref(false);
const authStore = useAuthStore();

const user = computed(() => authStore.user);

const nombreFormateado = computed(() => {
  if (!user.value?.nombre_cliente) return '';

  return user.value.nombre_cliente
    .toLowerCase()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
});

const logout = async () => {
  const confirm = await Swal.fire({
    title: "¿Cerrar sesión?",
    text: "Se cerrará tu sesión actual",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, salir"
  });

  if (!confirm.isConfirmed) return;

  await authStore.logout();
  router.push("/login");
};

</script>

<template>
  <div class="pa-4">
    <h4 class="mb-n1">Cliente, <span class="font-weight-regular">{{ nombreFormateado }}</span></h4>
    <span class="text-subtitle-2 text-medium-emphasis">{{ user?.tipo_doc_texto }}: {{ user?.clie_doc }}</span>

    <v-divider></v-divider>
    <perfect-scrollbar style="height: calc(100vh - 300px); max-height: 115px">
      <!-- 
      <div class="bg-lightprimary rounded-md px-5 py-3 my-3">
        <div class="d-flex align-center justify-space-between">
          <h5 class="text-h5">Start DND Mode</h5>
          <div>
            <v-switch v-model="swt1" color="primary" hide-details></v-switch>
          </div>
        </div>
        <div class="d-flex align-center justify-space-between">
          <h5 class="text-h5">Allow Notifications</h5>
          <div>
            <v-switch v-model="swt2" color="primary" hide-details></v-switch>
          </div>
        </div>
      </div>

      <v-divider></v-divider> -->
      <v-list class="mt-3">
        <v-list-item color="secondary" rounded="md">
          <template v-slot:prepend>
            <v-icon size="20" class="mr-2">mdi-cog-outline</v-icon>
          </template>
          <v-list-item-title class="text-subtitle-2"> Account Settings</v-list-item-title>
        </v-list-item>
        <v-list-item @click="logout" color="secondary" rounded="md">
          <template v-slot:prepend>
            <v-icon size="20" class="mr-2">mdi-logout</v-icon>
          </template>
          <v-list-item-title class="text-subtitle-2"> Cerrar Sesión</v-list-item-title>
        </v-list-item>
      </v-list>
    </perfect-scrollbar>
  </div>
</template>
