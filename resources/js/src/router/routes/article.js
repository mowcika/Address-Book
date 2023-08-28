export default [
    {
        path: '/article/create/',
        name: 'article-create',
        component: () => import('@/views/article/articleCreate.vue'),
    },
    {
        path: '/article/comments/create/',
        name: 'article-comments',
        component: () => import('@/views/article/articleComments.vue'),
    },
]
