import './bootstrap';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.css'
import '../views/vue/assets/scss/app.css'
import '../views/vue/assets/scss/bootstrap_custom.css'
import '../views/vue/assets/scss/chat.css'
// import './assets/js/app.js'
import axios from "axios";
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from '../views/vue/App.vue'
import router from '../views/vue/router'
import Echo from "laravel-echo";
const app = createApp(App)
// window.echo =new Echo({
//     broadcaster: 'Pusher',
//     key:import.meta.env.VUE_APP_PUSHER_APP_KEY,
//     wsHost:'127.0.0.1',
//     wsPort:6001,
//     cluster:"mt1",
//     disableStats:true,
//     forceTLS:false

// })
app.use(createPinia())
app.use(router)

app.mount('#app')
console.log('---dev---')