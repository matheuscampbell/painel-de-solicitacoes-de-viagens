import ApiService from "@/services/ApiService"

export default class UserService  {
  constructor() {
      this.ApiService = new ApiService();
  }

  async get() {
    const response = await this.ApiService.get('/getMe', null)
    return response
  }

}
