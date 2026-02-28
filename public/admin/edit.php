<?php
// public/admin/edit.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

if (!isset($_GET['id'])) {
    redirect('/admin/index.php');
}

$id = (int)$_GET['id'];
$pdo = connectDB();

// Fetch Tutorial
$stmt = $pdo->prepare("SELECT * FROM tutorials WHERE id = ?");
$stmt->execute([$id]);
$tutorial = $stmt->fetch();

if (!$tutorial) {
    redirect('/admin/index.php');
}

// Fetch Materials
$stmt = $pdo->prepare("SELECT * FROM materials WHERE tutorial_id = ?");
$stmt->execute([$id]);
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Steps
$stmt = $pdo->prepare("SELECT * FROM steps WHERE tutorial_id = ? ORDER BY step_number ASC");
$stmt->execute([$id]);
$steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = getCategories($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $difficulty = sanitize($_POST['difficulty']);
    $duration = (int)$_POST['duration'];
    $video_url = sanitize($_POST['video_url'] ?? '');

    // Update Tutorial
    $sql = "UPDATE tutorials SET title = ?, description = ?, category_id = ?, difficulty = ?, duration = ?, video_url = ? WHERE id = ?";
    $params = [$title, $description, $category_id, $difficulty, $duration, $video_url, $id];
    
    // Handle Image Update
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['image']);
        if ($upload) {
            $sql = str_replace("WHERE id = ?", ", image_path = ? WHERE id = ?", $sql);
            $params = array_merge(array_slice($params, 0, -1), [$upload, $id]);
        }
    }

    // Handle PDF Update
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['pdf_file']);
        if ($upload) {
            $sql = str_replace("WHERE id = ?", ", pdf_path = ? WHERE id = ?", $sql);
            $params = array_merge(array_slice($params, 0, -1), [$upload, $id]);
        }
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Update Materials (Delete all and re-insert is simpler for this prototype)
        $pdo->prepare("DELETE FROM materials WHERE tutorial_id = ?")->execute([$id]);
        if (isset($_POST['materials']) && is_array($_POST['materials'])) {
            $stmtMat = $pdo->prepare("INSERT INTO materials (tutorial_id, name, quantity, local_source) VALUES (?, ?, ?, ?)");
            foreach ($_POST['materials'] as $mat) {
                if (!empty($mat['name'])) {
                    $stmtMat->execute([$id, sanitize($mat['name']), sanitize($mat['quantity']), sanitize($mat['local_source'])]);
                }
            }
        }

        // Update Steps (Complex due to images)
        // Strategy: 
        // 1. Get existing steps to preserve images if not updated.
        // 2. We will just update by ID if it exists, or insert if new. 
        // However, the Alpine form submits as a list.
        // Easier strategy: Delete all steps and re-insert? No, we lose old images if not re-uploaded.
        // Better: Loop through submitted steps. If ID exists, update. If new, insert.
        // But we need to handle deletions too.
        
        // Let's stick to "Delete all and Re-insert" BUT we must carry over the image path if no new image is uploaded.
        // The form needs to submit the 'old_image_path'.
        
        $pdo->prepare("DELETE FROM steps WHERE tutorial_id = ?")->execute([$id]);
        
        if (isset($_POST['steps']) && is_array($_POST['steps'])) {
            $stmtStep = $pdo->prepare("INSERT INTO steps (tutorial_id, step_number, title, content, image_path) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($_POST['steps'] as $index => $step) {
                if (!empty($step['title'])) {
                    $step_image = $step['old_image'] ?? '';
                    
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

                    $stmtStep->execute([$id, $index + 1, sanitize($step['title']), sanitize($step['content']), $step_image]);
                }
            }
        }

        $pdo->commit();
        redirect('/admin/index.php?msg=updated');

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Gagal update: " . $e->getMessage();
    }
}
?>
<?php
$pageTitle = "Edit Tutorial";
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Edit Tutorial</h1>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6 border-l-4 border-red-500 shadow-sm"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-8" x-data="{ materials: <?php echo htmlspecialchars(json_encode($materials)); ?>, steps: <?php echo htmlspecialchars(json_encode($steps)); ?> }">
        
        <!-- Basic Info -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="font-bold text-lg mb-4 border-b pb-2">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tutorial</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($tutorial['title']); ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $tutorial['category_id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kesulitan</label>
                    <select name="difficulty" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                        <option value="Easy" <?php echo $tutorial['difficulty'] == 'Easy' ? 'selected' : ''; ?>>Mudah (Easy)</option>
                        <option value="Medium" <?php echo $tutorial['difficulty'] == 'Medium' ? 'selected' : ''; ?>>Sedang (Medium)</option>
                        <option value="Hard" <?php echo $tutorial['difficulty'] == 'Hard' ? 'selected' : ''; ?>>Sulit (Hard)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                    <input type="number" name="duration" value="<?php echo $tutorial['duration']; ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Video Youtube (URL)</label>
                    <input type="url" name="video_url" value="<?php echo htmlspecialchars($tutorial['video_url'] ?? ''); ?>" placeholder="https://youtube.com/watch?v=..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran PDF</label>
                    <?php if(!empty($tutorial['pdf_path'])): ?>
                        <div class="mb-2 text-sm text-blue-600">
                            <a href="<?php echo BASE_URL . $tutorial['pdf_path']; ?>" target="_blank"><i class="fas fa-file-pdf"></i> Lihat PDF Saat Ini</a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="pdf_file" accept="application/pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama</label>
                    <?php if($tutorial['image_path']): ?>
                        <div class="mb-2">
                            <img src="<?php echo BASE_URL . $tutorial['image_path']; ?>" class="h-20 rounded">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2"><?php echo htmlspecialchars($tutorial['description']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Materials (Dynamic) -->
        <div x-data="{ materials: <?php echo htmlspecialchars(json_encode($materials), ENT_QUOTES, 'UTF-8'); ?> }" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="font-bold text-lg">Alat & Bahan</h2>
                <button type="button" @click="materials.push({id: Date.now()})" class="text-sm text-green-600 hover:text-green-800 font-semibold">+ Tambah Item</button>
            </div>
            
            <div class="space-y-3">
                <template x-for="(mat, index) in materials" :key="index">
                    <div class="flex gap-2 items-start bg-gray-50 p-3 rounded">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-2">
                            <input type="text" :name="'materials['+index+'][name]'" x-model="mat.name" placeholder="Nama Alat/Bahan" required class="w-full border-gray-300 rounded border p-2 text-sm">
                            <input type="text" :name="'materials['+index+'][quantity]'" x-model="mat.quantity" placeholder="Jumlah" class="w-full border-gray-300 rounded border p-2 text-sm">
                            <input type="text" :name="'materials['+index+'][local_source]'" x-model="mat.local_source" placeholder="Beli di mana?" class="w-full border-gray-300 rounded border p-2 text-sm">
                        </div>
                        <button type="button" @click="materials = materials.filter((m, i) => i !== index)" class="text-red-500 hover:text-red-700 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Steps (Dynamic) -->
        <div x-data="{ steps: <?php echo htmlspecialchars(json_encode($steps), ENT_QUOTES, 'UTF-8'); ?> }" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="font-bold text-lg">Langkah-langkah</h2>
                <button type="button" @click="steps.push({id: Date.now()})" class="text-sm text-green-600 hover:text-green-800 font-semibold">+ Tambah Langkah</button>
            </div>
            
            <div class="space-y-6">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                        <span class="absolute -left-3 -top-3 w-8 h-8 bg-gray-800 text-white rounded-full flex items-center justify-center font-bold" x-text="index + 1"></span>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" :name="'steps['+index+'][title]'" x-model="step.title" placeholder="Judul Langkah" required class="w-full border-gray-300 rounded border p-2 font-semibold">
                            
                            <textarea :name="'steps['+index+'][content]'" x-model="step.content" rows="3" placeholder="Jelaskan detail..." class="w-full border-gray-300 rounded border p-2 text-sm"></textarea>
                            
                            <!-- Hidden input for old image path -->
                            <input type="hidden" :name="'steps['+index+'][old_image]'" x-model="step.image_path">
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Gambar Langkah (Opsional)</label>
                                <template x-if="step.image_path">
                                    <div class="mb-2">
                                        <img :src="'<?php echo BASE_URL; ?>' + step.image_path" class="h-20 rounded">
                                    </div>
                                </template>
                                <input type="file" :name="'steps['+index+'][image]'" accept="image/*" class="w-full text-xs text-gray-500">
                            </div>
                        </div>
                        
                        <button type="button" @click="steps = steps.filter((s, i) => i !== index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                            <i class="fas fa-times-circle text-lg"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-primary hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:scale-105">
                Update Tutorial
            </button>
        </div>

    </form>
</div>

</body>
</html>
