/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
import '@core/scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@styles/styles.scss'
import { createPinia } from 'pinia'
import { createApp } from 'vue'
import { useLoadingStore } from "@/store/LoadingStore";


import MaskDirective from "@/plugins/mask";

loadFonts()

// Create vue app
const app = createApp(App)



// Use plugins
vuetify.locale = {
    t: (key, ...params) => i18n.t(key, params),
}
app.use(vuetify)
const pinia = createPinia()
app.use(pinia)
app.use(router)

app.use(MaskDirective);
app.config.performance = true





var loading = useLoadingStore()

app.config.globalProperties.$showLoading = function() {
    loading.showLoading()
};

app.config.globalProperties.$hideLoading = function() {
    loading.hideLoading()
};




// Mount vue app
app.mount('#app')
