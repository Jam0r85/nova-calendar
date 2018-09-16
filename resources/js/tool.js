Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-calendar',
            path: '/nova-calendar',
            component: require('./components/Tool'),
        },
    ])
})
