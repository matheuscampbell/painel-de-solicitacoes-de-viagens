<template>
  <v-menu v-model="menuOpen" location="bottom end" :close-on-content-click="false">
    <template #activator="{ props }">
      <v-badge :content="unreadCount" color="error" v-if="unreadCount > 0">
        <v-btn v-bind="props" icon variant="text">
          <VIcon icon="bx-bell" />
        </v-btn>
      </v-badge>
      <template v-else>
        <v-btn v-bind="props" icon variant="text">
          <VIcon icon="bx-bell" />
        </v-btn>
      </template>
    </template>

    <v-card style="min-width: 340px; max-width: 460px;">
      <v-card-title class="py-2 d-flex align-center justify-space-between">
        Notificações:
        <v-btn variant="tonal" density="compact" size="small" @click="markAllRead" :disabled="mappedNotifications.length===0">
          Marcar todas lidas <v-icon class="ms-1" size="18">mdi-check-all</v-icon>
        </v-btn>
      </v-card-title>
      <v-divider />
      <v-card-text style="max-height: 400px; overflow:auto;">
        <v-list density="compact">
          <v-list-item v-if="mappedNotifications.length===0">
            <v-list-item-title class="grey--text">Sem notificações</v-list-item-title>
          </v-list-item>

          <v-list-item
            v-for="n in mappedNotifications"
            :key="n.key"
            @click="openNotification(n)"
            class="cursor-pointer"
          >
            <template #prepend>
              <v-badge dot color="primary" :model-value="!n.read">
                <v-icon :color="n.color">{{ n.icon }}</v-icon>
              </v-badge>
            </template>

            <v-list-item-title>
              <span :class="{ 'font-weight-medium': !n.read }">{{ n.title }}</span>
            </v-list-item-title>
            <v-list-item-subtitle class="grey--text">
              {{ n.subtitle }}
            </v-list-item-subtitle>

            <template #append>
              <v-chip size="small" :color="n.chipColor" text-color="black">{{ n.chip }}</v-chip>
            </template>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>
  </v-menu>
</template>

<script>
import { useNotificationsStore } from '@/store/NotificationsStore';

export default {
  name: 'NotificationsDropdown',
  data() {
    return {
      notificationsStore: useNotificationsStore(),
      loading: false,
      menuOpen: false,
      pollId: null,
    };
  },
  created() {
    this.load();
  },
  mounted() {
    this.startPolling();
  },
  beforeUnmount() {
    if (this.pollId) clearInterval(this.pollId);
  },
  computed: {
    notifications() {
      return this.notificationsStore.notifications || [];
    },
    unreadCount() {
      return this.notifications.filter(n => !(n.is_read || n.read_at)).length;
    },
    mappedNotifications() {
      // transforma itens crus do backend em objetos de exibição
      return (this.notifications || []).map(n => {
        const status = n.data?.status || n.status || 'notificação';
        const prev = n.data?.previous_status || n.previous_status || '';
        const dest = n.data?.destination || '';
        const message = n.message || n.data?.message;
        const title = message || `Pedido de viagem para ${dest} ${this.statusHuman(status)}`;
        const subtitleParts = [];
        if (prev || status) subtitleParts.push(`${this.capitalize(prev)} → ${this.capitalize(status)}`);
        if (dest) subtitleParts.push(dest);
        const subtitle = `${subtitleParts.join(' • ')} • ${this.formatDateTime(n.created_at || n.createdAt)}`;
        const link = `/orders/${n.data?.travel_order_uuid || n.data?.order_uuid || ''}`;
        return {
          key: n.uuid || n.id,
          id: n.id || n.uuid,
          title,
          subtitle,
          chip: this.capitalize(status),
          chipColor: this.statusColor(status),
          color: this.iconColor(status),
          icon: this.statusIcon(status),
          link,
          read: Boolean(n.is_read || n.read_at),
          raw: n,
        };
      });
    }
  },
  methods: {
    async load() {
      this.loading = true;
      try {
        await this.notificationsStore.getAll({ per_page: 10 });
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
    startPolling() {
      if (this.pollId) clearInterval(this.pollId);
      this.pollId = setInterval(() => {
        this.notificationsStore.getAll({ per_page: 10 });
      }, 5000);
    },
    async openNotification(n) {
      try {
        if (n?.id) await this.notificationsStore.setRead(n.id);
      } catch (e) {
        console.error('Falha ao marcar como lida', e);
      }
      this.menuOpen = false;
      if (n.link) this.$router.push(n.link);
    },
    async markAllRead() {
      const unreadIds = this.mappedNotifications.filter(n => !n.read && n.id).map(n => n.id);
      await Promise.all(unreadIds.map(id => this.notificationsStore.setRead(id)));
    },
    formatDateTime(d) {
      if (!d) return '-';
      const dt = new Date(d);
      if (isNaN(dt)) return d;
      return dt.toLocaleString('pt-BR', {
        year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'
      });
    },
    capitalize(s) {
      if (!s) return '';
      return String(s).charAt(0).toUpperCase() + String(s).slice(1);
    },
    statusHuman(s) {
      const m = { aprovado: 'foi aprovado', rejeitado: 'foi rejeitado', cancelado: 'foi cancelado', pendente: 'está pendente', solicitado: 'foi solicitado' };
      return m[(s||'').toLowerCase()] || s;
    },
    statusColor(s) {
      const m = {
        solicitado: 'blue lighten-4',
        pendente: 'amber lighten-4',
        aprovado: 'green lighten-4',
        cancelado: 'red lighten-4',
        rejeitado: 'red lighten-4'
      };
      return m[(s||'').toLowerCase()] || 'grey lighten-3';
    },
    iconColor(s) {
      const m = {
        solicitado: 'primary',
        pendente: 'warning',
        aprovado: 'success',
        cancelado: 'error',
        rejeitado: 'error'
      };
      return m[(s||'').toLowerCase()] || 'grey';
    },
    statusIcon(s) {
      const m = {
        solicitado: 'mdi-file-document-outline',
        pendente: 'mdi-clock-outline',
        aprovado: 'mdi-check-circle-outline',
        cancelado: 'mdi-close-circle-outline',
        rejeitado: 'mdi-close-circle-outline'
      };
      return m[(s||'').toLowerCase()] || 'mdi-bell-outline';
    }
  }
}
</script>

<style scoped>
.cursor-pointer { cursor: pointer; }
</style>
