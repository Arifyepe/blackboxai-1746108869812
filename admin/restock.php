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

// Handle restock submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restock'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity <= 0) {
        $error = 'Jumlah restock harus lebih dari 0';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            $stmt->execute([$quantity, $product_id]);
            $success = "Stok berhasil ditambahkan";
        } catch(PDOException $e) {
            $error = 'Terjadi kesalahan dalam menambah stok.';
        }
    }
}

// Fetch all products
try {
    $stmt = $conn->prepare("
        SELECT id, category, name, type, brand, size, stock
        FROM products
        ORDER BY category, type, name, brand, size
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();

    // Group products by category
    $grouped_products = [];
    foreach ($products as $product) {
        $grouped_products[$product['category']][] = $product;
    }
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan dalam mengambil data produk.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock Barang - Sports Shop Admin</title>
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
        <h1 class="font-russo text-3xl text-red-600 mb-8">Restock Barang</h1>

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

        <!-- Products by Category -->
        <?php foreach ($grouped_products as $category => $category_products): ?>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="font-russo text-2xl mb-6 capitalize"><?php echo $category; ?></h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($category_products as $product): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">#<?php echo $product['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $product['name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize"><?php echo $product['type']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $product['brand'] ?? '-'; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $product['size'] ?? '-'; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="<?php echo $product['stock'] < 10 ? 'text-red-600 font-bold' : ''; ?>">
                                            <?php echo $product['stock']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" action="" class="flex items-center space-x-2">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="number" name="quantity" min="1" value="1"
                                                   class="w-20 px-2 py-1 border rounded focus:outline-none focus:border-red-500">
                                            <button type="submit" name="restock"
                                                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                                Restock
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
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
