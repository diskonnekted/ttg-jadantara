<?php
// public/admin/categories.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = connectDB();
$error = '';
$success = '';

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = sanitize($_POST['name']);
    $icon = sanitize($_POST['icon']);
    
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, icon_class) VALUES (?, ?)");
        if ($stmt->execute([$name, $icon])) {
            $success = "Kategori berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan kategori.";
        }
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name']);
    $icon = sanitize($_POST['icon']);
    
    if ($name && $id) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon_class = ? WHERE id = ?");
        if ($stmt->execute([$name, $icon, $id])) {
            $success = "Kategori berhasil diupdate.";
        } else {
            $error = "Gagal update kategori.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Check if used
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tutorials WHERE category_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        $error = "Kategori tidak bisa dihapus karena masih digunakan oleh tutorial.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success = "Kategori berhasil dihapus.";
        }
    }
}

$categories = getCategories($pdo);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 pb-20">

<div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-2xl font-bold">Kelola Kategori</h1>
        </div>
        <button onclick="document.getElementById('createModal').showModal()" class="bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Tambah
        </button>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase">
                    <th class="p-4">Icon</th>
                    <th class="p-4">Nama Kategori</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($categories as $cat): ?>
                <tr class="hover:bg-gray-50 transition" x-data="{ editing: false, name: '<?php echo $cat['name']; ?>', icon: '<?php echo $cat['icon_class']; ?>' }">
                    <!-- View Mode -->
                    <td class="p-4 w-16 text-center" x-show="!editing">
                        <i class="<?php echo $cat['icon_class']; ?> text-primary text-xl"></i>
                    </td>
                    <td class="p-4 font-medium" x-show="!editing">
                        <?php echo $cat['name']; ?>
                    </td>
                    <td class="p-4 text-right space-x-2" x-show="!editing">
                        <button @click="editing = true" class="text-yellow-500 hover:text-yellow-700">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Hapus kategori ini?')" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>

                    <!-- Edit Mode -->
                    <td colspan="3" class="p-4 bg-yellow-50" x-show="editing">
                        <form method="POST" class="flex gap-2 items-center">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                            <input type="text" name="icon" x-model="icon" class="border rounded p-2 w-1/3" placeholder="Icon Class (fa-...)">
                            <input type="text" name="name" x-model="name" class="border rounded p-2 w-full" placeholder="Nama Kategori">
                            <button type="submit" class="bg-green-500 text-white p-2 rounded"><i class="fas fa-save"></i></button>
                            <button type="button" @click="editing = false" class="bg-gray-300 text-gray-700 p-2 rounded"><i class="fas fa-times"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<dialog id="createModal" class="p-6 rounded-xl shadow-xl backdrop:bg-black/50 w-full max-w-md">
    <h2 class="text-xl font-bold mb-4">Tambah Kategori Baru</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
            <input type="text" name="name" required class="w-full border rounded p-2">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Icon Class (FontAwesome)</label>
            <input type="text" name="icon" placeholder="fas fa-folder" class="w-full border rounded p-2">
            <p class="text-xs text-gray-500 mt-1">Contoh: fas fa-leaf, fas fa-tools</p>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('createModal').close()" class="bg-gray-200 px-4 py-2 rounded">Batal</button>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</dialog>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
