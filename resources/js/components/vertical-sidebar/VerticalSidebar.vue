<template>
    <v-navigation-drawer left v-model="customizer.Sidebar_drawer" elevation="0" rail-width="75" mobile-breakpoint="lg"
        app class="leftSidebar" :rail="customizer.mini_sidebar" expand-on-hover>
        <div class="pa-5">
            <Logo />
        </div>
        <perfect-scrollbar class="scrollnavbar">
            <v-list class="pa-4">
                <template v-for="(item, i) in sidebarMenu" :key="i">
                    <NavGroup :item="item" v-if="item.header" :key="item.title" />
                    <v-divider class="my-3" v-else-if="item.divider" />
                    <NavCollapse class="leftPadding" :item="item" :level="0" v-else-if="item.children" />
                    <NavItem :item="item" v-else class="leftPadding" />
                </template>
            </v-list>
            <div class="pa-4 text-center">
                <v-chip color="inputBorder" size="small">{{ appVersion }}</v-chip>
            </div>
        </perfect-scrollbar>
        <!-- <template #append>
             <v-chip color="inputBorder" size="small">{{ appVersion }}</v-chip>
        </template> -->
    </v-navigation-drawer>
</template>
<script setup>
import { ref } from 'vue'
import { useCustomizerStore } from '@/stores/customizer';
import Logo from './Logo.vue';
import NavGroup from './NavGroup.vue';
import NavItem from './NavItem.vue';
import NavCollapse from './NavCollapse.vue';

const customizer = useCustomizerStore();

const sidebarMenu = ref([
    { header: 'Dashboard' },
    {
        title: 'General',
        //subCaption: 'Default Dashboard',
        icon: 'mdi-view-dashboard-outline',
        to: '/'
    },
    { divider: true },
    { header: 'Paginas' },
    /* {
        title: 'Authentication',
        icon: 'KeyIcon',
        to: '/auth',
        children: [
            {
                title: 'Login',
                icon: 'CircleIcon',
                to: '/login1'
            },
            {
                title: 'Register',
                icon: 'CircleIcon',
                to: '/register'
            }
        ]
    }, */
    {
        title: 'Mis Casos',
        icon: 'mdi-clipboard-list-outline',
        to: '/mycases'
    },
   /*  {
        title: 'Typography',
        icon: 'TypographyIcon',
        to: '/utils/typography'
    },

    {
        title: 'Documentation',
        icon: 'HelpIcon',
        to: 'https://codedthemes.gitbook.io/berry-vuetify/',
        type: 'external'
    } */
])

const appVersion = import.meta.env.VITE_APP_VERSION;

</script>
<style></style>