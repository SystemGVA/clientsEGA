//import './bootstrap'  
import { createApp } from 'vue'
import { createPinia } from 'pinia' 
import App from './App.vue' 
import router from './routes/route'
import vuetify from './plugins/vuetify'
import api, { axiosPlugin } from './plugins/axios';
import swalPlugin from './plugins/swal';
import '../scss/style.scss';
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar'
import { useAuthStore } from "@/stores/auth";
import '@mdi/font/css/materialdesignicons.css'
//import store from './store'




const app = createApp(App)
const pinia = createPinia() 

app.use(pinia) 
const auth = useAuthStore();
await auth.checkAuth();
app.use(router)
app.use(vuetify)
app.use(axiosPlugin);
app.use(swalPlugin);
app.use(PerfectScrollbarPlugin);


app.mount('#app')