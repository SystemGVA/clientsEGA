import axios from "axios";

const api = axios.create({
    baseURL: "/",
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
});

api.interceptors.response.use(
    response => response,
    error => {

        let message = "Error inesperado";

        if (error.response) {
            const status = error.response.status;

            if (status === 401) {
                message = error.response.data?.error || "No autorizado";
            } else if (status === 403) {
                message = error.response.data?.error || "Acceso denegado";
            }
        }

        console.error("AXIOS ERROR:", message);

        return Promise.reject(error);
    }
);

export default api;

export const axiosPlugin = {
    install(app) {
        app.config.globalProperties.$axios = api;
    }
};