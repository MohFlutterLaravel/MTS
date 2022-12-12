require('./bootstrap');
import { createApp } from 'vue';
import router from './router'
window.router = router



// Sweet Alert start 
import Swal from 'sweetalert2'
window.Swal = Swal;

const Sweet = Swal.mixin({ 
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});
window.Sweet = Sweet;
// Sweet Alert End 

// vue-toastification Alert Start 
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
const props = {
  transition: "Vue-Toastification__slideBlurred",
  maxToasts: 20,
  newestOnTop: true
};
// vue-toastification Alert end 

// Import Vuex store
import { store } from './Helpers/store';
window.store = store

//Import PrimeVue
import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/saga-blue/theme.css'       //theme
import 'primevue/resources/primevue.min.css'                 //core css
import 'primeicons/primeicons.css'                           //icons

// import vue-select
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

// Import Notification Class
import Notification from './Helpers/Notification';
window.Notification = Notification



import VueLoading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const app = createApp()
.component("v-select", vSelect)
.use(store)
.use(router)
.use(PrimeVue)
.use(VueLoading)
.use(Toast, props);
const mountedApp = app.mount('#app');

