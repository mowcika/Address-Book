export default [
    {
        path: '/analytics/revenue/yhq/:segment',
        name: 'revenue-yhq',
        component: () => import('@/views/analytics/revenue-yhq.vue'),
    },
    {
        path: '/employer/free/to/paid/:segment',
        name: 'employer-free-to-paid',
        component: () => import('@/views/analytics/employer-free-to-paid.vue'),
    },
    {
        path: '/employee/views/timing/:segment',
        name: 'employee-views-timing',
        component: () => import('@/views/analytics/employee-views-timing.vue'),
    },
    {
        path: '/executives/call/report',
        name: 'executives-call-report',
        component: () => import('@/views/analytics/executives-call-report.vue'),
    }
]
