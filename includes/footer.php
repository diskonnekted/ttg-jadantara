<!-- Install App Prompt (PWA) -->
<div id="install-prompt" class="fixed bottom-20 left-4 right-4 bg-white p-4 rounded-xl shadow-2xl border border-gray-100 z-50 transform transition-transform duration-300 translate-y-full" style="display: none;">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-primary">
            <i class="fas fa-mobile-alt text-2xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-800">Install Aplikasi</h3>
            <p class="text-sm text-gray-500 mb-3">Akses tutorial lebih cepat & hemat data langsung dari layar utama HP Anda.</p>
            <div class="flex gap-2">
                <button id="install-btn" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-green-600 transition">Install</button>
                <button id="close-install" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition">Nanti</button>
            </div>
        </div>
    </div>
</div>

<script>
    // PWA Install Prompt Logic
    let deferredPrompt;
    const installPrompt = document.getElementById('install-prompt');
    const installBtn = document.getElementById('install-btn');
    const closeBtn = document.getElementById('close-install');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI to notify the user they can add to home screen
        installPrompt.style.display = 'block';
        setTimeout(() => {
            installPrompt.classList.remove('translate-y-full');
        }, 100);
    });

    installBtn.addEventListener('click', (e) => {
        // Hide our user interface that shows our A2HS button
        installPrompt.classList.add('translate-y-full');
        setTimeout(() => { installPrompt.style.display = 'none'; }, 300);
        
        // Show the prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the A2HS prompt');
            } else {
                console.log('User dismissed the A2HS prompt');
            }
            deferredPrompt = null;
        });
    });

    closeBtn.addEventListener('click', (e) => {
        installPrompt.classList.add('translate-y-full');
        setTimeout(() => { installPrompt.style.display = 'none'; }, 300);
    });
</script>

<!-- Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 pb-safe">
    <div class="flex justify-around items-center h-16 max-w-md mx-auto">
        <a href="<?php echo BASE_URL; ?>/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-primary active:text-primary transition">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs">Beranda</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/category.php" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-primary active:text-primary transition">
            <i class="fas fa-th-large text-xl mb-1"></i>
            <span class="text-xs">Kategori</span>
        </a>
        <!-- Saved functionality using LocalStorage (client-side) -->
        <a href="#" onclick="alert('Fitur Simpan akan segera hadir!'); return false;" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-primary active:text-primary transition">
            <i class="fas fa-bookmark text-xl mb-1"></i>
            <span class="text-xs">Disimpan</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-primary active:text-primary transition">
            <i class="fas fa-user text-xl mb-1"></i>
            <span class="text-xs">Profil</span>
        </a>
    </div>
</div>

<!-- Scripts -->
<script>
    // PWA Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('<?php echo BASE_URL; ?>/sw.js')
                .then(registration => {
                    console.log('SW registered:', registration);
                })
                .catch(error => {
                    console.log('SW registration failed:', error);
                });
        });
    }
</script>

</body>
</html>
