<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = htmlspecialchars($_POST['address']);

    // Validation
    if (empty($email) || empty($password) || empty($confirm_password) || empty($address)) {
        $error = 'Semua field harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Insert new user
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (email, password, address) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hashed_password, $address]);
                $success = 'Registrasi berhasil! Silahkan login';
                header("refresh:2;url=login.php");
            } catch(PDOException $e) {
                $error = 'Terjadi kesalahan. Silahkan coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sports Shop</title>
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
                <a href="index.php" class="font-russo text-2xl">SPORTS SHOP</a>
                <div class="flex items-center space-x-4">
                    <button id="catalogBtn" class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition">
                        Katalog
                    </button>
                    <a href="login.php" class="hover:text-red-200">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Catalog Modal -->
    <div id="catalogModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg max-w-md mx-auto mt-20 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-russo text-red-600">Katalog Olahraga</h2>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <a href="sepakbola.php" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg">
                    <i class="fas fa-futbol mr-2"></i> Sepakbola
                </a>
                <a href="futsal.php" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg">
                    <i class="fas fa-futbol mr-2"></i> Futsal
                </a>
                <a href="running.php" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg">
                    <i class="fas fa-running mr-2"></i> Running
                </a>
                <a href="bulutangkis.php" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg">
                    <i class="fas fa-table-tennis mr-2"></i> Bulutangkis
                </a>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="font-russo text-2xl text-red-600 mb-6 text-center">Register</h1>
            
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
                    <label for="email" class="block text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label for="confirm_password" class="block text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label for="address" class="block text-gray-700 mb-2">Alamat</label>
                    <textarea id="address" name="address" required
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500 h-24"
                    ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>

                <button type="submit" 
                        class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                    Register
                </button>
            </form>

            <p class="mt-4 text-center text-gray-600">
                Sudah punya akun? 
                <a href="login.php" class="text-red-600 hover:text-red-700">Login disini</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-red-600 text-white py-8 mt-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-russo text-xl mb-4">Contact Us</h3>
                    <p class="mb-2"><i class="fas fa-phone mr-2"></i> +62 123 456 789</p>
                    <p class="mb-2"><i class="fas fa-envelope mr-2"></i> info@sportsshop.com</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> Jl. Olahraga No. 123, Jakarta</p>
                </div>
                <div>
                    <h3 class="font-russo text-xl mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-red-200"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="hover:text-red-200"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="hover:text-red-200"><i class="fab fa-twitter fa-2x"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Modal functionality
        const catalogBtn = document.getElementById('catalogBtn');
        const catalogModal = document.getElementById('catalogModal');
        const closeModal = document.getElementById('closeModal');

        catalogBtn.addEventListener('click', () => {
            catalogModal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            catalogModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        catalogModal.addEventListener('click', (e) => {
            if (e.target === catalogModal) {
                catalogModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
