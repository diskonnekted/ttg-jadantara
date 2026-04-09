<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = connectDB();

// Get Berita category ID
$stmt = $pdo->prepare("SELECT id FROM categories WHERE name = 'Berita'");
$stmt->execute();
$beritaCat = $stmt->fetch();

$beritaId = $beritaCat ? $beritaCat['id'] : null;
$articles = [];

if ($beritaId) {
    $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM tutorials t JOIN categories c ON t.category_id = c.id WHERE t.category_id = ? ORDER BY t.created_at DESC");
    $stmt->execute([$beritaId]);
    $articles = $stmt->fetchAll();
}

$pageTitle = "Berita";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-lg mx-auto px-5 py-5 mb-20">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-800">Berita Terbaru</h1>
        <div class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-semibold">
            <?php echo count($articles); ?> Artikel
        </div>
    </div>

    <?php if (empty($articles)): ?>
        <div class="text-center py-16 bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-newspaper text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Belum Ada Berita</h3>
            <p class="text-sm text-gray-500">Berita dan informasi terbaru akan segera ditambahkan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($articles as $art): ?>
            <a href="tutorial.php?id=<?php echo $art['id']; ?>" class="block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="flex items-center p-3 gap-3">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                        <?php if ($art['image_path']): ?>
                            <img src="<?php echo BASE_URL . $art['image_path']; ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 leading-tight mb-1"><?php echo htmlspecialchars($art['title']); ?></h3>
                        <div class="flex items-center text-[10px] text-gray-400 gap-2">
                            <span><i class="far fa-calendar mr-1"></i> <?php echo date('d M Y', strtotime($art['created_at'])); ?></span>
                            <span>•</span>
                            <span>Baca selengkapnya</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-gray-300">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
