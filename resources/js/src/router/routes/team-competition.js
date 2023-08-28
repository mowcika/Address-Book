export default [
    {
        path: '/team/comparison/',
        name: 'paymentTeamComparison',
        component: () => import('@/views/teamCompetition/paymentTeamComparison.vue'),
    },
    {
        path: '/month/wise/comparison',
        name: 'paymentMonthComparison',
        component: () => import('@/views/teamCompetition/paymentMonthComparison.vue'),
    },
    {
        path: '/team/run/rate',
        name: 'teamRunRate',
        component: () => import('@/views/teamCompetition/teamRunRate.vue'),
    },
    {
        path: '/executive/prediction',
        name: 'executive-prediction',
        component: () => import('@/views/teamCompetition/executive-prediction.vue'),
    },

]
