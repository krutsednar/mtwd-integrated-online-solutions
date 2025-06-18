var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
    "/storage/01JXYHAQHP667KGRF52A4FZBK0.png",
    "/storage/01JXYHAQHSPVM7RCEHQPD33YV4.png",
    "/storage/01JXYHAQHW5D3KA7VASF28MJ3J.png",
    "/storage/01JXYHAQHYNF422PRNFHGGT8X6.png",
    "/storage/01JXYHAQHZAHSVERFH7AT5CXVA.png",
    "/storage/01JXYHAQJ1AFCM8CN25ZH6TTM1.png",
    "/storage/01JXYHAQJ2JMTDS998R5ZP345S.png",
    "/storage/01JXYHAQJ4R25K1X2PRQPPG6KP.png"
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
