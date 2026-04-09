<?php
// Disable all caching for this request
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Tutorial Teknologi Tepat Guna untuk Masyarakat Pondokrejo">
    <meta name="theme-color" content="#10b981">
    
    <!-- Disable Aggressive Caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/images/logo.png">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/logo.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/logo.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                        'primary-dark': '#059669',
                        'primary-light': '#d1fae5',
                        surface: '#f3f4f6',
                        card: '#ffffff',
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-surface font-sans antialiased pb-16">

<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-lg mx-auto px-4 py-3 flex justify-between items-center">
        <a href="<?php echo BASE_URL; ?>/index.php" class="flex items-center gap-2.5">
            <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" class="w-8 h-8" alt="Logo">
            <span class="text-base font-bold text-primary">TTG Pondokrejo</span>
        </a>
        <div class="flex gap-3">
             <a href="<?php echo BASE_URL; ?>/search.php" class="text-gray-400 hover:text-primary transition"><i class="fas fa-search text-lg"></i></a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>/admin/index.php" class="text-gray-400 hover:text-primary transition"><i class="fas fa-cog text-lg"></i></a>
            <?php endif; ?>
        </div>
    </div>
</nav>
