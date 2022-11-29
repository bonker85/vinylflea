self.addEventListener('install', (event) => {
    console.log('Установлен');
});

self.addEventListener('activate', (event) => {
    console.log('Активирован');
});

self.addEventListener('fetch', (event) => {
    console.log('Происходит запрос на сервер');
});
self.addEventListener('beforeinstallprompt', (e) => {
    $('.install-app-btn-container').show();
    deferredPrompt = e;
});
