importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.6.3/workbox-sw.js');

workbox.precaching.precacheAndRoute([
    {
        url: "/favicon.png",
        revision: '1'
    },
    {
        url: "/images/icons/adiva-192px.png",
        revision: '1'
    },
    {
        url: "/images/icons/adiva-512px.png",
        revision: '1'
    },
    {
        url: "/manifest.json",
        revision: '1'
    },
]);

workbox.routing.registerRoute(
    new RegExp('/vendors/'),
    workbox.strategies.cacheFirst()
);

workbox.routing.registerRoute(
    new RegExp('/images/'),
    workbox.strategies.cacheFirst()
);

workbox.routing.registerRoute(
    new RegExp('/css/'),
    workbox.strategies.networkOnly()
);

workbox.routing.registerRoute(
    new RegExp('/js/'),
    workbox.strategies.networkOnly()
);

