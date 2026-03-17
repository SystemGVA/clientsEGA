<template>
    <v-app-bar elevation="0" height="80">
        <v-btn class="hidden-md-and-down text-secondary" color="lightsecondary" icon rounded="sm" variant="flat"
            @click.stop="customizer.SET_MINI_SIDEBAR(!customizer.mini_sidebar)" size="small">
            <v-icon>mdi-menu</v-icon>
        </v-btn>
        <v-btn class="hidden-lg-and-up text-secondary ms-3" color="lightsecondary" icon rounded="sm" variant="flat"
            @click.stop="customizer.SET_SIDEBAR_DRAWER" size="small">
            <v-icon>mdi-menu</v-icon>
        </v-btn>

        <!-- search mobile -->
        <!-- <v-btn class="hidden-lg-and-up text-secondary ml-3" color="lightsecondary" icon rounded="sm" variant="flat"
            size="small" @click="searchbox">
            <SearchIcon size="17" stroke-width="1.5" />
        </v-btn>
        <v-sheet v-if="showSearch" class="search-sheet v-col-12">
            <Searchbar :closesearch="searchbox" />
        </v-sheet> -->

        <!-- ---------------------------------------------- -->
        <!-- Search part -->
        <!-- ---------------------------------------------- -->
        <!-- <v-sheet class="mx-3 v-col-3 v-col-xl-2 v-col-lg-4 d-none d-lg-block">
            <Searchbar />
        </v-sheet> -->

        <!---/Search part -->

        <v-spacer />
        <!-- ---------------------------------------------- -->
        <!---right part -->
        <!-- ---------------------------------------------- -->

        <!-- ---------------------------------------------- -->
        <!-- Notification -->
        <!-- ---------------------------------------------- -->
        <!-- <v-menu :close-on-content-click="false">
            <template v-slot:activator="{ props }">
                <v-btn icon class="text-secondary mx-3" color="lightsecondary" rounded="sm" size="small" variant="flat"
                    v-bind="props">
                    <v-icon>mdi-bell</v-icon>
                </v-btn>
            </template>
            <v-sheet rounded="md" width="330" elevation="12">
                <NotificationDD />
            </v-sheet>
        </v-menu> -->

        <!-- ---------------------------------------------- -->
        <!-- User Profile -->
        <!-- ---------------------------------------------- -->
        <v-menu :close-on-content-click="false">
            <template v-slot:activator="{ props }">
                <v-btn class="profileBtn text-primary" color="lightprimary" variant="flat" rounded="pill"
                    v-bind="props">
                    <v-avatar size="30" class="mr-2 py-2">
                        <img src="https://cdn.vuetifyjs.com/images/john.png" alt="Julia" />
                    </v-avatar>
                    <v-icon>mdi-cog-outline</v-icon>
                </v-btn>
            </template>
            <v-sheet rounded="md" width="330" elevation="12">
                <!-- <ProfileDD /> -->
            </v-sheet>
        </v-menu>
    </v-app-bar>
</template>
<script setup>
import { ref } from 'vue'
import { useCustomizerStore } from '@/stores/customizer';



const customizer = useCustomizerStore();
</script>