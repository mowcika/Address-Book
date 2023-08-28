export default [
    {
        path: '/employer/onCall/:segment',
        name: 'employer-onCall',
        component: () => import('@/views/IOTrack/onCallTrack.vue'),
        meta: {
            navActiveLink: 'employer-onCall',
        }
    }
]
