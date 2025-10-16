<script>
export default {
  name: 'LoadingDialog',
  props: {
    message: {
      type: String,
      default: 'Carregando...',
    },
    awaysOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      dialog: this.awaysOpen,
    }
  },
  methods: {
    close() {
      this.dialog = false
    },
    open() {
      this.dialog = true
    },
  },
}
</script>

<template>
  <VDialog
    v-model="dialog"
    hide-overlay
    width="300"
    persistent
    class="dialog-z-index"
  >
    <VCard
      color="primary"
      dark
      rounded="lg"
    >
      <VCardText>
        {{ message }}
        <VProgressLinear
          indeterminate
          color="white"
          class="mb-0"
        />
      </VCardText>
    </VCard>
  </VDialog>
</template>
<style scoped>
.dialog-z-index {
  z-index: 1000000000000 !important;
}
</style>

