<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Tutorial Teknologi Tepat Guna untuk Masyarakat Pondokrejo">
    <meta name="theme-color" content="#10B981">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/images/icon-192.png">

    <!-- Tailwind CSS (Compiled) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <!-- Removed CDN Script -->


    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Hide scrollbar for horizontal scrolling */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="antialiased pb-20"> <!-- pb-20 for bottom nav -->

<!-- Top Navigation -->
<nav class="bg-cornsilk shadow-sm sticky top-0 z-50">
    <div class="max-w-md mx-auto px-4 py-3 flex justify-between items-center">
        <a href="<?php echo BASE_URL; ?>/index.php" class="text-xl font-bold text-primary flex items-center gap-2">
            <i class="fas fa-leaf"></i> TTG Pondokrejo
        </a>
        <div class="flex gap-3">
             <a href="<?php echo BASE_URL; ?>/search.php" class="text-gray-500 hover:text-primary">
                <i class="fas fa-search text-xl"></i>
            </a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>/admin/index.php" class="text-gray-500 hover:text-primary">
                    <i class="fas fa-user-cog text-xl"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
