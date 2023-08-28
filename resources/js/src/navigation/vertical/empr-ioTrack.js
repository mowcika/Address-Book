export default [
    {
        header: 'Employer IO Track',
    },
    // {
    //     title: 'Upload',
    //     icon: 'UploadIcon',
    //     children: [
    //         {
    //             title: 'Free Calling Upload',
    //             route: 'freeCalling-upload',
    //             icon: 'UploadIcon',
    //         },
    //         {
    //             title: 'Private Job',
    //             route: 'private-posting',
    //             icon: 'ApertureIcon',
    //         },
    //     ],
    // },
    // {
    //     title: 'Follow Up',
    //     icon: 'UserIcon',
    //     children: [
    //         {
    //             title: 'Add Free Calling',
    //             route: 'freeCalling-add',
    //             icon: 'UsersIcon',
    //         },
    //     ],
    // },
    {
        title: 'On Call',
        // route: 'employer-onCall',
        route: {
            name: 'employer-onCall',
            params: { segment: 'load' }
        },
        icon: 'PhoneIcon',
    },
]
