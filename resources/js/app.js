//import './bootstrap'  
import { createApp } from 'vue'
import { createPinia } from 'pinia' 
import App from './App.vue' 
import router from './routes/route'
import vuetify from './plugins/vuetify'
import '../scss/style.scss';
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar'

import '@mdi/font/css/materialdesignicons.css'
//import store from './store'




const app = createApp(App)
const pinia = createPinia() 
//app.use(store)
app.use(pinia) 
app.use(router)
app.use(vuetify)


app.use(PerfectScrollbarPlugin);


app.mount('#app')