<?php
// public/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = connectDB();

// Fetch Categories
$categories = getCategories($pdo);

// Fetch Featured Tutorial (Random or specific logic)
$stmt = $pdo->query("SELECT t.*, c.name as category_name 
                     FROM tutorials t 
                     JOIN categories c ON t.category_id = c.id 
                     ORDER BY RANDOM() LIMIT 1");
$featured = $stmt->fetch();

// Fetch Latest Tutorials (excluding featured if possible, but for simplicity just latest 10)
$stmt = $pdo->query("SELECT t.*, c.name as category_name, c.icon_class 
                     FROM tutorials t 
                     JOIN categories c ON t.category_id = c.id 
                     ORDER BY created_at DESC LIMIT 10");
$latestTutorials = $stmt->fetchAll();

$pageTitle = "Beranda";
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Modern Hero Section -->
<div class="relative bg-gradient-to-br from-olive-leaf to-black-forest text-cornsilk pb-10 pt-6 px-6 rounded-b-[2.5rem] shadow-xl overflow-hidden">
    <!-- Decorative Circles -->
    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

    <div class="relative z-10">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-cornsilk/80 text-sm font-medium mb-1">Selamat Datang di</p>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">TTG Pondokrejo</h1>
            </div>
            <div class="bg-white/20 p-2 rounded-full backdrop-blur-sm text-white">
                <i class="fas fa-leaf text-xl"></i>
            </div>
        </div>

        <p class="mb-6 text-cornsilk/90 text-sm leading-relaxed max-w-xs">
            Pusat informasi teknologi tepat guna untuk kemandirian dan kemajuan desa kita.
        </p>
        
        <form action="search.php" method="GET" class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors"></i>
            </div>
            <input type="text" name="q" placeholder="Cari panduan (misal: hidroponik)..." 
                   class="w-full py-4 pl-11 pr-4 rounded-2xl text-black-forest bg-cornsilk shadow-lg focus:outline-none focus:ring-4 focus:ring-sunlit-clay/50 transition-all placeholder-gray-500 font-medium">
        </form>
    </div>
</div>

<!-- Categories (Horizontal Scroll) -->
<div class="mt-8 px-4">
    <div class="flex justify-between items-end mb-4 px-2">
        <h2 class="text-lg font-bold text-black-forest">Kategori Populer</h2>
        <a href="category.php" class="text-xs text-olive-leaf font-bold hover:underline">Lihat Semua</a>
    </div>
    
    <div class="flex overflow-x-auto space-x-4 pb-4 px-2 no-scrollbar snap-x">
        <?php foreach($categories as $cat): ?>
        <a href="category.php?id=<?php echo $cat['id']; ?>" class="snap-start flex-shrink-0 flex flex-col items-center group">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-500 shadow-sm border border-gray-100 group-hover:border-olive-leaf group-hover:bg-cornsilk group-hover:text-olive-leaf transition-all duration-300 p-3">
                <img src="<?php echo getCategoryIcon($cat['name']); ?>" class="w-full h-full object-contain filter group-hover:brightness-110 transition">
            </div>
            <span class="text-xs font-medium text-gray-600 mt-2 group-hover:text-olive-leaf transition-colors"><?php echo $cat['name']; ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Section -->
<?php if($featured): ?>
<div class="px-6 mt-4">
    <h2 class="text-lg font-bold text-black-forest mb-4 px-2">Pilihan Hari Ini</h2>
    <a href="tutorial.php?id=<?php echo $featured['id']; ?>" class="block relative h-48 rounded-3xl overflow-hidden shadow-md group">
        <?php if($featured['image_path']): ?>
            <img src="<?php echo BASE_URL . $featured['image_path']; ?>" alt="<?php echo $featured['title']; ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <i class="fas fa-image text-4xl text-gray-400"></i>
            </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black-forest/90 via-black-forest/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-5">
            <span class="bg-sunlit-clay/90 backdrop-blur-sm text-black-forest text-[10px] font-bold px-2 py-1 rounded-md mb-2 inline-block shadow-sm">
                <?php echo $featured['category_name']; ?>
            </span>
            <h3 class="text-white font-bold text-lg leading-tight line-clamp-2 mb-1 drop-shadow-md">
                <?php echo $featured['title']; ?>
            </h3>
            <div class="flex items-center text-gray-300 text-xs gap-3">
                <span><i class="far fa-clock mr-1"></i> <?php echo $featured['duration']; ?> min</span>
                <span><i class="fas fa-signal mr-1"></i> <?php echo $featured['difficulty']; ?></span>
            </div>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- Latest Tutorials (Masonry Grid) -->
<div class="px-6 mt-8 mb-24">
    <h2 class="text-lg font-bold text-black-forest mb-4 px-2">Terbaru</h2>
    <div class="columns-2 gap-4 space-y-4">
        <?php foreach($latestTutorials as $tutorial): ?>
        <a href="tutorial.php?id=<?php echo $tutorial['id']; ?>" class="break-inside-avoid block bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition group border border-gray-100">
            <div class="relative">
                <?php if($tutorial['image_path']): ?>
                    <img src="<?php echo BASE_URL . $tutorial['image_path']; ?>" alt="<?php echo $tutorial['title']; ?>" class="w-full h-32 object-cover">
                <?php else: ?>
                    <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-400">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                <?php endif; ?>
                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm p-1.5 rounded-lg shadow-sm">
                    <i class="<?php echo $tutorial['icon_class'] ?? 'fas fa-book'; ?> text-olive-leaf text-xs"></i>
                </div>
            </div>
            <div class="p-3">
                <h3 class="font-bold text-sm text-black-forest mb-1 line-clamp-2 group-hover:text-olive-leaf transition-colors"><?php echo $tutorial['title']; ?></h3>
                <p class="text-xs text-gray-500 line-clamp-2 mb-2"><?php echo $tutorial['description']; ?></p>
                <div class="flex items-center justify-between text-[10px] text-gray-400">
                    <span><?php echo timeAgo($tutorial['created_at']); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
