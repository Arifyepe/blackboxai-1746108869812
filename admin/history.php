<?php
session_start();
require_once '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';

// Handle Excel export
if (isset($_GET['export'])) {
    try {
        // Fetch all orders for export
        $stmt = $conn->prepare("
            SELECT o.id, o.created_at, u.email, p.name as product_name, 
                   od.quantity, od.price, o.shipping_address, o.payment_method, o.status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN order_details od ON o.id = od.order_id
            JOIN products p ON od.product_id = p.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll();

        // Set headers for Excel download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d') . '.csv"');

        // Create CSV file
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for proper Excel encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Add headers
        fputcsv($output, [
            'Order ID', 'Tanggal', 'Customer', 'Produk', 
            'Jumlah', 'Total', 'Alamat', 'Pembayaran', 'Status'
        ]);

        // Add data
        foreach ($orders as $order) {
            fputcsv($output, [
                '#' . $order['id'],
                $order['created_at'],
                $order['email'],
                $order['product_name'],
                $order['quantity'],
                $order['price'],
                $order['shipping_address'],
                $order['payment_method'],
                $order['status']
            ]);
        }

        fclose($output);
        exit();
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan dalam mengekspor data.';
    }
}

// Fetch orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Get total orders count
    $stmt = $conn->query("SELECT COUNT(*) FROM orders");
    $total_orders = $stmt->fetchColumn();
    $total_pages = ceil($total_orders / $limit);

    // Fetch orders for current page
    $stmt = $conn->prepare("
        SELECT o.*, u.email as user_email, p.name as product_name, 
               od.quantity, od.price
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_details od ON o.id = od.order_id
        JOIN products p ON od.product_id = p.id
        ORDER BY o.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan dalam mengambil data.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian - Sports Shop Admin</title>
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
        <div class="flex justify-between items-center mb-8">
            <h1 class="font-russo text-3xl text-red-600">Riwayat Pembelian</h1>
            <a href="?export=1" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                <i class="fas fa-file-excel mr-2"></i>Export ke Excel
            </a>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <?php if (empty($orders)): ?>
                <p class="text-gray-600">Tidak ada riwayat pembelian.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['user_email']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['product_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['quantity']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo formatRupiah($order['price']); ?></td>
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

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="flex justify-center mt-6">
                        <div class="flex space-x-2">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="px-4 py-2 rounded <?php echo $i === $page ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
