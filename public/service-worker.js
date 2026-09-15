const CACHE_NAME = "hbt-produksi-v2";
const urlsToCache = [
    "/",
    "/images/logo.png",
];

// Install Service Worker
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log("Opened cache");
            return cache.addAll(urlsToCache);
        }),
    );
});

// Cache and return requests
self.addEventListener("fetch", (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        }),
    );
});

// Update Service Worker
self.addEventListener("activate", (event) => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                }),
            );
        }),
    );
});
