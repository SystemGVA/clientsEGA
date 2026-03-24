import { createRouter, createWebHistory } from "vue-router";

// layouts
import Login from "@/layouts/AuthLayout.vue";
import MainLayout from "@/layouts/MainLayout.vue";

// views
import Dashboard from "@/views/Dashboard.vue";
import Clientes from "@/views/mycases/index.vue";

// 🔥 store
import { useAuthStore } from "@/stores/auth";

// 🧩 rutas
const routes = [
    {
        path: "/login",
        name: "Login",
        component: Login,
        meta: { guest: true } // solo invitados
    },
    {
        path: "/",
        component: MainLayout,
        meta: { requiresAuth: true }, // requiere login
        children: [
            {
                path: "",
                name: "dashboard",
                meta: {
                    title: "Dashboard",
                },
                component: Dashboard
            },
            {
                path: "mycases",
                name: "mycases",
                meta: {
                    title: "Mis Casos",
                },
                component: Clientes
            },
            {
                path: "mycases/encargo/:id?",
                meta: {
                    title: "ENCA. INDIVIDUAL",
                },
                component: () => import("@/views/mycases/encargo/encargoIndex.vue"),
                name: "VerEncargoHash",
            },
            {
                path: "mycases/caso/:id?",
                meta: {
                    title: "CASO. INDIVIDUAL",
                },
                component: () => import("@/views/mycases/caso/casoIndex.vue"),
                name: "VerCasoHash",
            },
        ]
    },
    {
        path: "/:catchAll(.*)",
        name: "NotFound",
        redirect: () => {
            const auth = useAuthStore();
            return auth.isAuthenticated ? "/" : "/login";
        }
    }
];

// 🚀 router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// 🔐 GUARD GLOBAL
router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    console.log(to, "Authenticated:", auth.isAuthenticated);
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return next("/login");
    }
    if (to.meta.guest && auth.isAuthenticated) {
        return next("/");
    }
    next();
});

export default router;