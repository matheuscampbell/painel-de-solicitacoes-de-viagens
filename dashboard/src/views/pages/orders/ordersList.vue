
<template>
    <v-card>
      <v-card-title class="border mb-4">
        Pedidos de Viagem
        <v-spacer />
        <v-progress-circular
          v-if="loading"
          indeterminate
          size="20"
          color="primary"
        />
      </v-card-title>

      <v-card-text>
        <v-row class="mb-1" align="center" justify="end" >
          <v-col cols="12" sm="auto">
            <NewOrder button-text="Novo Pedido + " />
          </v-col>
          <v-col cols="12" sm="4">
            <v-select
              v-model="filters.status"
              :items="statusItems"
              label="Status"
              @update:modelValue="load"
              clearable
              density="compact"
            />
          </v-col>
        </v-row>
        <v-data-table-server
          :items="orders"
          :headers="headers"
          :loading="loading"
          item-key="uuid"
          class="elevation-1"
          hover
          dense
          @update:itemsPerPage="(val) => { filters.per_page = val; load(page); }"
          :items-per-page-options="[5, 10, 25, 50, 100]"
          :items-length="total"
          @update:page="onPageChange"
        >
          <template #item.actions="{ item }">
            <v-btn small icon color="primary" @click="gotoDetails(item)">
              <v-icon>mdi-eye</v-icon>
            </v-btn>
          </template>
          <template #no-data>
            <div class="text-center pa-4">Nenhum pedido encontrado.</div>
          </template>
        </v-data-table-server>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-pagination
          v-model="page"
          :length="pageCount"
          @update:model-value="onPageChange"
        />
      </v-card-actions>
    </v-card>
</template>

<script>
import { useOrderStore } from "@/store/OrderStore";
import NewOrder from "@/components/order/NewOrder.vue";

export default {
  name: "OrdersList",
  components: {NewOrder},
  data() {
    return {
      orders: [],
      loading: false,
      page: 1,
      total: 0,
      maxPages: 1,
      filters: {
        status: "",
        origin: "",
        destination: "",
        created_from: "",
        created_to: "",
        travel_from: "",
        travel_to: "",
        per_page: 10
      },
      headers: [
        { title: "ID", key: "uuid", value: (v)=> v.uuid.slice(0, 8).toUpperCase() },
        { title: "Origem", key: "origin", value: "origin" },
        { title: "Destino", key: "destination", value: "destination" },
        { title: "Data Ida", key: "departure_date", value: (v) => v.departure_date ? new Date(v.departure_date).toLocaleDateString() : 'N/A' },
        { title: "Data Volta", key: "return_date", value: (v) => v.return_date ? new Date(v.return_date).toLocaleDateString() : 'N/A' },
        { title: "Status", key: "status", value: "status" },
        { title: "Criado Em", key: "created_at", value: (v)=> v.created_at ? new Date(v.created_at).toLocaleString() : '' },
        { title: "Ações", key: "actions", value: "actions", sortable: false }
      ],
      statusItems: [
        { title: "Todos", value: "" },
        { title: "Solicitado", value: "solicitado" },
        { title: "Aprovado", value: "aprovado" },
        { title: "Cancelado", value: "cancelado" }
      ]
    };
  },
  created() {
    useOrderStore().reset();
    this.load();
  },
  methods: {
    async load(page = 1) {
      this.loading = true;
      try {
        const store = useOrderStore();
        const params = { ...this.filters, page };

        await store.getAll(params);
        this.orders = store.orders;
        this.total = store.pages.total;
        this.page = store.pages.current_page;
      } finally {
        this.loading = false;
      }
    },
    onPageChange(newPage) {
      this.load(newPage);
    },
    gotoDetails(item) {
      location.href = `/orders/${item.uuid}`;
    }
  }
};
</script>

<style scoped>
</style>
