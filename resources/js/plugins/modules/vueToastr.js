// http://s4l1h.github.io/vue-toastr/global_options.html#overwrite-default-options-within-application
import Vue from 'vue'
// import plugin
import VueToastr from "vue-toastr";
// use plugin
Vue.use(VueToastr, {
    /* OverWrite Plugin Options if you need */
    defaultTimeout : 2000,
    defaultProgressBar: false,
    defaultPosition: "toast-bottom-right",
    defaultStyle: { 
      // "font-family": "Arial, Helvetica, sans-serif"
      'font-family': 'Roboto,sans-serif'
    },
});