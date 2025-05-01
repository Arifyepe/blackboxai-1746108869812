<?php
session_start();
require_once '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Handle payment confirmation
if (isset($_POST['confirm_payment']) && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$order_id]);
        $success = "Pembayaran untuk Order #$order_id berhasil dikonfirmasi";
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan dalam mengkonfirmasi pembayaran.';
    }
}

// Fetch pending orders
try {
    $stmt = $conn->prepare("
        SELECT o.*, u.email as user_email, p.name as product_name, 
               od.quantity, od.price
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_details od ON o.id = od.order_id
        JOIN products p ON od.product_id = p.id
        WHERE o.status = 'pending'
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $pending_orders = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan dalam mengambil data.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - Sports Shop Admin</title>
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
                <a href="index.php" class="font-russo text-2xl">SPORTS SHOP ADMIN</a>
                <div class="flex items-center space-x-4">
                    <a href="confirm_payment.php" class="hover:text-red-200">Konfirmasi Pembayaran</a>
                    <a href="restock.php" class="hover:text-red-200">Restock Barang</a>
                    <a href="history.php" class="hover:text-red-200">Riwayat Pembelian</a>
                    <a href="logout.php" class="hover:text-red-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="font-russo text-3xl text-red-600 mb-8">Konfirmasi Pembayaran</h1>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Pending Orders Table -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="font-russo text-2xl mb-6">Pesanan Pending</h2>
            
            <?php if (empty($pending_orders)): ?>
                <p class="text-gray-600">Tidak ada pesanan pending.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pending_orders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['user_email']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['product_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['quantity']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo formatRupiah($order['price']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['payment_method']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" name="confirm_payment"
                                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"
                                                    onclick="return confirm('Konfirmasi pembayaran untuk Order #<?php echo $order['id']; ?>?')">
                                                Konfirmasi
                                            </button>
                                        </form>
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>" 
                                           class="text-red-600 hover:text-red-900 ml-2">
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
