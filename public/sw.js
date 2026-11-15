/* Janwar Mart service worker (Laravel version).
   Pages are rendered per user on the server, so HTML pages are NEVER cached (no private data is stored).
   Only static files and animal photos are cached; when the phone is offline a small offline page is shown. */
const VERSION = "jm-lv-1";
const STATIC = "static-" + VERSION, IMGS = "imgs-" + VERSION;
const FILES = ["/offline.html", "/style.css", "/js/app.js", "/manifest.webmanifest", "/icons/icon-192.png", "/icons/icon-512.png", "/img/logo.png"];
const MAX_IMGS = 80;

self.addEventListener("install", e => e.waitUntil(caches.open(STATIC).then(c => c.addAll(FILES)).then(() => self.skipWaiting())));
self.addEventListener("activate", e => e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k => ![STATIC, IMGS].includes(k)).map(k => caches.delete(k)))).then(() => self.clients.claim())));

self.addEventListener("fetch", e => {
  const req = e.request, url = new URL(req.url);
  if (req.method !== "GET" || url.origin !== location.origin) return;
  const p = url.pathname;
  if (p.startsWith("/admin") || p.startsWith("/api/") || p === "/sw.js") return;       // admin and APIs: always the network
  if (req.mode === "navigate") {
    e.respondWith(fetch(req).catch(() => caches.match("/offline.html")));
    return;
  }
  if (p.startsWith("/storage/")) {                                                     // public animal photos: cache first
    e.respondWith(caches.open(IMGS).then(async c => {
      const hit = await c.match(req); if (hit) return hit;
      const res = await fetch(req);
      if (res && res.ok) { c.put(req, res.clone()); const k = await c.keys(); if (k.length > MAX_IMGS) c.delete(k[0]); }
      return res;
    }));
    return;
  }
  if (FILES.includes(p) || p.startsWith("/img/") || p.startsWith("/icons/")) {
    e.respondWith(fetch(req).then(res => { if (res.ok) caches.open(STATIC).then(c => c.put(req, res.clone())); return res; }).catch(() => caches.match(req)));
  }
});
