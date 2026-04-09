<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = "Info Aplikasi";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="pb-20 max-w-lg mx-auto">
    <!-- Hero -->
    <div class="bg-gradient-to-br from-primary to-primary-dark text-white px-6 pt-8 pb-12 rounded-b-3xl">
        <div class="text-center">
            <div class="w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-4">
                <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" class="w-12 h-12" alt="Logo TTG">
            </div>
            <h1 class="text-2xl font-bold mb-2">TTG Pondokrejo</h1>
            <p class="text-white/80 text-sm">Pusat Informasi Teknologi Tepat Guna</p>
        </div>
    </div>

    <!-- Content -->
    <div class="px-5 -mt-6">
        <!-- Manfaat -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-star text-primary"></i> Manfaat Aplikasi
            </h2>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-primary mt-0.5 flex-shrink-0"></i>
                    <span>Akses mudah ke <strong>teknologi tepat guna</strong> untuk pertanian, peternakan, dan pengolahan air di Pondokrejo</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-primary mt-0.5 flex-shrink-0"></i>
                    <span>Panduan praktis dengan langkah-langkah yang jelas dan mudah diikuti</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-primary mt-0.5 flex-shrink-0"></i>
                    <span>Download artikel PDF untuk dibaca <strong>secara offline</strong> tanpa koneksi internet</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-primary mt-0.5 flex-shrink-0"></i>
                    <span>Database lengkap dengan <strong>100+ tutorial</strong> dari berbagai kategori</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-primary mt-0.5 flex-shrink-0"></i>
                    <span>Ringan dan cepat, dirancang khusus untuk penggunaan di <strong>HP/smartphone</strong></span>
                </li>
            </ul>
        </div>

        <!-- Cara Penggunaan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-hand-pointer text-primary"></i> Cara Penggunaan
            </h2>
            <div class="space-y-4 text-sm text-gray-600">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center flex-shrink-0 font-bold text-xs">1</div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Cari Tutorial</h3>
                        <p>Gunakan kolom pencarian di beranda atau pilih kategori yang diinginkan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center flex-shrink-0 font-bold text-xs">2</div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Baca Artikel</h3>
                        <p>Klik pada artikel untuk membaca panduan lengkap dengan langkah-langkah</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center flex-shrink-0 font-bold text-xs">3</div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Download PDF</h3>
                        <p>Tap tombol <strong>"Download"</strong> di bawah artikel untuk menyimpan file PDF</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center flex-shrink-0 font-bold text-xs">4</div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Bagikan</h3>
                        <p>Gunakan tombol <strong>"Bagikan"</strong> untuk mengirim tutorial via WhatsApp</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tentang Kami -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-building text-primary"></i> Tentang Aplikasi
            </h2>
            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                Aplikasi TTG Pondokrejo dibangun sebagai media transfer pengetahuan teknologi tepat guna untuk masyarakat Kalurahan Pondokrejo, Sleman, Yogyakarta.
            </p>
            <p class="text-sm text-gray-600 leading-relaxed">
                Dikembangkan oleh <strong>Clasnet Group</strong> sebagai mitra transformasi digital Kalurahan Pondokrejo.
            </p>
        </div>

        <!-- Logo Clasnet -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            <a href="https://www.clasnet.co.id" target="_blank" class="inline-block hover:opacity-80 transition-opacity">
                <img src="<?php echo BASE_URL; ?>/assets/images/logo-clasnet.jpg" class="h-12 w-auto mx-auto mb-3" alt="Clasnet Group">
                <p class="text-xs text-gray-500">Kunjungi clasnet.co.id</p>
            </a>
        </div>

        <!-- Version -->
        <div class="text-center mt-6 mb-8">
            <p class="text-xs text-gray-400">Versi 1.0.0 • 2026</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
