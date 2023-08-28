export default [
    {
        header: 'Feedback Follow Up',
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
        title: '6th Day Follow Up',
        // route: 'employer-onCall',
        route: {
            name: 'jobs-feedback',
            params: { segment: 'load' }
        },
        icon: 'PhoneIcon',
    },
]
