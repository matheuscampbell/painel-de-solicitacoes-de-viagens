<template>
  <div>
    <!-- Botão de ação para abrir o modal -->
    <v-btn
      color="primary"
      prepend-icon="mdi-airplane"
      @click="dialog = true"
    >
      {{buttonText}}
    </v-btn>

    <v-dialog v-model="dialog" :fullscreen="isMobile" max-width="1200" scrollable>
      <v-card rounded="lg">
        <v-toolbar flat density="comfortable" color="primary" dark>
          <v-toolbar-title>Novo pedido de Viagem:</v-toolbar-title>
          <v-spacer />
          <v-btn icon="mdi-close" color="white" variant="text" @click="dialog = false" />
        </v-toolbar>
        <v-divider />

        <v-card-text>

          <v-form @submit.prevent="onSubmit">
            <v-row class="g-2 g-md-3" justify="center" align="center">
              <!-- Origem / Destino -->
              <v-col cols="12" lg="7" md="7" sm="12">
                <v-row align="end" no-gutters class="g-2">
                  <!-- Origem -->
                  <v-col cols="12" md="5">
                    <v-text-field
                      v-model.trim="origin"
                      label="Origem"
                      prepend-inner-icon="mdi-airplane-takeoff"
                      variant="outlined"
                      :density="isMobile ? 'compact' : 'comfortable'"
                      hide-details="auto"
                      :rules="[required]"
                    />
                  </v-col>

                  <!-- Botão trocar (mobile) -->
                  <v-col cols="12" class="d-flex justify-center d-md-none">
                    <v-btn
                      icon="mdi-swap-horizontal"
                      variant="tonal"
                      color="deep-purple"
                      size="small"
                      @click="swapPlaces"
                      :title="'Trocar origem/destino'"
                    />
                  </v-col>

                  <!-- Botão trocar (md+) -->
                  <v-col class="d-none d-md-flex justify-center" md="2">
                    <v-btn
                      class="my-1"
                      icon="mdi-swap-horizontal"
                      variant="tonal"
                      color="deep-purple"
                      @click="swapPlaces"
                      :title="'Trocar origem/destino'"
                    />
                  </v-col>

                  <!-- Destino -->
                  <v-col cols="12" md="5">
                    <v-text-field
                      v-model.trim="destination"
                      label="Destino"
                      prepend-inner-icon="mdi-map-marker"
                      variant="outlined"
                      :density="isMobile ? 'compact' : 'comfortable'"
                      hide-details="auto"
                      :rules="[required]"
                    />
                  </v-col>
                </v-row>
              </v-col>

              <!-- Datas -->
              <v-col cols="12" lg="5" md="5">
                <v-row class="g-2">
                  <v-col :cols="12" :md="tripType === 'roundtrip' ? 6 : 12">
                    <component
                      :is="hasDateInput ? 'v-date-input' : 'v-text-field'"
                      v-model="departDate"
                      :label="'Ida'"
                      :prepend-icon="hasDateInput ? 'mdi-calendar' : undefined"
                      :prepend-inner-icon="hasDateInput ? undefined : 'mdi-calendar'"
                      :min="today"
                      :type="!hasDateInput ? 'date' : undefined"
                      variant="outlined"
                      :density="isMobile ? 'compact' : 'comfortable'"
                      hide-details="auto"
                      :rules="[required]"
                    />
                  </v-col>

                  <v-col v-if="tripType === 'roundtrip'" cols="12" md="6">
                    <component
                      :is="hasDateInput ? 'v-date-input' : 'v-text-field'"
                      v-model="returnDate"
                      label="Volta"
                      :prepend-icon="hasDateInput ? 'mdi-calendar-arrow-left' : undefined"
                      :prepend-inner-icon="hasDateInput ? undefined : 'mdi-calendar-arrow-left'"
                      :min="minReturn"
                      :type="!hasDateInput ? 'date' : undefined"
                      variant="outlined"
                      :density="isMobile ? 'compact' : 'comfortable'"
                      hide-details="auto"
                      :rules="[required]"
                    />
                  </v-col>
                </v-row>
              </v-col>

              <!-- Notes -->
              <v-col cols="12">
                <v-textarea
                  v-model="orderStore.order.notes"
                  label="Notas adicionais (opcional)"
                  prepend-inner-icon="mdi-note-text"
                  variant="outlined"
                  :density="isMobile ? 'compact' : 'comfortable'"
                  hide-details="auto"
                  rows="2"
                  auto-grow
                />
              </v-col>

              <!-- Buscar -->
              <v-col cols="12">
                <v-btn
                  type="submit"
                  class="text-none font-weight-bold"
                  size="large"
                  rounded="lg"
                  block
                >
                  Realizar Pedido de Viagem <v-icon right>mdi-arrow-right</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useOrderStore } from '@/store/OrderStore'
import Swal from 'sweetalert2'

export default {
  name: 'NewOrder',
  props: {
    // Prop to indicate if the browser supports date input
    buttonText: {
      type: String,
      default: 'Novo pedido de Viagem'
    }
  },
  data() {
    const fmt = (d) => new Date(d).toISOString().slice(0, 10)
    const today = fmt(new Date())
    return {
      dialog: false,
      tripType: 'roundtrip',
      origin: '',
      destination: '',
      hasDateInput: false,
      today,
      departDate: today,
      returnDate: fmt(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)),
      isMobile: false,
      orderStore: useOrderStore()
    }
  },
  computed: {
    minReturn() {
      return this.departDate || this.today
    }
  },
  watch: {
    departDate(val) {
      if (!val) return
      if (new Date(this.returnDate) < new Date(val)) {
        this.returnDate = val
      }
    },
    tripType(t) {
      if (t === 'oneway') {
        this.returnDate = ''
      } else if (!this.returnDate) {
        const d = new Date(this.departDate || this.today)
        d.setDate(d.getDate() + 7)
        this.returnDate = new Date(d).toISOString().slice(0, 10)
      }
    }
  },
  methods: {
    onResize() {
      this.isMobile = window.matchMedia('(max-width: 960px)').matches
    },
    swapPlaces() {
      [this.origin, this.destination] = [this.destination, this.origin]
    },
    required(v) {
      return (!!v && String(v).length > 0) || 'Obrigatório'
    },
    async onSubmit() {
      try {
        if (isNaN(new Date(this.departDate))) throw new Error('invalid')
        if (this.tripType === 'roundtrip') {
          if (isNaN(new Date(this.returnDate))) throw new Error('invalid')
          if (new Date(this.returnDate) < new Date(this.departDate)) {
            await Swal.fire('Data inválida', 'A data de volta não pode ser anterior à data de ida.', 'error')
            return
          }
        }

        this.orderStore.order = {
          origin: this.origin,
          destination: this.destination,
          departure_date: this.departDate,
          return_date: this.returnDate,
          notes: this.orderStore.order?.notes || ''
        }

        const order = await this.orderStore.save()
        console.log(order)
        await Swal.fire('Sucesso!', `Pedido criado com sucesso!`, 'success')
        // Reset form
        this.origin = ''
        this.destination = ''
        this.departDate = this.today
        this.returnDate = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().slice(0, 10)
        this.dialog = false
        location.href = `/orders/${order.uuid}`
      } catch (e) {
        console.error(e)
        await Swal.fire('Oops!', 'Ocorreu um erro ao criar o pedido.', 'error')
      }
    }
  },
  mounted() {
    this.onResize()
    window.addEventListener('resize', this.onResize)
  },
  unmounted() {
    window.removeEventListener('resize', this.onResize)
  }
}
</script>
