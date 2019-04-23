
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue'

import Vuelidate from "vuelidate"
Vue.use(Vuelidate)

import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrashAlt, faEdit } from '@fortawesome/free-regular-svg-icons'

library.add(
    faTrashAlt, faEdit,
);

import { FontAwesomeIcon, FontAwesomeLayers } from '@fortawesome/vue-fontawesome'
Vue.component('FontAwesomeIcon', FontAwesomeIcon)
Vue.component('FontAwesomeLayers', FontAwesomeLayers)

import Datetime from 'vue-datetime'
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css'

Vue.use(Datetime);

// const files = require.context('./components', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

import App from './views/App'
import store from './store/index'
import router from './routes'

const app = new Vue({
    el: '#app',
    components: { App },
    store,
    router,
});
