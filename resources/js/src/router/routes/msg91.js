export default [
    {
        path: '/msg91/message/:segment',
        name: 'msg91-message',
        component: () => import('@/views/msg91/msg91.vue'),
        meta: {
            navActiveLink: 'msg91-message',
        }
    }
]
