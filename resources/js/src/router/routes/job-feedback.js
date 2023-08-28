export default [
    {
        path: '/jobs/feedback/:segment',
        name: 'jobs-feedback',
        component: () => import('@/views/jobs-feedback/jobs-feedback.vue'),
        meta: {
            navActiveLink: 'jobs-feedback',
        }
    }
]
