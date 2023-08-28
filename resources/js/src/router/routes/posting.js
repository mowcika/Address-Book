export default [
    {
        path: '/save/notification/',
        name: 'save-noti',
        component: () => import('@/views/notification/SaveNotification.vue'),
    },
    {
        path: '/send/notification',
        name: 'send-noti',
        component: () => import('@/views/listing/Notification.vue'),
    },
    {
        path: '/view/sendNotification',
        name: 'view-send-noti',
        component: () => import('@/views/listing/SendedNotification.vue'),
    },
]
