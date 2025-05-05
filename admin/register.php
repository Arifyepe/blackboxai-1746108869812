<?php
session_start();
require_once '../config.php';

$error = '';
$success = '';

// Redirect if already logged in as admin
if(isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_key = $_POST['admin_key']; // Special key to prevent unauthorized registrations

    // Validate admin key (you can change this to any secure key you want)
    $valid_admin_key = "sports_shop_admin_2024";

    if (empty($username) || empty($password) || empty($confirm_password) || empty($admin_key)) {
        $error = 'Semua field harus diisi';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif ($admin_key !== $valid_admin_key) {
        $error = 'Kunci admin tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username sudah digunakan';
            } else {
                // Create new admin account
                $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute([$username, $hashed_password]);

                $success = 'Registrasi berhasil! Silahkan login.';
                header("refresh:2;url=login.php");
            }
        } catch(PDOException $e) {
            $error = 'Terjadi kesalahan. Silahkan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Sports Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .font-russo { font-family: 'Russo One', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-red-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="../index.php" class="font-russo text-2xl">SPORTS SHOP</a>
                <div class="text-lg font-semibold">Admin Panel</div>
            </div>
        </div>
    </nav>

    <!-- Register Form -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="font-russo text-2xl text-red-600 mb-6 text-center">Admin Register</h1>
            
            <?php if($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500"
                           minlength="6">
                    <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500"
                           minlength="6">
                </div>

                <div>
                    <label for="admin_key" class="block text-gray-700 mb-2">Kunci Admin</label>
                    <input type="password" id="admin_key" name="admin_key" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500">
                    <p class="text-sm text-gray-500 mt-1">Diperlukan untuk registrasi admin</p>
                </div>

                <button type="submit" 
                        class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                    Register
                </button>
            </form>

            <div class="mt-4 text-center space-y-2">
                <p class="text-gray-600">
                    Sudah punya akun? 
                    <a href="login.php" class="text-red-600 hover:text-red-700">Login</a>
                </p>
                <p>
                    <a href="../index.php" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Halaman Utama
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-red-600 text-white py-8 mt-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> Sports Shop Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
