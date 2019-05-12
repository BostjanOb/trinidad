window.Vue   = require('vue');
window.axios = require('axios');
import VueRouter from 'vue-router';

window.axios = axios;
window.Vue   = Vue;

Vue.use(VueRouter);

let token = document.head.querySelector('meta[name="csrf-token"]');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN']     = token.content;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

const routes = [
  {path: '/', name: 'dashboard', component: require('./pages/dashboard.vue').default},
  {path: '/sites', name: 'sites.index', component: require('./pages/sites/index.vue').default},
  {path: '/servers', name: 'servers.index', component: require('./pages/servers/index.vue').default},
  {path: '/servers/:id', name: 'servers.show', component: require('./pages/servers/show.vue').default},
  {path: '/users', name: 'users', component: require('./pages/users.vue').default},
];

const router = new VueRouter({
  routes,
});

const app = new Vue({
  el: '#app',
  router,
});
