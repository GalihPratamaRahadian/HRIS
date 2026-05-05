const registerServiceWorker = () => {
	return navigator.serviceWorker.register('/service-worker.js')
	.then(registration => {
		console.log('sw berhasil');
		return registration;
	})
	.catch(error => {
		console.error(`Pendaftaran gagal : ${error}`);
	});
}

if(!("serviceWorker" in navigator)) {
	console.log("Tidak mendukung service worker");
} else {
	registerServiceWorker();
}