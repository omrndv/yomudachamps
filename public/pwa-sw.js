// Service worker minimal untuk instalasi PWA di iOS
self.addEventListener('install', (e) => {
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (e) => {
  // Hanya bypass network
  e.respondWith(fetch(e.request));
});
