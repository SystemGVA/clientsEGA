import { defineStore } from "pinia";
import axios from "@/plugins/axios";

export const useConceptoStore = defineStore("concepto", {
    state: () => ({
        materias: [],
        loading: false
    }),

    actions: {
        async LOAD_MATERIA(filtros = {}) {
            //if (this.materias.length) return;

            this.loading = true;
            try {
                const res = await axios.post('/api/subject', filtros);
                this.materias = res.data;
            } catch (error) {
                console.error("Error cargando materias:", error);
            } finally {
                this.loading = false;
            }
        },


    }
});