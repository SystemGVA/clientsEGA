<template>
  <v-locale-provider>
    <v-app theme="PurpleTheme"
      :class="[customizer.fontTheme, customizer.mini_sidebar ? 'mini-sidebar' : '', customizer.inputBg ? 'inputWithbg' : '']">
      <AppBar />
      <NavigationDrawer />
      <v-main>
        <v-container fluid class="page-wrapper">
          <RouterView />
        </v-container>
      </v-main>
    </v-app>
  </v-locale-provider>
</template>

<script setup>
import AppBar from '@/components/AppBar.vue'
import NavigationDrawer from '@/components/vertical-sidebar/VerticalSidebar.vue'

import { useCustomizerStore } from '@/stores/customizer';
const customizer = useCustomizerStore();

</script>