<?php
// public/category.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = connectDB();
$pageTitle = "Kategori";

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($category_id) {
    // Fetch Category Name
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    
    if ($category) {
        $pageTitle = $category['name'];
        // Fetch Tutorials in Category
        $stmt = $pdo->prepare("SELECT t.*, c.name as category_name 
                               FROM tutorials t 
                               JOIN categories c ON t.category_id = c.id 
                               WHERE t.category_id = ? 
                               ORDER BY created_at DESC");
        $stmt->execute([$category_id]);
        $tutorials = $stmt->fetchAll();
    } else {
        $category_id = null; // Category not found, fallback to list
    }
}

if (!$category_id) {
    // Fetch All Categories
    $categories = getCategories($pdo);
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-4 py-6 mb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800"><?php echo $pageTitle; ?></h1>

    <?php if ($category_id): ?>
        <!-- List Tutorials in Category -->
        <div class="space-y-4">
            <?php if (empty($tutorials)): ?>
                <div class="text-center py-10 text-gray-500">
                    <i class="far fa-folder-open text-4xl mb-2 block"></i>
                    Belum ada tutorial di kategori ini.
                </div>
            <?php else: ?>
                <?php foreach($tutorials as $tutorial): ?>
                <a href="tutorial.php?id=<?php echo $tutorial['id']; ?>" class="block bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition border border-gray-100 flex gap-4">
                    <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                         <?php if($tutorial['image_path']): ?>
                            <img src="<?php echo BASE_URL . $tutorial['image_path']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 mb-1 line-clamp-2"><?php echo $tutorial['title']; ?></h3>
                        <p class="text-xs text-gray-500 mb-2 line-clamp-2"><?php echo $tutorial['description']; ?></p>
                        <div class="flex items-center text-xs text-gray-400 gap-3">
                            <span><i class="far fa-clock"></i> <?php echo $tutorial['duration']; ?>m</span>
                            <span class="<?php echo ($tutorial['difficulty'] == 'Easy' ? 'text-green-500' : ($tutorial['difficulty'] == 'Medium' ? 'text-yellow-500' : 'text-red-500')); ?>">
                                <i class="fas fa-signal"></i> <?php echo $tutorial['difficulty']; ?>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="mt-8 text-center">
            <a href="category.php" class="text-primary font-semibold hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kategori
            </a>
        </div>

    <?php else: ?>
        <!-- List All Categories -->
        <div class="grid grid-cols-2 gap-4">
            <?php foreach($categories as $cat): ?>
            <a href="category.php?id=<?php echo $cat['id']; ?>" class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition text-center border border-gray-100 flex flex-col items-center">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center text-primary text-2xl mb-3">
                    <i class="<?php echo $cat['icon_class'] ?? 'fas fa-folder'; ?>"></i>
                </div>
                <h3 class="font-bold text-gray-700"><?php echo $cat['name']; ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
