export default [
    {
        path: '/forum/create/',
        name: 'forum-create',
        component: () => import('@/views/forum/forumCreate.vue'),
    },
    {
        path: '/forum/ans/',
        name: 'forum-answers',
        component: () => import('@/views/forum/forumAnswers.vue'),
    },
]
