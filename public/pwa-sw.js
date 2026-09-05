// Service worker minimal untuk instalasi PWA di iOS
self.addEventListener('install', (e) => {
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(self.clients.claim());
});

// Fetch event listener kosong agar memenuhi syarat instalasi PWA tanpa mencegat atau memperlambat network
self.addEventListener('fetch', () => {
  // Biarkan browser menghandle request secara native tanpa proxy overhead
});
