<template>
  <v-locale-provider>
    <v-app theme="PurpleTheme"
      :class="[customizer.fontTheme, customizer.mini_sidebar ? 'mini-sidebar' : '', customizer.inputBg ? 'inputWithbg' : '']">
      <AppBar />
      <NavigationDrawer />
      <v-main>
        <v-container fluid class="page-wrapper">
          <div>  
            <RouterView />
          </div>
        </v-container>
        <!-- <v-container fluid class="pt-0">
          <div>
            <v-footer class="px-0 footer mt-2">
              <v-row justify="center" align="center" no-gutters>
                <v-col cols="12" sm="6">
                  <p class="text-body-1 mb-0 text-sm-left text-center">
                    Berry ♥ crafted by Team
                    <a href="https://themeforest.net/user/codedthemes" class="text-darkText text-decoration-none"
                      target="_blank">Codedthemes</a>
                  </p>
                </v-col>
                <v-col class="text-sm-right text-center" cols="12" sm="6">

                </v-col>
              </v-row>
            </v-footer>
          </div>
        </v-container> -->
      </v-main>
    </v-app>
  </v-locale-provider>
</template>

<script setup>
import { onMounted, watch } from 'vue';

import AppBar from '@/components/AppBar.vue'
import NavigationDrawer from '@/components/vertical-sidebar/VerticalSidebar.vue'
import { useCustomizerStore } from '@/stores/customizer';
import { useConceptoStore } from '@/stores/concepto';
import { useRoute } from 'vue-router';

const route = useRoute();

const customizer = useCustomizerStore();
const conceptoStore = useConceptoStore();

onMounted(() => {
  //document.title = `${route.meta.title } - CLIEGA`;
  conceptoStore.LOAD_MATERIA({ conc_prefijo: 5 });
});
/* watch(
  () => route.meta.title,
  (title) => {
    document.title = title
      ? `${title} - CLIEGA`
      : "CLIEGA";
  }
); */
</script>