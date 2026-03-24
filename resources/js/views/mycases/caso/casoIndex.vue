<template>
    <v-row>
        <v-col cols="12" md="4">
            <v-card variant="outlined">
                <v-card-title>CASO {{ id }}</v-card-title>
            </v-card>
        </v-col>
        <v-col cols="12" lg="8">
            <v-card variant="outlined">

            </v-card>
        </v-col>
    </v-row>
</template>
<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();

// 🔥 aquí obtienes el parámetro
const id = route.params.id;
</script>