import { createRouter, createWebHistory } from "vue-router";
import Login from "@/layouts/AuthLayout.vue";
import MainLayout from "@/layouts/MainLayout.vue";
import Dashboard from "@/views/Dashboard.vue";
import Clientes from "@/views/Clientes.vue";

const routes = [
    {
        path: "/login",
        name: "Login",
        component: Login,
    },
    {
        path: "/",
        component: MainLayout, // aquí usas tu layout
        children: [
            { path: "", name: 'Dashboard', component: Dashboard },
            { path: "/clientes", component: Clientes },
        ],
        meta: { requiresAuth: true },
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
