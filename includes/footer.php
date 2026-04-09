<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-50 pb-safe">
    <div class="flex justify-around items-center h-14 max-w-lg mx-auto">
        <a href="<?php echo BASE_URL; ?>/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <img src="<?php echo BASE_URL; ?>/assets/icons/ide.png" class="w-6 h-6 mb-0.5" alt="Beranda">
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/category.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <img src="<?php echo BASE_URL; ?>/assets/icons/perikanan.png" class="w-6 h-6 mb-0.5" alt="Kategori">
            <span class="text-[10px] font-medium">Kategori</span>
        </a>
        <a href="#" onclick="alert('Segera hadir!'); return false;" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <img src="<?php echo BASE_URL; ?>/assets/icons/edukas.png" class="w-6 h-6 mb-0.5" alt="Disimpan">
            <span class="text-[10px] font-medium">Disimpan</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <img src="<?php echo BASE_URL; ?>/assets/icons/tak berkategori.png" class="w-6 h-6 mb-0.5" alt="Profil">
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </div>
</div>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => { navigator.serviceWorker.register('<?php echo BASE_URL; ?>/sw.js').catch(() => {}); });
    }
</script>

</body>
</html>
