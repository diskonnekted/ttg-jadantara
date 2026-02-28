<?php
// public/search.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = connectDB();
$q = isset($_GET['q']) ? sanitize($_GET['q']) : '';

$tutorials = [];
if ($q) {
    $stmt = $pdo->prepare("SELECT * FROM tutorials WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC");
    $term = "%$q%";
    $stmt->execute([$term, $term]);
    $tutorials = $stmt->fetchAll();
}

$pageTitle = "Pencarian: " . htmlspecialchars($q);
require_once __DIR__ . '/../includes/header.php';
?>

<div class="px-4 py-6 mb-20">
    <h1 class="text-xl font-bold mb-4 text-gray-800">Hasil Pencarian: "<?php echo htmlspecialchars($q); ?>"</h1>

    <?php if (empty($tutorials)): ?>
        <p class="text-gray-500 text-center py-8">Tidak ditemukan tutorial yang cocok.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
