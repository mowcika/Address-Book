export default [
    {
        header: 'Free Calling',
    },
    {
        title: 'Upload',
        icon: 'UploadIcon',
        children: [
            {
                title: 'Free Calling Upload',
                route: 'freeCalling-upload',
                icon: 'UploadIcon',
            },
            // {
            //     title: 'Private Job',
            //     route: 'private-posting',
            //     icon: 'ApertureIcon',
            // },
        ],
    },
    {
        title: 'Follow Up',
        icon: 'UserIcon',
        children: [
            {
                title: 'Add Free Calling',
                route: 'freeCalling-add',
                icon: 'UsersIcon',
            },
        ],
    },
    {
        title: 'Confirmed Calls',
        icon: 'UserIcon',
        children: [
            {
                title: 'Employer Creation',
                route: 'freecalling-employer-creation',
                icon: 'UsersIcon',
            },
            // {
            //     title: 'Invoice Request',
            //     route: 'invoice-Request',
            //     icon: 'ArrowUpRightIcon',
            // },
        ],
    },
    // {
    //     title: 'Search',
    //     route: 'job-search',
    //     icon: 'SearchIcon',
    // },
]
