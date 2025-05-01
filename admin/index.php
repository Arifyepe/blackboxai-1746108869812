<?php
session_start();
require_once '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch statistics
try {
    // Total pending orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $pending_orders = $stmt->fetchColumn();

    // Total confirmed orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed'");
    $confirmed_orders = $stmt->fetchColumn();

    // Total products
    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt->fetchColumn();

    // Recent orders
    $stmt = $conn->prepare("
        SELECT o.*, u.email as user_email, p.name as product_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_details od ON o.id = od.order_id
        JOIN products p ON od.product_id = p.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll();

} catch(PDOException $e) {
    $error = 'Terjadi kesalahan dalam mengambil data.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sports Shop</title>
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
                <div class="font-russo text-2xl">SPORTS SHOP ADMIN</div>
                <div class="flex items-center space-x-4">
                    <a href="confirm_payment.php" class="hover:text-red-200">Konfirmasi Pembayaran</a>
                    <a href="restock.php" class="hover:text-red-200">Restock Barang</a>
                    <a href="history.php" class="hover:text-red-200">Riwayat Pembelian</a>
                    <a href="logout.php" class="hover:text-red-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="font-russo text-3xl text-red-600 mb-8">Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Pesanan Pending</p>
                        <h2 class="text-3xl font-bold text-red-600"><?php echo $pending_orders; ?></h2>
                    </div>
                    <div class="text-yellow-500">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Pesanan Terkonfirmasi</p>
                        <h2 class="text-3xl font-bold text-red-600"><?php echo $confirmed_orders; ?></h2>
                    </div>
                    <div class="text-green-500">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Produk</p>
                        <h2 class="text-3xl font-bold text-red-600"><?php echo $total_products; ?></h2>
                    </div>
                    <div class="text-blue-500">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="font-russo text-2xl mb-6">Pesanan Terbaru</h2>
            
            <?php if (empty($recent_orders)): ?>
                <p class="text-gray-600">Belum ada pesanan.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['user_email']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['product_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                            echo match($order['status']) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'shipped' => 'bg-blue-100 text-blue-800',
                                                'delivered' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>" 
                                           class="text-red-600 hover:text-red-900">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
