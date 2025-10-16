<script>
import {useUserStore} from "@/store/UserStore";
import LoadingDialog from "@/components/LoadingDialog.vue";
import AuthProvider from "@/views/pages/authentication/AuthProvider.vue";
import Swal from "sweetalert2";

export default {
  name: 'Login',
  components: {AuthProvider, LoadingDialog},
  data() {
    return {
      isPasswordVisible: false,
      form: {
        email: '',
        password: '',
        remember: false,
      },
    }
  },
  methods: {
    async login() {
      const { email, password, remember } = this.form

      this.$refs.loadingDialog.open()
      if (await this.$refs.email.validate() && await this.$refs.password.validate()) {

        try {
          await  useUserStore().login(email, password, remember)

          this.$router.push({ path: 'dashboard' })
        } catch (error) {
          Swal.fire({
            title: "Ooops!",
            text: "E-mail ou senha incorretos!",
            icon: "error",
            confirmButtonText: "Ok",
          });
          this.$refs.loadingDialog.close()
        }
      }
    },
  },
}
</script>

<template>
  <LoadingDialog ref="loadingDialog" />
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <template #prepend>
          <div class="d-flex justify-center">
            <img
              width="200"
              src="/logo.png"
              alt="logo"
            >
          </div>
        </template>
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 mb-1">
          Olá, seja bem vindo! 👋🏻
        </h5>
        <p class="mb-0">
          Para acessar a plataforma, faça seu login.
        </p>
      </VCardText>

      <VCardText>
        <VForm
          lazy-validation
          @submit.prevent="login"
        >
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                ref="email"
                v-model="form.email"
                autofocus
                placeholder="johndoe@email.com"
                label="E-mail"
                type="email"
                validate-on="submit lazy blur"
                :rules="[
                  (v) => !!v || 'E-mail é obrigatório',
                  (v) => /.+@.+\..+/.test(v) || 'E-mail deve ser válido',
                ]"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                ref="password"
                v-model="form.password"
                label="Senha"
                placeholder="············"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                validate-on="lazy blur"
                aria-autocomplete="current-password"
                :rules="[
                  (v) => !!v || 'Senha é obrigatória'
                ]"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />

              

              <!-- login button -->
              <VBtn
                block
                class="mt-4"
                type="submit"
              >
                Entrar
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core/scss/template/pages/page-auth.scss";
</style>
