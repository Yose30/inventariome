/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');
window.numeral = require('numeral');

Vue.use(require('vue-resource'));

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.component('pagination', require('laravel-vue-pagination'));


// app.js
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// import Datepicker from 'vuejs-datepicker';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('editar-libro-component', require('./components/EditarLibroComponent.vue').default);
Vue.component('remision-component', require('./components/RemisionComponent.vue').default);
Vue.component('remisiones-component', require('./components/RemisionesComponent.vue').default);
Vue.component('pagos-almacen-component', require('./components/PagosAlmacenComponent.vue').default);
Vue.component('listado-component', require('./components/ListadoComponent.vue').default);
Vue.component('libros-component', require('./components/LibrosComponent.vue').default);
Vue.component('new-client-component', require('./components/NewClientComponent.vue').default);
Vue.component('new-libro-component', require('./components/NewLibroComponent.vue').default);
Vue.component('pagos-component', require('./components/PagosComponent.vue').default);
Vue.component('entradas-component', require('./components/EntradasComponent.vue').default);
Vue.component('clientes-component', require('./components/ClientesComponent.vue').default);
Vue.component('new-nota-component', require('./components/NewNotaComponent.vue').default);
Vue.component('vendidos-component', require('./components/VendidosComponent.vue').default);
Vue.component('adeudos-component', require('./components/AdeudosComponent.vue').default);
Vue.component('promociones-component', require('./components/PromocionesComponent.vue').default);
Vue.component('editar-entradas-component', require('./components/EditarEntradasComponent.vue').default);
Vue.component('devolucion-adeudos-component', require('./components/DevolucionAdeudosComponent.vue').default);
Vue.component('compras-component', require('./components/ComprasComponent.vue').default);
Vue.component('donaciones-component', require('./components/DonacionesComponent.vue').default);
Vue.component('movimientos-component', require('./components/MovimientosComponent.vue').default);
Vue.component('check-connection-component', require('./components/CheckConnectionComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});
