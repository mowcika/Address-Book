export default [
    {
        path: '/dashboardPro/analytics',
        name: 'dashboardpro-analytics',
        component: () => import('@/views/dashboardPro/analytics/Analytics.vue'),
    },
    {
        path: '/dashboardPro/ecommerce',
        name: 'dashboardpro-ecommerce',
        component: () => import('@/views/dashboardPro/ecommerce/Ecommerce.vue'),
    },
]
