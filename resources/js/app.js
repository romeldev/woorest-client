require('./bootstrap');

window.Vue = require('vue')

import './plugins'

import Mixins from './mixins'
Vue.mixin(Mixins);

Vue.component('example-component', require('./components/ExampleComponent.vue').default)

Vue.component('api-test', require('./components/ApiTest.vue').default)

const app = new Vue({
    el: '#app',
});
