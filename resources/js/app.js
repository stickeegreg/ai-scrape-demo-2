import './bootstrap';
import { createApp } from 'vue';
import { createMemoryHistory, createRouter } from 'vue-router';
import routes from './routes';
import App from './App.vue';

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

createApp({})
    .use(router)
    .component('App', App)
    .mount('#app');
