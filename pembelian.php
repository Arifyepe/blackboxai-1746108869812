<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order data exists in session
if (!isset($_SESSION['order_data'])) {
    header("Location: index.php");
    exit();
}

$order_data = $_SESSION['order_data'];
$error = '';
$success = '';

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_method = $_POST['shipping_method'];
    
    if (empty($shipping_method)) {
        $error = 'Pilih metode pengiriman';
    } else {
        try {
            $conn->beginTransaction();

            // Calculate fees
            $shipping_fee = ($shipping_method === 'sicepat') ? 9000 : 7000;
            $service_fee = 5000;
            $subtotal = $order_data['quantity'] * $order_data['product_price'];
            $total_amount = $subtotal + $shipping_fee + $service_fee;

            // Create order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, shipping_address, payment_method, shipping_method, shipping_fee, service_fee, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([
                $_SESSION['user_id'],
                $order_data['shipping_address'],
                $order_data['payment_method'],
                $shipping_method,
                $shipping_fee,
                $service_fee,
                $total_amount
            ]);
            $order_id = $conn->lastInsertId();

            // Add order details
            $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, size, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $order_id,
                $order_data['product_id'],
                $order_data['quantity'],
                $order_data['size'],
                $order_data['product_price']
            ]);

            $conn->commit();
            unset($_SESSION['order_data']); // Clear order data from session
            $success = 'Pesanan berhasil dibuat! Silahkan melakukan pembayaran.';
            header("refresh:2;url=history.php");
        } catch(PDOException $e) {
            $conn->rollBack();
            $error = 'Terjadi kesalahan. Silahkan coba lagi.';
        }
    }
}

// Calculate initial totals
$subtotal = $order_data['quantity'] * $order_data['product_price'];
$service_fee = 5000;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembelian - Sports Shop</title>
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

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
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

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="font-russo text-2xl text-red-600 mb-6">Detail Pemesanan</h2>
                
                <!-- Customer Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-lg mb-3">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Nama:</p>
                            <p class="font-medium"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">No. Telepon:</p>
                            <p class="font-medium"><?php echo htmlspecialchars($user['phone_number'] ?? 'Belum diisi'); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600">Alamat Pengiriman:</p>
                            <p class="font-medium"><?php echo nl2br(htmlspecialchars($order_data['shipping_address'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-lg mb-3">Informasi Produk</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Nama Produk:</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order_data['product_name']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ukuran:</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order_data['size']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Jumlah:</p>
                            <p class="font-medium"><?php echo htmlspecialchars($order_data['quantity']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Harga Satuan:</p>
                            <p class="font-medium">Rp <?php echo number_format($order_data['product_price'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method Form -->
                <form method="POST" action="" class="space-y-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-3">Metode Pengiriman</h3>
                        <select name="shipping_method" id="shipping_method" required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500"
                                onchange="updateTotal()">
                            <option value="">Pilih metode pengiriman</option>
                            <option value="sicepat">SiCepat Express (1-2 hari) - Rp 9.000</option>
                            <option value="jne">JNE Reguler (1-3 hari) - Rp 7.000</option>
                        </select>
                    </div>

                    <!-- Payment Details -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-3">Rincian Pembayaran</h3>
                        <table class="w-full">
                            <tr class="border-b">
                                <td class="py-2">Subtotal Produk</td>
                                <td class="py-2 text-right">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2">Biaya Pengiriman</td>
                                <td class="py-2 text-right" id="shipping-cost">Rp 0</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2">Biaya Layanan</td>
                                <td class="py-2 text-right">Rp <?php echo number_format($service_fee, 0, ',', '.'); ?></td>
                            </tr>
                            <tr class="font-semibold">
                                <td class="py-2">Total Pembayaran</td>
                                <td class="py-2 text-right" id="total-payment">Rp <?php echo number_format($subtotal + $service_fee, 0, ',', '.'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition">
                        Konfirmasi Pesanan
                    </button>
                </form>
            </div>
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
        function updateTotal() {
            const shippingMethod = document.getElementById('shipping_method').value;
            const shippingCost = shippingMethod === 'sicepat' ? 9000 : (shippingMethod === 'jne' ? 7000 : 0);
            const subtotal = <?php echo $subtotal; ?>;
            const serviceFee = <?php echo $service_fee; ?>;
            const total = subtotal + shippingCost + serviceFee;

            document.getElementById('shipping-cost').textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
            document.getElementById('total-payment').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</body>
</html>
