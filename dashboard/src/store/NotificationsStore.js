import { defineStore } from "pinia"
import NotificationService from "@/services/NotificationService";

const service = new NotificationService();

export const useNotificationsStore = defineStore({
    id: "notifications",
    state: () => ({
        notifications: [],
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
        getAll(params) {
            return new Promise((resolve, reject) => {
                service.getAll(params)
                    .then(response => {
                        this.notifications = response.data;
                        this.pages = response.meta;
                        resolve(response);
                    })
                    .catch(error => reject(error));
            });
        },
        async setRead(uuid) {
            return new Promise((resolve, reject) => {
                service.markAsRead(uuid)
                    .then(response => {
                        const idx = this.notifications.findIndex(n => n.uuid === uuid || n.id === uuid);
                        if (idx !== -1) this.notifications[idx] = { ...this.notifications[idx], is_read: true, read_at: new Date().toISOString() };
                        resolve(response)
                    })
                    .catch(error => reject(error));
            });
        },
    }
})
