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
<div class="relative bg-gradient-to-br from-primary to-green-600 text-white pb-10 pt-6 px-6 rounded-b-[2.5rem] shadow-xl overflow-hidden">
    <!-- Decorative Circles -->
    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

    <div class="relative z-10">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-green-100 text-sm font-medium mb-1">Selamat Datang di</p>
                <h1 class="text-3xl font-extrabold tracking-tight">TTG Pondokrejo</h1>
            </div>
            <div class="bg-white/20 p-2 rounded-full backdrop-blur-sm">
                <i class="fas fa-leaf text-xl"></i>
            </div>
        </div>

        <p class="mb-6 text-green-50 text-sm leading-relaxed max-w-xs">
            Pusat informasi teknologi tepat guna untuk kemandirian dan kemajuan desa kita.
        </p>
        
        <form action="search.php" method="GET" class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors"></i>
            </div>
            <input type="text" name="q" placeholder="Cari panduan (misal: hidroponik)..." 
                   class="w-full py-4 pl-11 pr-4 rounded-2xl text-gray-800 bg-white shadow-lg focus:outline-none focus:ring-4 focus:ring-green-400/30 transition-all placeholder-gray-400 font-medium">
        </form>
    </div>
</div>

<!-- Categories (Horizontal Scroll) -->
<div class="mt-8 px-4">
    <div class="flex justify-between items-end mb-4 px-2">
        <h2 class="text-lg font-bold text-gray-800">Kategori Populer</h2>
        <a href="category.php" class="text-xs text-primary font-bold hover:underline">Lihat Semua</a>
    </div>
    
    <div class="flex overflow-x-auto space-x-4 pb-4 px-2 no-scrollbar snap-x">
        <?php foreach($categories as $cat): ?>
        <a href="category.php?id=<?php echo $cat['id']; ?>" class="snap-start flex-shrink-0 flex flex-col items-center group">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-500 shadow-sm border border-gray-100 group-hover:border-primary group-hover:bg-green-50 group-hover:text-primary transition-all duration-300 p-3">
                <img src="<?php echo getCategoryIcon($cat['name']); ?>" class="w-full h-full object-contain filter group-hover:brightness-110 transition">
            </div>
            <span class="text-xs font-medium text-gray-600 mt-2 group-hover:text-primary transition-colors"><?php echo $cat['name']; ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Section -->
<?php if($featured): ?>
<div class="px-6 mt-4">
    <h2 class="text-lg font-bold text-gray-800 mb-4 px-2">Pilihan Hari Ini</h2>
    <a href="tutorial.php?id=<?php echo $featured['id']; ?>" class="block relative h-48 rounded-3xl overflow-hidden shadow-md group">
        <?php if($featured['image_path']): ?>
            <img src="<?php echo BASE_URL . $featured['image_path']; ?>" alt="<?php echo $featured['title']; ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <i class="fas fa-image text-4xl text-gray-400"></i>
            </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-5">
            <span class="bg-primary/90 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md mb-2 inline-block shadow-sm">
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

<!-- Latest Tutorials (Masonry-ish) -->
<div class="px-6 mt-8 pb-24">
    <h2 class="text-lg font-bold text-gray-800 mb-4 px-2">Terbaru</h2>
    
    <div class="grid grid-cols-1 gap-5">
        <?php if(empty($latestTutorials)): ?>
            <div class="text-center py-10 bg-white rounded-3xl shadow-sm border border-dashed border-gray-300">
                <i class="far fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada tutorial.</p>
            </div>
        <?php else: ?>
            <?php foreach($latestTutorials as $tutorial): ?>
            <a href="tutorial.php?id=<?php echo $tutorial['id']; ?>" class="flex bg-white p-3 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-100 transition-all duration-300 items-start gap-4 group">
                <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-xl overflow-hidden relative">
                    <?php if($tutorial['image_path']): ?>
                        <img src="<?php echo BASE_URL . $tutorial['image_path']; ?>" alt="<?php echo $tutorial['title']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fas fa-image text-xl"></i>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($tutorial['video_url'])): ?>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition">
                            <div class="w-8 h-8 bg-white/90 rounded-full flex items-center justify-center shadow-sm backdrop-blur-sm">
                                <i class="fas fa-play text-xs text-red-500 pl-0.5"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1 py-1 min-w-0">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-[10px] font-bold text-primary uppercase tracking-wide">
                            <?php echo $tutorial['category_name']; ?>
                        </span>
                        <span class="text-[10px] text-gray-400">
                            <?php echo date('d M', strtotime($tutorial['created_at'])); ?>
                        </span>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                        <?php echo $tutorial['title']; ?>
                    </h3>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                            <i class="far fa-clock mr-1"></i> <?php echo $tutorial['duration']; ?>m
                        </span>
                        <?php if(!empty($tutorial['pdf_path'])): ?>
                            <span class="text-[10px] text-red-500 bg-red-50 px-2 py-1 rounded-md border border-red-100">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="mt-8 text-center">
        <a href="search.php" class="inline-block px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-full text-sm hover:bg-gray-200 transition">
            Lihat Semua Panduan
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
