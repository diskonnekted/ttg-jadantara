<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <!-- Use Local CSS with cache busting -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-cornsilk text-black-forest font-sans antialiased">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Sidebar (Mobile Overlay) -->
    <div class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 bg-white shadow-xl w-64 transform transition-transform duration-300 z-50 lg:translate-x-0 lg:static lg:shadow-none border-r border-olive-leaf/10 flex flex-col h-full"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <div class="p-6 flex items-center justify-between border-b border-olive-leaf/10 flex-shrink-0">
            <a href="<?php echo BASE_URL; ?>/index.php" class="text-xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-leaf"></i> Admin Panel
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-olive-leaf/10 text-primary' : 'text-black-forest/70 hover:bg-olive-leaf/5 hover:text-black-forest'; ?> rounded-xl font-medium transition">
                <i class="fas fa-th-large w-5"></i> Dashboard
            </a>
            <a href="create.php" class="flex items-center gap-3 px-4 py-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'create.php') ? 'bg-olive-leaf/10 text-primary' : 'text-black-forest/70 hover:bg-olive-leaf/5 hover:text-black-forest'; ?> rounded-xl font-medium transition">
                <i class="fas fa-plus-circle w-5"></i> Buat Tutorial
            </a>
            <a href="categories.php" class="flex items-center gap-3 px-4 py-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'bg-olive-leaf/10 text-primary' : 'text-black-forest/70 hover:bg-olive-leaf/5 hover:text-black-forest'; ?> rounded-xl font-medium transition">
                <i class="fas fa-tags w-5"></i> Kategori
            </a>
            <div class="pt-4 mt-4 border-t border-olive-leaf/10">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase mb-2">Sistem</p>
                <a href="<?php echo BASE_URL; ?>/index.php" class="flex items-center gap-3 px-4 py-3 text-black-forest/70 hover:bg-olive-leaf/5 hover:text-black-forest rounded-xl font-medium transition">
                    <i class="fas fa-globe w-5"></i> Lihat Website
                </a>
                <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-copperwood hover:bg-red-50 rounded-xl font-medium transition">
                    <i class="fas fa-sign-out-alt w-5"></i> Logout
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-olive-leaf/10 flex-shrink-0">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-black-forest"><?php echo $_SESSION['username'] ?? 'Admin'; ?></p>
                    <p class="text-xs text-black-forest/60">Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Topbar (Mobile) -->
        <header class="bg-white shadow-sm p-4 flex items-center justify-between lg:hidden sticky top-0 z-30 flex-shrink-0">
            <button @click="sidebarOpen = true" class="text-black-forest focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <span class="font-bold text-black-forest">Dashboard</span>
            <div class="w-6"></div> <!-- Spacer -->
        </header>

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 w-full">
