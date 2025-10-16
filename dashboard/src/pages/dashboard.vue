<script setup>
import AnalyticsCongratulations from '@/views/dashboard/AnalyticsCongratulations.vue'

// 👉 Images
import chart from '@images/cards/chart-success.png'
import card from '@images/cards/credit-card-primary.png'
import paypal from '@images/cards/paypal-error.png'
import wallet from '@images/cards/wallet-info.png'
import OrdersList from "@/views/pages/orders/ordersList.vue";
</script>

<template>
  <VRow>
    <!-- 👉 Congratulations -->
    <VCol
      cols="12"
    >
      <AnalyticsCongratulations />
    </VCol>
    <v-col cols="12">
      <OrdersList />
    </v-col>
  </VRow>
</template>
