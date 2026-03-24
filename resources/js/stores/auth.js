import { defineStore } from "pinia";
import axios from "@/plugins/axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        isAuthenticated: false,
        loading: false
    }),

    actions: {
        async checkAuth() {
            this.loading = true;

            try {
                const res = await axios.get('/me');
                this.user = res.data;
                this.isAuthenticated = true;
            } catch (error) {
                this.user = null;
                this.isAuthenticated = false;
            } finally {
                this.loading = false;
            }
            console.log("User authenticated:", this.isAuthenticated);

        },

        async login(data) {
            await axios.post('/login', data);
            await this.checkAuth();
        },

        async logout() {
            try {
                await axios.post('/logout');
            } catch (e) {

            }

            this.user = null;
            this.isAuthenticated = false;
        }
    }
});