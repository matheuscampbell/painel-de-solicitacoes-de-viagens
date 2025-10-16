//user store is a class that extends the base store class
import {defineStore} from "pinia"
import LoginService from "@/services/LoginService"
import UserService from "@/services/UserService"
import Cookies from "js-cookie"

export const useUserStore = defineStore({
    id: "user",
    state: () => ({
        user: {
            name: '',
            tipo_usuario: '',
        },
        token: null,
        token_expires_at: null,
    }),
    getters: {
        userRole: state => state.user ? state.user.role : null,
    },
    actions: {
        login: async function (email, password, rememberMe = false) {
            return new Promise((resolve, reject) => {
                new LoginService().login(email, password, rememberMe)
                    .then(async response => {
                        this.token = response.access_token
                        this.token_expires_at = new Date(Date.now() + response.expires_in * 1000)
                        Cookies.set("token", this.token, {expires: this.token_expires_at, domain: location.hostname})
                        resolve(response)
                    })
                    .catch(error => reject(error))
            })
        },
        isTokenExpired() {
            return this.token_expires_at < Date.now()
        },
        async getUser() {
            return new Promise((resolve, reject) => {
                new UserService().get()
                    .then(response => {
                        this.user = response
                        resolve(response)
                    })
                    .catch(error => reject(error))
            })
        },
        async logout() {
            return new Promise((resolve, reject) => {
                new LoginService().logout()
                    .then(response => {
                        this.user = null
                        this.token = null
                        this.token_expires_at = null
                        Cookies.remove("token")
                        resolve(response)
                    })
                    .catch(error => reject(error))
            })
        },
        async checkToken() {
            await this.getUser();
            return await new LoginService().checkToken()
        },
        userInitialLetters() {
            if (!this.user || !this.user.name) return '';
            const names = this.user.name.split(' ');
            if (names.length === 1) {
                return names[0].charAt(0).toUpperCase();
            } else {
                return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
            }
        },
    },
})
