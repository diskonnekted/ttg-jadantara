<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }

$pdo = connectDB();
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM tutorials t JOIN categories c ON t.category_id = c.id WHERE t.id = ?");
$stmt->execute([$id]);
$tutorial = $stmt->fetch();
if (!$tutorial) die("Tutorial tidak ditemukan.");

$stmt = $pdo->prepare("SELECT * FROM steps WHERE tutorial_id = ? ORDER BY step_number ASC");
$stmt->execute([$id]);
$allSteps = $stmt->fetchAll();
$steps = array_filter($allSteps, function($s) { return !empty($s['title']) || !empty($s['content']); });

$stmt = $pdo->prepare("SELECT * FROM materials WHERE tutorial_id = ?");
$stmt->execute([$id]);
$materials = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT id, title, image_path FROM tutorials WHERE category_id = ? AND id != ? ORDER BY RANDOM() LIMIT 3");
$stmt->execute([$tutorial['category_id'], $id]);
$related = $stmt->fetchAll();

$pageTitle = $tutorial['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .article-text, .article-text * { font-weight: 400 !important; font-variant-numeric: normal; }
    .article-text h1, .article-text h2, .article-text h3, .article-text h4 { font-weight: 700 !important; }
</style>

<div class="pb-20">
    <!-- Hero -->
    <div class="relative h-56 bg-gray-200">
        <?php if($tutorial['image_path']): ?>
            <img src="<?php echo BASE_URL . $tutorial['image_path']; ?>" class="w-full h-full object-cover" alt="">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center"><i class="fas fa-book-open text-5xl text-white/50"></i></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
        <a href="javascript:history.back()" class="absolute top-4 left-4 w-9 h-9 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow"><i class="fas fa-arrow-left text-gray-700 text-sm"></i></a>
        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full shadow-sm flex items-center gap-2">
            <img src="<?php echo getCategoryIcon($tutorial['category_name']); ?>" class="w-4 h-4" alt="">
            <span class="text-xs font-semibold text-gray-700"><?php echo $tutorial['category_name']; ?></span>
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-5">
            <h1 class="text-xl font-bold text-white leading-tight mb-2 drop-shadow"><?php echo htmlspecialchars($tutorial['title']); ?></h1>
            <div class="flex items-center text-white/70 text-xs gap-4">
                <span><i class="far fa-clock mr-1"></i> 10-15 min</span>
                <span><i class="far fa-calendar mr-1"></i> <?php echo date('d M Y', strtotime($tutorial['created_at'])); ?></span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 py-5 max-w-2xl mx-auto">
        <?php if(!empty($tutorial['description'])): ?>
        <div class="bg-primary/5 border-l-4 border-primary rounded-r-lg p-4 mb-6">
            <p class="text-sm text-gray-700 leading-relaxed font-medium"><?php echo nl2br(htmlspecialchars($tutorial['description'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($materials)): ?>
        <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-100">
            <h3 class="font-bold text-blue-800 mb-3 flex items-center text-sm"><i class="fas fa-toolbox mr-2"></i> Alat & Bahan</h3>
            <ul class="space-y-2">
                <?php foreach($materials as $m): ?>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <span class="text-blue-400 mt-1">•</span>
                    <div>
                        <span class="font-medium"><?php echo htmlspecialchars($m['name']); ?> <?php if($m['quantity']): ?><span class="text-blue-600 text-xs">(<?php echo htmlspecialchars($m['quantity']); ?>)</span><?php endif; ?></span>
                        <?php if($m['local_source']): ?><span class="text-xs text-gray-500 block mt-0.5"><i class="fas fa-map-marker-alt mr-1 text-blue-400"></i> <?php echo htmlspecialchars($m['local_source']); ?></span><?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Article Body -->
        <div class="article-content">
            <?php foreach($steps as $step): ?>
            <?php if(!empty($step['title'])): ?>
            <h2 class="text-lg font-bold text-gray-900 mt-6 mb-3"><?php echo htmlspecialchars($step['title']); ?></h2>
            <?php endif; ?>
            <?php if(!empty($step['content'])): ?>
            <div class="mb-4"><?php echo renderContent($step['content']); ?></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if(!empty($tutorial['pdf_path'])): ?>
        <a href="<?php echo BASE_URL . $tutorial['pdf_path']; ?>" target="_blank" class="flex items-center gap-3 bg-red-50 border border-red-100 p-4 rounded-xl mt-6 hover:bg-red-100 transition">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-red-500"><i class="fas fa-file-pdf text-xl"></i></div>
            <div class="flex-1"><h4 class="font-bold text-gray-800 text-sm">Download PDF</h4><p class="text-xs text-gray-500">Simpan untuk dibaca offline</p></div>
            <i class="fas fa-download text-gray-400"></i>
        </a>
        <?php endif; ?>

        <div class="flex gap-3 mt-8">
            <a href="https://wa.me/?text=<?php echo urlencode($tutorial['title'] . ' - ' . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 transition shadow-sm"><i class="fab fa-whatsapp text-lg"></i> Bagikan</a>
        </div>

        <?php if(!empty($related)): ?>
        <div class="mt-8">
            <h3 class="font-bold text-gray-900 text-lg mb-4">Artikel Terkait</h3>
            <div class="space-y-3">
                <?php foreach($related as $r): ?>
                <a href="tutorial.php?id=<?php echo $r['id']; ?>" class="flex gap-3 bg-white rounded-xl shadow-sm border border-gray-100 p-3 hover:shadow-md transition">
                    <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                        <?php if($r['image_path']): ?><img src="<?php echo BASE_URL . $r['image_path']; ?>" class="w-full h-full object-cover" alt=""><?php else: ?><div class="w-full h-full bg-primary/10 flex items-center justify-center"><i class="fas fa-image text-primary/30"></i></div><?php endif; ?>
                    </div>
                    <div class="flex-1 flex items-center"><h4 class="font-semibold text-sm text-gray-800 line-clamp-2"><?php echo htmlspecialchars($r['title']); ?></h4></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
