import { defineStore } from "pinia"
import OrderService from "@/services/OrderService";

const service = new OrderService();

export const useOrderStore = defineStore({
    id: "orders",
    state: () => ({
        order: {
            origin: '',
            destination: '',
            departure_date: '',
            return_date: '',
            notes: ''
        },
        orders: [],
        pages: {
            "current_page": 1,
            "per_page": 10,
            "total": 10,
            "last_page": 1,
            "from": 1,
            "to": 1,
            "has_more_pages": false
        }
    }),
    actions: {
        async getThis(id) {
            return new Promise((resolve, reject) => {
                service.getById(id)
                    .then(response => {
                        this.order = response.data;
                        resolve(response.data);
                    })
                    .catch(error => reject(error));
            });
        },
        getAll(params) {
            return new Promise((resolve, reject) => {
                service.getAll(params)
                    .then(response => {
                        this.orders = response.data;
                        this.pages = response.meta;
                        resolve(response);
                    })
                    .catch(error => reject(error));
            });
        },
        async save() {
            return new Promise((resolve, reject) => {
                service.create(this.order)
                    .then(response => {
                        this.order = response;
                        resolve(response);
                    })
                    .catch(error => reject(error));
            });
        },
        async updateStatus(payload) {
            return new Promise((resolve, reject) => {
                service.update(this.order.uuid, payload)
                    .then(response => resolve(response))
                    .catch(error => reject(error));
            });
        },
    
        reset() {
            this.order = {
                origin: '',
                destination: '',
                departure_date: '',
                return_date: '',
                notes: ''
            };
            this.orders = [];
            this.pages = {
                "current_page": 1,
                "per_page": 10,
                "total": 10,
                "last_page": 1,
                "from": 1,
                "to": 1,
                "has_more_pages": false
            };
        }
    }
})
