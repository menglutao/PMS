import {createRouter, createWebHistory} from 'vue-router';
import UserLogin from './components/UserLogin.vue';
import UserRegister from './components/UserRegister.vue';
import HealthLog from './components/HealthLog.vue';


const routes = [
    { path: '/', component: UserLogin},
    { path: '/login', name: 'UserLogin',component: UserLogin },
    { path: '/register', component: UserRegister },
    { path: '/health-log', name: 'HealthLog', component: HealthLog }

];


const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;