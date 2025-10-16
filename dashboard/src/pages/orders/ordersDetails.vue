<template>

    <v-btn text class="mb-4" @click="goBack">
      <v-icon left>mdi-arrow-left</v-icon>
      Voltar
    </v-btn>

    <v-card elevation="2" class="rounded-lg">
      <v-card-title class="d-flex align-center">
        <div class="d-flex align-center">
          <v-avatar color="primary" size="36" class="mr-3" v-if="order && order.requester">
            <span class="white--text">
              {{ (order.requester.name || 'U')?.charAt(0)?.toUpperCase() }}
            </span>
          </v-avatar>
          <div>
            <div class="text-h6">Detalhes do Pedido</div>
            <div class="subtitle-2 grey--text" v-if="order && order.requester">
              Solicitante: {{ order.requester.name }}
            </div>
          </div>
        </div>
        <v-spacer />
        <v-chip v-if="order" :color="statusColor" text-color="white" class="font-weight-medium">
          <v-icon left small>
            {{ statusIcon }}
          </v-icon>
          {{ statusLabel }}
        </v-chip>
        <v-progress-circular v-if="loading" indeterminate size="20" color="primary" class="ml-3" />
      </v-card-title>

      <v-divider />

      <v-card-text>
        <div v-if="loading">
          <v-skeleton-loader type="article, list-item, list-item, list-item, actions" class="mb-4" />
          <v-skeleton-loader type="image, list-item, list-item, list-item" />
        </div>

        <div v-else-if="!order">
          <v-alert type="error" dense>Pedido não encontrado.</v-alert>
        </div>

        <div v-else>
          <v-row dense>
            <!-- Coluna principal -->
            <v-col cols="12" md="7">
              <v-card outlined class="mb-4">
                <v-card-title class="py-2 justify-center align-center text-center">
                  <v-icon left color="primary">mdi-airplane</v-icon>
                  Viagem
                  <v-spacer />
                  <v-chip small class="mr-2" color="blue lighten-5" text-color="blue darken-2">
                    <v-icon left small>mdi-map-marker</v-icon>
                    {{ order.origin }}
                  </v-chip>
                  <v-icon small class="mx-1 grey--text">mdi-arrow-right</v-icon>
                  <v-chip small color="green lighten-5" text-color="green darken-2">
                    <v-icon left small>mdi-map-marker</v-icon>
                    {{ order.destination }}
                  </v-chip>
                </v-card-title>
                <v-divider />

                <v-row no-gutters align-content="center" justify="space-between">
                  <v-col cols="auto">
                    <v-list dense two-line>
                      <v-list-item>
                        <v-list-item-avatar>
                          <v-icon color="indigo">mdi-calendar-start</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                          <v-list-item-title>Data de ida</v-list-item-title>
                          <v-list-item-subtitle>{{ formatDate(order.departure_date) }}</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-col>
                  <v-col cols="auto" v-if="durationDays !== null">
                    <v-list dense two-line>
                      <v-list-item>
                        <v-list-item-avatar>
                          <v-icon color="teal">mdi-timer-sand</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                          <v-list-item-title>Duração</v-list-item-title>
                          <v-list-item-subtitle>{{ durationDays }} dia(s)</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-col>
                  <v-col cols="auto">
                    <v-list dense two-line>
                      <v-list-item>
                        <v-list-item-avatar>
                          <v-icon color="indigo">mdi-calendar-end</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                          <v-list-item-title>Data de retorno</v-list-item-title>
                          <v-list-item-subtitle>{{ formatDate(order.return_date) }}</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-col>

                </v-row>
              </v-card>

              <v-card outlined class="mb-4">
                <v-card-title class="py-2">
                  <v-icon left color="amber darken-2">mdi-note-text-outline</v-icon>
                  Observações
                </v-card-title>
                <v-divider />
                <v-card-text>
                  <v-alert v-if="order.notes" type="info" outlined dense class="mb-0">
                    {{ order.notes }}
                  </v-alert>
                  <v-alert v-else type="warning" outlined dense class="mb-0">
                    Nenhuma observação adicionada.
                  </v-alert>
                </v-card-text>
              </v-card>

              <v-card outlined v-if="isAdmin && (order.status === 'solicitado' || order.status === 'pendente')">
                <v-card-title class="py-2">
                  <v-icon left color="teal">mdi-comment-text-outline</v-icon>
                  Anotação interna
                </v-card-title>
                <v-divider />
                <v-card-text>
                  <v-textarea
                    v-model="annotation"
                    label="Adicionar anotação (opcional)"
                    rows="3"
                    outlined
                    auto-grow
                  />
                  <div
                    class="mt-2 d-flex"
                  >
                    <v-btn
                      color="success"
                      class="me-2"
                      :loading="updating"
                      :disabled="updating"
                      @click="changeStatus('aprovado')"
                    >
                      <v-icon left>mdi-check</v-icon>
                      Aprovar
                    </v-btn>
                    <v-btn
                      color="error"
                      :loading="updating"
                      :disabled="updating"
                      @click="changeStatus('cancelado')"
                    >
                      <v-icon left>mdi-close-circle</v-icon>
                      Cancelar
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <!-- Coluna lateral -->
            <v-col cols="12" md="5">
              <v-card outlined class="mb-4">
                <v-card-title class="py-2">
                  <v-icon left color="purple">mdi-information-outline</v-icon>
                  Detalhes
                </v-card-title>
                <v-divider />
                <v-list dense>
                  <v-list-item>
                    <v-list-item-content>
                      <v-list-item-title class="font-weight-bold">UUID</v-list-item-title>
                      <v-list-item-subtitle class="d-flex align-center">
                        <span class="uuid mr-2">{{ order.uuid || order.id }}</span>
                        <v-tooltip top v-model="copyUuidTooltip">
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn icon small v-bind="attrs" v-on="on" @click="copyUuid">
                              <v-icon small>mdi-content-copy</v-icon>
                            </v-btn>
                          </template>
                          <span>Copiado</span>
                        </v-tooltip>
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>

                  <v-divider />

                  <v-list-item>
                    <v-list-item-content>
                      <v-list-item-title class="font-weight-bold">Status</v-list-item-title>
                      <v-list-item-subtitle>
                        <v-chip :color="statusColor" small dark>
                          <v-icon left small>{{ statusIcon }}</v-icon>
                          {{ statusLabel }}
                        </v-chip>
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>

                  <v-list-item>
                    <v-list-item-content>
                      <v-list-item-title class="font-weight-bold">Criado em</v-list-item-title>
                      <v-list-item-subtitle>{{ formatDateTime(order.created_at || order.createdAt) }}</v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>

                  <v-list-item>
                    <v-list-item-content>
                      <v-list-item-title class="font-weight-bold">Atualizado em</v-list-item-title>
                      <v-list-item-subtitle>{{ formatDateTime(order.updated_at || order.updatedAt) }}</v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-card>

              <v-card outlined>
                <v-card-title class="py-2">
                  <v-icon left color="deep-purple accent-2">mdi-timeline-clock-outline</v-icon>
                  Histórico de status
                </v-card-title>
                <v-divider />
                <v-card-text>
                  <v-timeline dense align-top v-if="(order.status_history || []).length">
                    <v-timeline-item
                      v-for="(h, i) in order.status_history"
                      :key="h.uuid"
                      :color="histColor(h)"
                      :icon="histIcon(h)"
                      small
                    >
                      <div class="font-weight-medium">{{ histLabel(h) }}</div>
                      <div class="caption grey--text">
                        {{ histUser(h) }} • {{ histDate(h) }}
                      </div>
                      <div class="mt-2 d-flex align-center" v-if="h.from_status || h.to_status">
                        <v-chip x-small class="mr-1" color="blue lighten-5" text-color="blue darken-2">
                          {{ capitalize(h.from_status) || '—' }}
                        </v-chip>
                        <v-icon small class="mx-1 grey--text">mdi-arrow-right</v-icon>
                        <v-chip x-small class="mr-1" color="green lighten-5" text-color="green darken-2">
                          {{ capitalize(h.to_status) || '—' }}
                        </v-chip>
                      </div>
                      <div v-if="histNote(h)" class="mt-2">
                        <v-chip x-small class="mr-1" color="grey lighten-4" text-color="grey darken-2">
                          Nota
                        </v-chip>
                        <span>{{ histNote(h) }}</span>
                      </div>
                    </v-timeline-item>
                  </v-timeline>

                  <div v-else class="grey--text text--darken-1">
                    Sem histórico disponível.
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </v-card-text>
    </v-card>
</template>

<script>
import { useOrderStore } from "@/store/OrderStore";
import { useUserStore } from "@/store/UserStore";
import Swal from "sweetalert2";

export default {
  name: "OrdersDetails",
  data() {
    return {
      order: null,
      loading: false,
      annotation: "",
      updating: false,
      copyUuidTooltip: false,
      useUser: useUserStore()
    };
  },
  created() {
    this.load();
  },
  computed: {
    isAdmin() {
      return this.useUser?.user?.tipo_usuario === "admin";
    },
    statusColor() {
      const s = (this.order?.status || "").toLowerCase();
      const map = {
        solicitado: "primary",
        pendente: "warning",
        aprovado: "success",
        cancelado: "error",
        rejeitado: "error"
      };
      return map[s] || "info";
    },
    statusIcon() {
      const s = (this.order?.status || "").toLowerCase();
      const map = {
        solicitado: "mdi-file-document-outline",
        pendente: "mdi-clock-outline",
        aprovado: "mdi-check-circle-outline",
        cancelado: "mdi-close-circle-outline",
        rejeitado: "mdi-close-circle-outline"
      };
      return map[s] || "mdi-information-outline";
    },
    statusLabel() {
      return this.order?.status_label || this.capitalize(this.order?.status) || "-";
    },
    durationDays() {
      if (!this.order?.departure_date || !this.order?.return_date) return null;
      const start = new Date(this.order.departure_date);
      const end = new Date(this.order.return_date);
      const ms = end - start;
      if (isNaN(ms)) return null;
      return Math.max(1, Math.round(ms / 86400000));
    }
  },
  methods: {
    async load() {
      this.loading = true;
      try {
        const store = useOrderStore();
        const id = this.$route.params.id;
        this.order = await store.getThis(id);
      } finally {
        this.loading = false;
      }
    },
    async changeStatus(newStatus) {
      if (!this.order) return;
      this.updating = true;
      try {
        const store = useOrderStore();
        const payload = { status: newStatus };
        if (this.annotation) payload.annotation = this.annotation;

        Swal.fire({
          title: "Confirmação",
          text: `Tem certeza que deseja alterar o status para "${this.capitalize(newStatus)}"?`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sim, alterar",
          cancelButtonText: "Cancelar"
        }).then(async (result) => {
          if (result.isConfirmed) {
            await store.updateStatus(payload);
            await Swal.fire("Sucesso", "Status atualizado com sucesso.", "success");
            this.annotation = "";
            this.order = await store.getThis(this.order.uuid);
          }
        });

      } catch (err) {
        Swal.fire("Erro", "Não foi possível atualizar o status. Tente novamente.", "error");
        console.error(err);
      } finally {
        this.updating = false;
      }
    },
    goBack() {
      this.$router.back();
    },
    formatDate(d) {
      if (!d) return "-";
      const dt = new Date(d);
      if (isNaN(dt)) return d;
      return dt.toLocaleDateString("pt-BR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit"
      });
    },
    formatDateTime(d) {
      if (!d) return "-";
      const dt = new Date(d);
      if (isNaN(dt)) return d;
      return dt.toLocaleString("pt-BR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit"
      });
    },
    capitalize(s) {
      if (!s) return s;
      return String(s).charAt(0).toUpperCase() + String(s).slice(1);
    },
    copyUuid() {
      if (!this.order) return;
      const text = this.order.uuid || this.order.id;
      if (navigator.clipboard?.writeText) {
        navigator.clipboard.writeText(text).then(() => {
          this.copyUuidTooltip = true;
          setTimeout(() => (this.copyUuidTooltip = false), 1200);
        });
      }
    },
    // Helpers para histórico
    histLabel(h) {
      // Prefer label específico, senão mostrar from -> to
      if (!h) return 'Atualização';
      if (h.status_label) return h.status_label;
      const from = h.from_status ? this.capitalize(h.from_status) : '';
      const to = h.to_status ? this.capitalize(h.to_status) : '';
      if (from && to) return `${from} → ${to}`;
      return to || from || 'Atualização';
    },
    histDate(h) {
      return this.formatDateTime(h?.created_at || h?.createdAt);
    },
    histUser(h) {
      return h?.changed_by?.name || h?.changedBy?.name || h?.user?.name || h?.by?.name || 'Sistema';
    },
    histNote(h) {
      return h?.annotation || h?.notes || '';
    },
    histColor(h) {
      const s = (h?.to_status || h?.status || '').toLowerCase();
      const map = {
        solicitado: 'primary',
        pendente: 'warning',
        aprovado: 'success',
        cancelado: 'error',
        rejeitado: 'error'
      };
      return map[s] || 'info';
    },
    histIcon(h) {
      const s = (h?.to_status || h?.status || '').toLowerCase();
      const map = {
        solicitado: 'mdi-file-document-outline',
        pendente: 'mdi-clock-outline',
        aprovado: 'mdi-check-circle-outline',
        cancelado: 'mdi-close-circle-outline',
        rejeitado: 'mdi-close-circle-outline'
      };
      return map[s] || 'mdi-information-outline';
    }
  }
};
</script>

<style scoped>
.subtitle-2 {
  opacity: 0.8;
}
.uuid {
  font-family: monospace;
  user-select: all;
}
</style>
