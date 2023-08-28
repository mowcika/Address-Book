export default [
    {
        header: 'Analytics',
    },
    {
        title: 'Revenue',
        icon: 'TrendingUpIcon',
        children: [
            {
                title: 'Year / Quarter',
                // route: 'revenue-yhq',
                route: {
                    name: 'revenue-yhq',
                    params: { segment: 'y' }
                },
                icon: 'TrendingUpIcon',
            }
        ],
    },
    {
        title: 'Employer',
        icon: 'UsersIcon',
        children: [
            {
                title: 'Free to Paid Conversion',
                // route: 'revenue-yhq',
                route: {
                    name: 'employer-free-to-paid',
                    params: { segment: 'statistic' }
                },
                icon: 'UserIcon',
            }
        ],
    },
    {
        title: 'Employee',
        icon: 'UsersIcon',
        children: [
            {
                title: 'Employee jobs views based on timing',
                // route: 'revenue-yhq',
                route: {
                    name: 'employee-views-timing',
                    params: { segment: 'timing' }
                },
                icon: 'UserIcon',
            }
        ],
    },
    {
        title: 'Executives',
        icon: 'UsersIcon',
        children: [
            {
                title: 'Executives call report',
                route: 'executives-call-report',
                // route: { name: 'employee-views-timing', params: { segment: "statistic" } },
                icon: 'UserIcon',
            }
        ],
    },
    // {
    //     title: 'On Call',
    //     // route: 'employer-onCall',
    //     route: { name: 'employer-onCall', params: { segment: "load" } },
    //     icon: 'PhoneIcon',
    // },
]
