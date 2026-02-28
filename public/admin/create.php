<?php
// public/admin/create.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = connectDB();
$categories = getCategories($pdo);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process Form
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $difficulty = sanitize($_POST['difficulty']);
    $duration = (int)$_POST['duration'];
    $video_url = sanitize($_POST['video_url'] ?? '');
    $slug = createSlug($title) . '-' . uniqid();
    $author_id = $_SESSION['user_id'];

    // Image Upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['image']);
        if ($upload) $image_path = $upload;
    }

    // PDF Upload
    $pdf_path = '';
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['pdf_file']);
        if ($upload) $pdf_path = $upload;
    }

    if (!$error) {
        try {
            $pdo->beginTransaction();

            // Insert Tutorial
            $stmt = $pdo->prepare("INSERT INTO tutorials (title, slug, description, difficulty, duration, category_id, author_id, image_path, video_url, pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $description, $difficulty, $duration, $category_id, $author_id, $image_path, $video_url, $pdf_path]);
            $tutorial_id = $pdo->lastInsertId();

            // Insert Materials
            if (isset($_POST['materials']) && is_array($_POST['materials'])) {
                $stmtMat = $pdo->prepare("INSERT INTO materials (tutorial_id, name, quantity, local_source) VALUES (?, ?, ?, ?)");
                foreach ($_POST['materials'] as $mat) {
                    if (!empty($mat['name'])) {
                        $stmtMat->execute([$tutorial_id, sanitize($mat['name']), sanitize($mat['quantity']), sanitize($mat['local_source'])]);
                    }
                }
            }

            // Insert Steps
            if (isset($_POST['steps']) && is_array($_POST['steps'])) {
                $stmtStep = $pdo->prepare("INSERT INTO steps (tutorial_id, step_number, title, content, image_path) VALUES (?, ?, ?, ?, ?)");
                foreach ($_POST['steps'] as $index => $step) {
                    if (!empty($step['title'])) {
                        $step_image = '';
                        // Handle Step Image Upload
                        // Note: Files array structure is tricky with dynamic inputs
                        // $_FILES['steps']['name'][$index]['image']
                        if (isset($_FILES['steps']['name'][$index]['image']) && $_FILES['steps']['error'][$index]['image'] === UPLOAD_ERR_OK) {
                            $file = [
                                'name' => $_FILES['steps']['name'][$index]['image'],
                                'type' => $_FILES['steps']['type'][$index]['image'],
                                'tmp_name' => $_FILES['steps']['tmp_name'][$index]['image'],
                                'error' => $_FILES['steps']['error'][$index]['image'],
                                'size' => $_FILES['steps']['size'][$index]['image']
                            ];
                            $up = uploadImage($file);
                            if ($up) $step_image = $up;
                        }

                        $stmtStep->execute([$tutorial_id, $index + 1, sanitize($step['title']), sanitize($step['content']), $step_image]);
                    }
                }
            }

            $pdo->commit();
            redirect('/admin/index.php?msg=created');

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>
<?php
$pageTitle = "Buat Tutorial";
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Buat Tutorial Baru</h1>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6 border-l-4 border-red-500 shadow-sm"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-8" x-data="{ materials: [], steps: [] }">
        
        <!-- Basic Info -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="font-bold text-lg mb-4 border-b pb-2">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tutorial</label>
                    <input type="text" name="title" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kesulitan</label>
                    <select name="difficulty" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                        <option value="Easy">Mudah (Easy)</option>
                        <option value="Medium">Sedang (Medium)</option>
                        <option value="Hard">Sulit (Hard)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                    <input type="number" name="duration" value="15" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Video Youtube (URL)</label>
                    <input type="url" name="video_url" placeholder="https://youtube.com/watch?v=..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran PDF (Opsional)</label>
                    <input type="file" name="pdf_file" accept="application/pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2"></textarea>
                </div>
            </div>
        </div>

        <!-- Materials (Dynamic) -->
        <div x-data="{ materials: [{id: 1}] }" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="font-bold text-lg">Alat & Bahan</h2>
                <button type="button" @click="materials.push({id: Date.now()})" class="text-sm text-green-600 hover:text-green-800 font-semibold">+ Tambah Item</button>
            </div>
            
            <div class="space-y-3">
                <template x-for="(mat, index) in materials" :key="mat.id">
                    <div class="flex gap-2 items-start bg-gray-50 p-3 rounded">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-2">
                            <input type="text" :name="'materials['+index+'][name]'" placeholder="Nama Alat/Bahan" required class="w-full border-gray-300 rounded border p-2 text-sm">
                            <input type="text" :name="'materials['+index+'][quantity]'" placeholder="Jumlah (mis: 2 buah)" class="w-full border-gray-300 rounded border p-2 text-sm">
                            <input type="text" :name="'materials['+index+'][local_source]'" placeholder="Beli di mana? (Opsional)" class="w-full border-gray-300 rounded border p-2 text-sm">
                        </div>
                        <button type="button" @click="materials = materials.filter(m => m.id !== mat.id)" class="text-red-500 hover:text-red-700 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Steps (Dynamic) -->
        <div x-data="{ steps: [{id: 1}] }" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="font-bold text-lg">Langkah-langkah</h2>
                <button type="button" @click="steps.push({id: Date.now()})" class="text-sm text-green-600 hover:text-green-800 font-semibold">+ Tambah Langkah</button>
            </div>
            
            <div class="space-y-6">
                <template x-for="(step, index) in steps" :key="step.id">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                        <span class="absolute -left-3 -top-3 w-8 h-8 bg-gray-800 text-white rounded-full flex items-center justify-center font-bold" x-text="index + 1"></span>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" :name="'steps['+index+'][title]'" placeholder="Judul Langkah" required class="w-full border-gray-300 rounded border p-2 font-semibold">
                            
                            <textarea :name="'steps['+index+'][content]'" rows="3" placeholder="Jelaskan detail langkah ini..." class="w-full border-gray-300 rounded border p-2 text-sm"></textarea>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Gambar Langkah (Opsional)</label>
                                <input type="file" :name="'steps['+index+'][image]'" accept="image/*" class="w-full text-xs text-gray-500">
                            </div>
                        </div>
                        
                        <button type="button" @click="steps = steps.filter(s => s.id !== step.id)" class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                            <i class="fas fa-times-circle text-lg"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-primary hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:scale-105">
                Simpan Tutorial
            </button>
        </div>

    </form>
</div>

</body>
</html>
