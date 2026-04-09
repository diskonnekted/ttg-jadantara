<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
$pdo = connectDB();

$perPage = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$categories = getCategories($pdo);
$totalTutorials = $pdo->query("SELECT COUNT(*) FROM tutorials")->fetchColumn();
$totalPages = ceil($totalTutorials / $perPage);

$featured = $pdo->query("SELECT t.*, c.name as category_name FROM tutorials t JOIN categories c ON t.category_id = c.id WHERE t.image_path != '' ORDER BY RANDOM() LIMIT 1")->fetch();

$stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM tutorials t JOIN categories c ON t.category_id = c.id ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$latestTutorials = $stmt->fetchAll();

$pageTitle = "Beranda";
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero -->
<div class="bg-gradient-to-br from-primary to-primary-dark text-white pb-8 pt-5 px-5 rounded-b-3xl shadow-lg">
    <div class="max-w-lg mx-auto">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="text-white/70 text-xs">Selamat Datang di</p>
                <h1 class="text-xl font-bold">TTG Pondokrejo</h1>
            </div>
            <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" class="w-10 h-10 opacity-90" alt="">
        </div>
        <form action="search.php" method="GET" class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" placeholder="Cari tutorial..." class="w-full py-3 pl-10 pr-4 rounded-xl text-gray-800 bg-white shadow-md focus:outline-none text-sm">
        </form>
    </div>
</div>

<!-- Categories -->
<div class="max-w-lg mx-auto px-5 mt-6">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-base font-bold text-gray-800">Kategori</h2>
        <a href="category.php" class="text-xs text-primary font-semibold">Lihat Semua</a>
    </div>
    <div class="flex overflow-x-auto space-x-3 pb-2 no-scrollbar">
        <?php foreach($categories as $cat): ?>
        <a href="category.php?id=<?php echo $cat['id']; ?>" class="flex-shrink-0 flex flex-col items-center w-14">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100 p-1.5">
                <img src="<?php echo getCategoryIcon($cat['name']); ?>" class="w-full h-full object-contain" alt="">
            </div>
            <span class="text-[10px] font-medium text-gray-600 mt-1 text-center leading-tight line-clamp-2"><?php echo $cat['name']; ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured -->
<?php if($featured): ?>
<div class="max-w-lg mx-auto px-5 mt-6">
    <h2 class="text-base font-bold text-gray-800 mb-3">Pilihan Hari Ini</h2>
    <a href="tutorial.php?id=<?php echo $featured['id']; ?>" class="block relative h-40 rounded-2xl overflow-hidden shadow-md">
        <img src="<?php echo BASE_URL . $featured['image_path']; ?>" class="w-full h-full object-cover" alt="">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-4">
            <span class="bg-white/90 backdrop-blur text-gray-800 text-[10px] font-bold px-2 py-1 rounded-md mb-1 inline-block"><?php echo $featured['category_name']; ?></span>
            <h3 class="text-white font-bold text-base line-clamp-2"><?php echo $featured['title']; ?></h3>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- Latest Grid -->
<div class="max-w-lg mx-auto px-5 mt-6 mb-6">
    <h2 class="text-base font-bold text-gray-800 mb-3">Terbaru</h2>
    <div class="grid grid-cols-2 gap-3">
        <?php foreach($latestTutorials as $t): ?>
        <a href="tutorial.php?id=<?php echo $t['id']; ?>" class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition">
            <div class="relative h-28 bg-gray-100">
                <?php if($t['image_path']): ?>
                    <img src="<?php echo BASE_URL . $t['image_path']; ?>" class="w-full h-full object-cover" alt="">
                <?php else: ?>
                    <div class="w-full h-full bg-primary/10 flex items-center justify-center"><i class="fas fa-image text-primary/30 text-2xl"></i></div>
                <?php endif; ?>
            </div>
            <div class="p-2.5 flex items-start gap-2">
                <div class="w-7 h-7 bg-gray-50 rounded-lg flex items-center justify-center flex-shrink-0 p-0.5">
                    <img src="<?php echo getCategoryIcon($t['category_name']); ?>" class="w-full h-full object-contain" alt="">
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-[11px] text-gray-800 line-clamp-2 leading-tight mb-0.5"><?php echo $t['title']; ?></h3>
                    <p class="text-[9px] text-gray-400"><?php echo timeAgo($t['created_at']); ?></p>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Pagination -->
<?php if($totalPages > 1): ?>
<div class="max-w-lg mx-auto px-5 pb-24">
    <nav class="flex justify-center items-center gap-1.5">
        <?php if($page > 1): ?><a href="?page=<?php echo $page - 1; ?>" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-primary hover:text-white transition text-xs"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
        <?php for($i = max(1, $page-1); $i <= min($totalPages, $page+1); $i++): ?>
        <?php if($i == $page): ?><span class="w-9 h-9 flex items-center justify-center rounded-full bg-primary text-white font-bold text-xs shadow"><?php echo $i; ?></span>
        <?php else: ?><a href="?page=<?php echo $i; ?>" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-primary hover:text-white transition text-xs"><?php echo $i; ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if($page < $totalPages): ?><a href="?page=<?php echo $page + 1; ?>" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-primary hover:text-white transition text-xs"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
    </nav>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
