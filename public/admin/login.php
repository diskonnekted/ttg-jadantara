<?php
// public/admin/login.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

if (isLoggedIn()) {
    redirect('/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        redirect('/admin/index.php');
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center p-4">

<div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-sm">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Login Admin</h1>
        <p class="text-sm text-gray-500">Masuk untuk mengelola konten</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-green-500" id="username" name="username" type="text" placeholder="Username" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline focus:border-green-500" id="password" name="password" type="password" placeholder="******************" required>
        </div>
        <div class="flex items-center justify-between">
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition" type="submit">
                Masuk
            </button>
        </div>
    </form>
    <div class="mt-4 text-center">
        <a href="<?php echo BASE_URL; ?>/index.php" class="text-sm text-gray-500 hover:text-green-600">Kembali ke Beranda</a>
    </div>
</div>

</body>
</html>
