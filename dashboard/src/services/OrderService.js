import ApiService from "@/services/ApiService"

export default class OrderService {
    constructor() {
        this.ApiService = new ApiService();
        this.endpoint = '/travel-orders';
    }

    async getAll(params) {
        const response = await this.ApiService.get(this.endpoint, params)

        return response??null
    }

    async getById(id) {
        const response = await this.ApiService.get(`${this.endpoint}/${id}`, null)

        return response
    }

    async create(data) {
        const response = await this.ApiService.post(this.endpoint, data)

        return response.data
    }

    async update(id, data) {
        const response = await this.ApiService.patch(`${this.endpoint}/${id}/status`, data)

        return response.data
    }

}
