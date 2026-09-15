const CACHE_NAME = "hbt-produksi-v3";
const urlsToCache = [
    "/images/logo.png",
    "/images/favicon192.png",
];

// Install Service Worker
self.addEventListener("install", (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(urlsToCache);
        }),
    );
});

// Cache and return requests - Network first, fallback to cache
self.addEventListener("fetch", (event) => {
    // Only handle GET requests and skip non-http
    if (event.request.method !== "GET" || !event.request.url.startsWith("http")) {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        }),
    );
});

// Update Service Worker & delete all old caches
self.addEventListener("activate", (event) => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        console.log("Deleting old cache:", cacheName);
                        return caches.delete(cacheName);
                    }
                }),
            );
        }).then(() => self.clients.claim())
    );
});

