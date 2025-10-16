import ApiService from "@/services/ApiService"

export default class LoginService  {
  constructor() {
      this.ApiService = new ApiService();
  }

  async login(email, password, rememberMe = false) {
    return await this.ApiService.post('/login', {
      email,
      password,
      remember_me: rememberMe,
    })
  }

  async logout() {
    const response = await this.ApiService.post('/logout')

    return response.data
  }

  async register(payload) {
    const response = await this.ApiService.post('/register', payload)

    return response.data
  }

  async checkToken() {
    const response = await this.ApiService.get('/check-token')

    return response.data
  }
}
