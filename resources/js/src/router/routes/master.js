export default [
    {
        path: '/login',
        name: 'auth-login',
        component: () => import('@/views/Login.vue'),
        meta: {
            layout: 'full',
            resource: 'Auth',
            redirectIfLoggedIn: true,
        },
    },
    {
        path: '/appName',
        name: 'appName',
        component: () => import('@/views/master/appName/AppMaster.vue'),
    },
    {
        path: '/lang',
        name: 'lang',
        component: () => import('@/views/master/language/LangMaster.vue'),
    },
    {
        path: '/Add/Category',
        name: 'LangCatMaster',
        component: () => import('@/views/master/Category/AddCategory.vue'),
    },
    {
        path: '/Add/SubCategory',
        name: 'subcat',
        component: () => import('@/views/master/Category/AddSubCategory.vue'),
    },

    {
        path: '/msg/type',
        name: 'msgType',
        component: () => import('@/views/master/msgType/MsgType.vue'),
    },
    {
        path: '/notification/type',
        name: 'notiType',
        component: () => import('@/views/master/notiType/NotiType.vue'),
    },
    {
        path: '/fontSize/add',
        name: 'fontSizeMaster',
        component: () => import('@/views/master/fontSize/FontSizeMaster.vue'),
    },
    {
        path: '/master/roleMaster',
        name: 'role-master',
        component: () => import('@/views/master/roleMaster.vue'),
    },
    {
        path: '/master/userRole',
        name: 'user-role',
        component: () => import('@/views/master/userRole.vue'),
    },
    {
        path: '/master/accessPermission',
        name: 'accessPermission',
        component: () => import('@/views/master/accessPermission.vue'),
    },
    {
        path: '/user/group',
        name: 'user-group',
        component: () => import('@/views/master/user/user-group/UserGroup.vue'),
    },
    {
        path: '/user/list',
        name: 'user-list',
        component: () => import('@/views/master/user/user-list/UserList.vue'),
    },
    {
        path: '/login1',
        name: 'login1',
        component: () => import('@/views/Login1'),
    },
    {
        path: '/menu',
        name: 'menu',
        component: () => import('@/views/Menu.vue'),
    },
]
