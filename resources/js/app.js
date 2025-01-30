import './bootstrap';
import { createApp } from 'vue';
import { createWebHistory, createRouter } from 'vue-router';
import routes from './routes';
import App from './App.vue';

const router = createRouter({
    history: createWebHistory(),
    routes,
})

createApp({})
    .use(router)
    .component('App', App)
    .mount('#app');
