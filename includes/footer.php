<!-- Bottom Navigation - Flat Icons (Single Color) -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 pb-safe shadow-[0_-2px_10px_rgba(0,0,0,0.03)]">
    <div class="flex justify-around items-center h-14 max-w-lg mx-auto">
        <a href="<?php echo BASE_URL; ?>/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <i class="fas fa-home text-lg mb-0.5"></i>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/category.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <i class="fas fa-th-large text-lg mb-0.5"></i>
            <span class="text-[10px] font-medium">Kategori</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/info.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <i class="fas fa-info-circle text-lg mb-0.5"></i>
            <span class="text-[10px] font-medium">Info</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/berita.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <i class="fas fa-newspaper text-lg mb-0.5"></i>
            <span class="text-[10px] font-medium">Berita</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-primary active:text-primary transition-colors">
            <i class="fas fa-user text-lg mb-0.5"></i>
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
