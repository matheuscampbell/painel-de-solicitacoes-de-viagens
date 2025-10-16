<script setup>
import DefaultLayoutWithVerticalNav from './components/DefaultLayoutWithVerticalNav.vue'
import LoadingDialog from "@/components/LoadingDialog.vue";
import {useLoadingStore} from "@/store/LoadingStore";

const loadingStore = useLoadingStore();


</script>

<template>
  <loading-dialog :aways-open="true" v-if="loadingStore.isLoading"  ref="loadingGlobal"></loading-dialog>
  <DefaultLayoutWithVerticalNav>
    <RouterView />
  </DefaultLayoutWithVerticalNav>
</template>

<style lang="scss">
// As we are using `layouts` plugin we need its styles to be imported
@use "@layouts/styles/default-layout";
</style>
