<template>
  <v-container>
    <v-card>
      <v-card-title>Dashboard CLIEGA</v-card-title>
      <v-card-text>
        Graficas sobre metricas importantes de casos y encargos
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>

import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

const auth = useAuthStore();
const router = useRouter();

const logout = async () => {
  const confirm = await Swal.fire({
    title: "¿Cerrar sesión?",
    text: "Se cerrará tu sesión actual",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, salir"
  });

  if (!confirm.isConfirmed) return;

  await auth.logout();

  router.push("/login");
};
</script>