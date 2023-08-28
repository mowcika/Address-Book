export default [
    {
        header: 'Master',
    },
    {
        title: 'Master',
        icon: 'FileTextIcon',
        children: [
            {
                title: 'App Name',
                route: 'appName',
                icon: 'CommandIcon',
            },
            {
                title: 'Language',
                route: 'lang',
                icon: 'CommandIcon',
            },
            {
                title: 'Notification Type',
                route: 'notiType',
                icon: 'CommandIcon',
            },
            {
                title: 'Font Size',
                route: 'fontSizeMaster',
                icon: 'CommandIcon',
            },
            {
                title: 'Role Master',
                route: 'role-master',
                icon: 'UserIcon',
            },
            {
                title: 'User Role',
                route: 'user-role',
                icon: 'ApertureIcon',
            },
            {
                title: 'Access Permission',
                route: 'accessPermission',
                icon: 'UserIcon',
            }
            // {
            //     title: 'Designation',
            //     route: 'designation',
            //     icon: '',
            // },
        ],
    },
    // {
    //     title: 'User Master',
    //     icon: 'UserIcon',
    //     children: [
    //         {
    //             title: 'User Group',
    //             route: 'user-group',
    //             icon: '',
    //         },
    //         {
    //             title: 'Users',
    //             route: 'user-list',
    //             icon: '',
    //         },
    //     ],
    // },
    {
        title: 'Menu Master',
        icon: 'UserIcon',
        children: [
            {
                title: 'Menu Master',
                route: 'menu',
                icon: '',
            },
        ],
    },
]
