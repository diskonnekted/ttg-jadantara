<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
$pdo = connectDB();
$pageTitle = "Kategori";
$cat_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($cat_id) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$cat_id]);
    $cat = $stmt->fetch();
    if ($cat) {
        $pageTitle = $cat['name'];
        $stmt = $pdo->prepare("SELECT t.* FROM tutorials t WHERE t.category_id = ? ORDER BY created_at DESC");
        $stmt->execute([$cat_id]);
        $tutorials = $stmt->fetchAll();
    } else { $cat_id = null; }
}
if (!$cat_id) { $categories = getCategories($pdo); }
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-lg mx-auto px-5 py-5 mb-20">
    <h1 class="text-xl font-bold mb-4 text-gray-800"><?php echo $pageTitle; ?></h1>

    <?php if ($cat_id): ?>
        <div class="space-y-3">
            <?php if (empty($tutorials)): ?>
                <div class="text-center py-12 text-gray-400"><i class="far fa-folder-open text-4xl mb-3 block"></i><p class="text-sm">Belum ada tutorial.</p></div>
            <?php else: ?>
                <?php foreach($tutorials as $t): ?>
                <a href="tutorial.php?id=<?php echo $t['id']; ?>" class="flex gap-3 bg-white rounded-xl shadow-sm border border-gray-100 p-3 hover:shadow-md transition">
                    <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                        <?php if($t['image_path']): ?>
                            <img src="<?php echo BASE_URL . $t['image_path']; ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <div class="w-full h-full bg-primary/10 flex items-center justify-center"><i class="fas fa-image text-primary/30"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 flex flex-col justify-center min-w-0">
                        <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 leading-tight mb-1"><?php echo $t['title']; ?></h3>
                        <p class="text-[10px] text-gray-400"><?php echo timeAgo($t['created_at']); ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="mt-6 text-center">
            <a href="category.php" class="text-primary font-semibold text-sm hover:underline"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Kategori</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-3 gap-3">
            <?php foreach($categories as $c): ?>
            <a href="category.php?id=<?php echo $c['id']; ?>" class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition text-center border border-gray-100 flex flex-col items-center">
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center p-2 mb-2">
                    <img src="<?php echo getCategoryIcon($c['name']); ?>" class="w-full h-full object-contain" alt="">
                </div>
                <h3 class="font-semibold text-xs text-gray-700 leading-tight line-clamp-2"><?php echo $c['name']; ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
