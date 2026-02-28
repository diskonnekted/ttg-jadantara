<?php
// public/admin/index.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = connectDB();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Delete image
    $stmt = $pdo->prepare("SELECT image_path FROM tutorials WHERE id = ?");
    $stmt->execute([$id]);
    $tutorial = $stmt->fetch();
    if ($tutorial && $tutorial['image_path']) {
        @unlink(__DIR__ . '/../../public' . $tutorial['image_path']);
    }
    
    // Steps and materials cascade delete due to foreign key constraints
    $stmt = $pdo->prepare("DELETE FROM tutorials WHERE id = ?");
    $stmt->execute([$id]);
    redirect('/admin/index.php?msg=deleted');
}

// Fetch Stats
$stats = [
    'tutorials' => $pdo->query("SELECT COUNT(*) FROM tutorials")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'views' => 1250 // Dummy stats for now, or implement view counter later
];

// Fetch Tutorials
$search = $_GET['q'] ?? '';
$sql = "SELECT t.*, c.name as category_name 
        FROM tutorials t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.title LIKE ? 
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%"]);
$tutorials = $stmt->fetchAll();

$pageTitle = "Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black-forest">Ringkasan</h1>
                <p class="text-black-forest/60">Statistik dan aktivitas terbaru website.</p>
            </div>
            <a href="create.php" class="bg-primary hover:bg-black-forest text-white px-5 py-2.5 rounded-xl shadow-lg shadow-olive-leaf/20 transition flex items-center justify-center gap-2 font-medium">
                <i class="fas fa-pen"></i> Tulis Tutorial
            </a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div x-data="{ show: true }" x-show="show" class="bg-olive-leaf/10 border border-olive-leaf/20 text-olive-leaf px-4 py-3 rounded-xl mb-6 flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i> Tutorial berhasil dihapus.</span>
                <button @click="show = false" class="text-olive-leaf hover:text-black-forest"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-olive-leaf/10 flex flex-col">
                <div class="text-black-forest/60 text-sm font-medium mb-1">Total Tutorial</div>
                <div class="text-3xl font-bold text-black-forest"><?php echo $stats['tutorials']; ?></div>
                <div class="mt-auto pt-2 text-xs text-olive-leaf font-medium">+ Updated</div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-olive-leaf/10 flex flex-col">
                <div class="text-black-forest/60 text-sm font-medium mb-1">Kategori</div>
                <div class="text-3xl font-bold text-black-forest"><?php echo $stats['categories']; ?></div>
                <div class="mt-auto pt-2 text-xs text-black-forest/40 font-medium">Aktif</div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-olive-leaf/10 flex flex-col">
                <div class="text-black-forest/60 text-sm font-medium mb-1">Total Pengunjung</div>
                <div class="text-3xl font-bold text-black-forest"><?php echo number_format($stats['views']); ?></div>
                <div class="mt-auto pt-2 text-xs text-olive-leaf font-medium"><i class="fas fa-arrow-up"></i> 12% bulan ini</div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-olive-leaf/10 flex flex-col">
                <div class="text-black-forest/60 text-sm font-medium mb-1">Admin</div>
                <div class="text-3xl font-bold text-black-forest"><?php echo $stats['users']; ?></div>
                <div class="mt-auto pt-2 text-xs text-black-forest/40 font-medium">Terdaftar</div>
            </div>
        </div>

        <!-- Content Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-olive-leaf/10 overflow-hidden">
            <div class="p-6 border-b border-olive-leaf/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="font-bold text-lg text-black-forest">Daftar Konten</h2>
                
                <form method="GET" class="relative w-full md:w-64">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari tutorial..." 
                           class="w-full pl-10 pr-4 py-2 bg-cornsilk/50 border border-olive-leaf/20 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition">
                    <i class="fas fa-search absolute left-3.5 top-2.5 text-gray-400 text-xs"></i>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                            <th class="p-5">Tutorial</th>
                            <th class="p-5">Kategori</th>
                            <th class="p-5">Status</th>
                            <th class="p-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($tutorials)): ?>
                            <tr><td colspan="4" class="p-8 text-center text-gray-500 italic">Tidak ada data ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach($tutorials as $t): ?>
                            <tr class="hover:bg-gray-50/80 transition group">
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0" style="width: 3rem; height: 3rem;">
                                            <?php if($t['image_path']): ?>
                                                <img src="<?php echo BASE_URL . $t['image_path']; ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm line-clamp-1 group-hover:text-primary transition"><?php echo $t['title']; ?></div>
                                            <div class="text-xs text-gray-500 mt-0.5"><i class="far fa-clock mr-1"></i> <?php echo date('d M Y', strtotime($t['created_at'])); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?php echo $t['category_name']; ?>
                                    </span>
                                </td>
                                <td class="p-5">
                                    <span class="text-xs font-medium text-gray-500 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Published
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?php echo BASE_URL; ?>/tutorial.php?id=<?php echo $t['id']; ?>" target="_blank" class="p-2 text-gray-400 hover:text-primary hover:bg-green-50 rounded-lg transition" title="Lihat">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $t['id']; ?>" class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="?delete=<?php echo $t['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 text-xs text-gray-500 flex justify-between items-center bg-gray-50/30">
                <span>Menampilkan <?php echo count($tutorials); ?> dari <?php echo $stats['tutorials']; ?> data</span>
                <!-- Simple Pagination (Static for now) -->
                <div class="flex gap-1">
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50" disabled>Prev</button>
                    <button class="px-3 py-1 bg-primary text-white rounded">1</button>
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50" disabled>Next</button>
                </div>
            </div>
        </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
