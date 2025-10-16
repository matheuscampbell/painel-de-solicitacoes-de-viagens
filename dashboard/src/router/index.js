import {createRouter, createWebHistory} from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {path: '/', redirect: '/dashboard'},
        {
            path: '/',
            component: () => import('../layouts/default.vue'),
            children: [
                {
                    path: 'dashboard',
                    component: () => import('../pages/dashboard.vue'),
                }
            ],
        },{
            path: '/orders',
            component: () => import('../layouts/default.vue'),
            children: [
                {
                    path: ':id',
                    name: 'order-details',
                    component: () => import('../pages/orders/ordersDetails.vue'),
                }
            ]
        },
        {
            path: '/',
            component: () => import('../layouts/blank.vue'),
            children: [
                {
                    path: 'login',
                    component: () => import('../pages/login.vue'),
                },
                {
                    path: '/:pathMatch(.*)*',
                    component: () => import('../pages/[...all].vue'),
                },
            ],
        },
    ],
})

export default router
