<?php
// public/tutorial.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$pdo = connectDB();
$id = (int)$_GET['id'];

// Fetch Tutorial
$stmt = $pdo->prepare("SELECT t.*, c.name as category_name, u.username as author_name 
                       FROM tutorials t 
                       JOIN categories c ON t.category_id = c.id 
                       LEFT JOIN users u ON t.author_id = u.id 
                       WHERE t.id = ?");
$stmt->execute([$id]);
$tutorial = $stmt->fetch();

if (!$tutorial) {
    die("Tutorial not found.");
}

// Fetch Steps
$stmt = $pdo->prepare("SELECT * FROM steps WHERE tutorial_id = ? ORDER BY step_number ASC");
$stmt->execute([$id]);
$steps = $stmt->fetchAll();

// Fetch Materials
$stmt = $pdo->prepare("SELECT * FROM materials WHERE tutorial_id = ?");
$stmt->execute([$id]);
$materials = $stmt->fetchAll();

$pageTitle = $tutorial['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<div x-data="{ 
    dataSaver: false, 
    completedSteps: JSON.parse(localStorage.getItem('completed_steps_<?php echo $id; ?>') || '[]'),
    toggleStep(stepId) {
        if (this.completedSteps.includes(stepId)) {
            this.completedSteps = this.completedSteps.filter(id => id !== stepId);
        } else {
            this.completedSteps.push(stepId);
        }
        localStorage.setItem('completed_steps_<?php echo $id; ?>', JSON.stringify(this.completedSteps));
    },
    isCompleted(stepId) {
        return this.completedSteps.includes(stepId);
    }
}" class="pb-24">

    <!-- Hero Image -->
    <div class="relative h-64 w-full bg-gray-200">
        <template x-if="!dataSaver">
            <img src="<?php echo BASE_URL . ($tutorial['image_path'] ?? '/assets/images/default.jpg'); ?>" 
                 alt="<?php echo $tutorial['title']; ?>" 
                 class="w-full h-full object-cover">
        </template>
        <template x-if="dataSaver">
             <div class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-500 flex-col">
                <i class="fas fa-image-slash text-4xl mb-2"></i>
                <span>Mode Hemat Data Aktif</span>
            </div>
        </template>
        
        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur rounded-full px-3 py-1 shadow-sm text-xs font-semibold">
             <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="dataSaver" class="form-checkbox text-primary rounded focus:ring-primary">
                <span>Hemat Data</span>
            </label>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 pt-20">
            <span class="bg-primary text-white text-xs px-2 py-1 rounded mb-2 inline-block">
                <?php echo $tutorial['category_name']; ?>
            </span>
            <h1 class="text-2xl font-bold text-white leading-tight mb-2">
                <?php echo $tutorial['title']; ?>
            </h1>
            <div class="flex text-white/80 text-sm gap-4">
                <span><i class="far fa-user mr-1"></i> <?php echo $tutorial['author_name'] ?? 'Admin'; ?></span>
                <span><i class="far fa-clock mr-1"></i> <?php echo $tutorial['duration']; ?> min</span>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="px-4 py-6 max-w-md mx-auto">
        
        <!-- Video Embed -->
        <?php if(!empty($tutorial['video_url'])): ?>
        <div class="mb-6 rounded-xl overflow-hidden shadow-sm">
            <template x-if="!dataSaver">
                <div class="aspect-w-16 aspect-h-9">
                    <?php 
                        // Extract Youtube ID
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $tutorial['video_url'], $matches)) {
                            $videoId = $matches[1];
                        }
                    ?>
                    <?php if($videoId): ?>
                        <iframe src="https://www.youtube.com/embed/<?php echo $videoId; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-64"></iframe>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($tutorial['video_url']); ?>" target="_blank" class="block bg-gray-100 p-8 text-center text-primary hover:bg-gray-200">
                            <i class="fab fa-youtube text-4xl mb-2"></i><br>
                            Tonton Video Tutorial
                        </a>
                    <?php endif; ?>
                </div>
            </template>
            <template x-if="dataSaver">
                <div class="bg-gray-100 p-4 text-center rounded-xl">
                    <i class="fas fa-video-slash text-gray-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">Video disembunyikan (Hemat Data)</p>
                    <a href="<?php echo htmlspecialchars($tutorial['video_url']); ?>" target="_blank" class="text-primary text-sm font-semibold mt-2 inline-block">Buka di YouTube</a>
                </div>
            </template>
        </div>
        <?php endif; ?>

        <!-- Description -->
        <div class="mb-8 text-gray-700 leading-relaxed">
            <p><?php echo nl2br($tutorial['description']); ?></p>
        </div>

        <!-- PDF Download -->
        <?php if(!empty($tutorial['pdf_path'])): ?>
        <a href="<?php echo BASE_URL . $tutorial['pdf_path']; ?>" target="_blank" class="flex items-center gap-4 bg-red-50 border border-red-100 p-4 rounded-xl mb-8 hover:bg-red-100 transition group">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-500 group-hover:bg-white group-hover:scale-110 transition">
                <i class="fas fa-file-pdf text-2xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">Download Panduan PDF</h4>
                <p class="text-xs text-gray-500">Simpan untuk dibaca nanti</p>
            </div>
            <i class="fas fa-download ml-auto text-gray-400 group-hover:text-red-500"></i>
        </a>
        <?php endif; ?>

        <!-- Materials & Tools -->
        <div class="bg-blue-50 rounded-xl p-5 mb-8 border border-blue-100">
            <h3 class="font-bold text-blue-800 mb-4 flex items-center">
                <i class="fas fa-tools mr-2"></i> Alat & Bahan
            </h3>
            <ul class="space-y-3">
                <?php foreach($materials as $mat): ?>
                <li class="flex items-start gap-3 text-sm text-gray-700 border-b border-blue-100 pb-2 last:border-0 last:pb-0">
                    <i class="fas fa-check-circle text-blue-400 mt-1"></i>
                    <div>
                        <span class="font-semibold block"><?php echo $mat['name']; ?> (<?php echo $mat['quantity']; ?>)</span>
                        <?php if($mat['local_source']): ?>
                            <span class="text-xs text-blue-600 block mt-0.5">
                                <i class="fas fa-map-marker-alt mr-1"></i> Tersedia di: <?php echo $mat['local_source']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Steps -->
        <div class="space-y-8">
            <h3 class="font-bold text-xl text-gray-800 mb-6">Langkah-langkah</h3>
            
            <?php foreach($steps as $index => $step): ?>
            <div class="relative pl-8 pb-8 border-l-2 border-gray-200 last:border-0">
                <!-- Step Number Bubble -->
                <div class="absolute -left-4 top-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white transition-colors duration-300"
                     :class="isCompleted(<?php echo $step['id']; ?>) ? 'bg-primary' : 'bg-gray-400'">
                    <?php echo $step['step_number']; ?>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 transition-all duration-300"
                     :class="isCompleted(<?php echo $step['id']; ?>) ? 'opacity-60 grayscale' : ''">
                    
                    <h4 class="font-bold text-lg mb-2 text-gray-800"><?php echo $step['title']; ?></h4>
                    
                    <?php if($step['image_path']): ?>
                    <div class="mb-3 rounded-lg overflow-hidden h-48 bg-gray-100">
                         <template x-if="!dataSaver">
                            <img src="<?php echo BASE_URL . $step['image_path']; ?>" class="w-full h-full object-cover">
                        </template>
                        <template x-if="dataSaver">
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image-slash"></i>
                            </div>
                        </template>
                    </div>
                    <?php endif; ?>

                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                        <?php echo nl2br($step['content']); ?>
                    </p>

                    <button @click="toggleStep(<?php echo $step['id']; ?>)" 
                            class="w-full py-2 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2"
                            :class="isCompleted(<?php echo $step['id']; ?>) 
                                ? 'bg-green-100 text-green-700 hover:bg-green-200' 
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        <i class="fas" :class="isCompleted(<?php echo $step['id']; ?>) ? 'fa-check' : 'fa-circle'"></i>
                        <span x-text="isCompleted(<?php echo $step['id']; ?>) ? 'Selesai' : 'Tandai Selesai'"></span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sticky Share Button -->
        <div class="fixed bottom-20 right-4 z-40">
            <a href="https://wa.me/?text=<?php echo urlencode('Cek tutorial ini: ' . $tutorial['title'] . ' - ' . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" 
               target="_blank"
               class="bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition transform hover:scale-110">
                <i class="fab fa-whatsapp text-3xl"></i>
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
