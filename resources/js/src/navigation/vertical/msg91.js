export default [
    {
        header: 'MSG 91',
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
        title: 'Free Expiry',
        // route: 'employer-onCall',
        route: {
            name: 'msg91-message',
            params: { segment: 'free' }
        },
        icon: 'PhoneIcon',
    },
]
