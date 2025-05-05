<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's orders
$stmt = $conn->prepare("
    SELECT o.*, od.quantity, od.price, od.size, p.name as product_name, p.category
    FROM orders o
    JOIN order_details od ON o.id = od.order_id
    JOIN products p ON od.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian - Sports Shop</title>
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
                    <a href="history.php" class="hover:text-red-200">Riwayat Pembelian</a>
                    <a href="logout.php" class="hover:text-red-200">Logout</a>
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

    <!-- Purchase History -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="font-russo text-3xl text-red-600 mb-8 text-center">Riwayat Pembelian</h1>

        <?php if (empty($orders)): ?>
            <div class="text-center text-gray-600">
                <p>Belum ada riwayat pembelian.</p>
                <a href="index.php" class="text-red-600 hover:text-red-700">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-semibold text-lg">Order #<?php echo $order['id']; ?></h3>
                                <p class="text-gray-600">
                                    <?php echo date('d F Y H:i', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            <!-- Order Status Timeline -->
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div class="px-3 py-1 rounded-full text-sm <?php 
                                        echo $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($order['status'] === 'confirmed' || $order['status'] === 'shipped' || $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600');
                                    ?>">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <div class="px-3 py-1 rounded-full text-sm <?php 
                                        echo $order['status'] === 'confirmed' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($order['status'] === 'shipped' || $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600');
                                    ?>">
                                        <i class="fas fa-box mr-1"></i> Dikemas
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <div class="px-3 py-1 rounded-full text-sm <?php 
                                        echo $order['status'] === 'shipped' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600');
                                    ?>">
                                        <i class="fas fa-truck mr-1"></i> Dikirim
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <div class="px-3 py-1 rounded-full text-sm <?php 
                                        echo $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
                                    ?>">
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-b py-4 my-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold"><?php echo $order['product_name']; ?></h4>
                                    <p class="text-gray-600">Kategori: <?php echo ucfirst($order['category']); ?></p>
                                    <p class="text-gray-600">Jumlah: <?php echo $order['quantity']; ?></p>
                                    <p class="text-gray-600">Ukuran: <?php echo $order['size']; ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold"><?php echo formatRupiah($order['price']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Alamat Pengiriman:</h4>
                            <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold mb-2">Metode Pembayaran:</h4>
                                <p class="text-gray-600"><?php echo $order['payment_method']; ?></p>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Metode Pengiriman:</h4>
                                <p class="text-gray-600">
                                    <?php echo $order['shipping_method'] === 'sicepat' ? 'SiCepat Express (1-2 hari)' : 'JNE Reguler (1-3 hari)'; ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Rincian Pembayaran:</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Subtotal Produk:</span>
                                    <span><?php echo formatRupiah($order['price'] * $order['quantity']); ?></span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Biaya Pengiriman:</span>
                                    <span><?php echo formatRupiah($order['shipping_fee']); ?></span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Biaya Layanan:</span>
                                    <span><?php echo formatRupiah($order['service_fee']); ?></span>
                                </div>
                                <div class="flex justify-between font-semibold border-t pt-2 mt-2">
                                    <span>Total:</span>
                                    <span><?php echo formatRupiah($order['total_amount']); ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if ($order['status'] === 'pending'): ?>
                            <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                                <p class="text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Silahkan lakukan pembayaran sesuai dengan metode yang dipilih.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
