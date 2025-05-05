<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$product_id = $_GET['id'];

// Fetch user's address
$stmt = $conn->prepare("SELECT address FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$default_address = $user['address'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = htmlspecialchars($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    $quantity = (int)$_POST['quantity'];
    $size = $_POST['size'];

    if (empty($shipping_address) || empty($payment_method) || $quantity < 1 || empty($size)) {
        $error = 'Semua field harus diisi dengan benar';
    } else {
        // Store order data in session
        $_SESSION['order_data'] = [
            'product_id' => $product_id,
            'shipping_address' => $shipping_address,
            'payment_method' => $payment_method,
            'quantity' => $quantity,
            'size' => $size,
            'product_name' => $product['name'],
            'product_price' => $product['price']
        ];
        
        header("Location: pembelian.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Produk - Sports Shop</title>
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
                    <a href="history.php" class="hover:text-red-200">Riwayat Pembelian</a>
                    <a href="logout.php" class="hover:text-red-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Purchase Form -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="font-russo text-2xl text-red-600 mb-6 text-center">Form Pembelian</h1>
            
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
                    <label for="shipping_address" class="block text-gray-700 mb-2">Alamat Pengiriman</label>
                    <textarea id="shipping_address" name="shipping_address" required
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500 h-24"
                    ><?php echo $default_address; ?></textarea>
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 mb-2">Jumlah</label>
                    <input type="number" id="quantity" name="quantity" required min="1"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500"
                           value="1">
                </div>

                <div>
                    <label for="size" class="block text-gray-700 mb-2">Ukuran Baju</label>
                    <select id="size" name="size" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500">
                        <option value="">Pilih ukuran baju</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>

                <div>
                    <label for="payment_method" class="block text-gray-700 mb-2">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500">
                        <option value="">Pilih metode pembayaran</option>
                        <option value="BCA">Transfer Bank BCA</option>
                        <option value="BRI">Transfer Bank BRI</option>
                        <option value="DANA">DANA</option>
                    </select>
                </div>

                <button type="submit" 
                        class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                    Beli Sekarang
                </button>
            </form>
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
</body>
</html>
