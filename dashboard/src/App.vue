<script setup>
import { hexToRgb } from '@layouts/utils'
import { useTheme } from 'vuetify'
import { watch, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from "@/store/UserStore"
import router from "@/router";
import Swal from "sweetalert2";
const { global } = useTheme()
const route = useRoute()

const userStore = useUserStore()

watch( () => route.path,  () => {
  if (route.path !== "/login" && route.path !== "/register") {
    userStore.checkToken().catch( () => {
      Swal.fire({
        title: "Ooops!",
        text: "Você não está logado!, faça login para continuar",
        icon: "error",
        confirmButtonText: "Ok",
      });
      router.push({ path: "/login" })
    })
  }
})



</script>

<template>
  <VApp :style="`--v-global-theme-primary: ${hexToRgb(global.current.value.colors.primary)}`">
    <RouterView />
  </VApp>
</template>

<style>
.swal2-container {
  z-index: 999999999999;
}
</style>
