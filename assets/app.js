
import {store} from "./store/store.js";
import { createApp } from 'vue'
import { createWebHistory, createRouter } from "vue-router";

import App from "./components/App.vue";
import Blank from "./components/Right/Blank";
import Right from "./components/Right/Right";



const routes = [
    {
        name: 'blank',
        path: '/',
        component: Blank
    },
    {
        name: 'conversation',
        path: '/conversation/:id',
        component: Right
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

store.commit("SET_USERNAME", document.querySelector('#app').dataset.username);

const app = createApp( App )
app.use(store)
app.mount('#app')
router.replace('/')
