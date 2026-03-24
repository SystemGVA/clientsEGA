import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
        // 🔥 importante
        /* host: "0.0.0.0",
        port: 5173,
        cors: true, 
        hmr: {
            host: '192.168.1.253',
        }, */
    },
});
