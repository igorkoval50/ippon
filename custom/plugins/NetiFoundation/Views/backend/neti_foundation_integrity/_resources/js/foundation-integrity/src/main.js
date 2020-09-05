// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.config.productionTip = false
Vue.use(BootstrapVue)

const swConfig = JSON.parse(document.getElementById('swConfig').innerText)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  components: { App },
  template: '<App v-bind:swConfig="swConfig"/>',
  data: {
    swConfig
  }
})
