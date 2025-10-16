import ApiService from "@/services/ApiService"

export default class NotificationService {
    constructor() {
        this.ApiService = new ApiService();
        this.endpoint = '/notifications';
    }

    async getAll(params) {
        const response = await this.ApiService.get(this.endpoint, params)

        return response??null
    }

    async markAsRead(id) {
        const response = await this.ApiService.patch(`${this.endpoint}/${id}/read`, null)

        return response.data
    }

}
