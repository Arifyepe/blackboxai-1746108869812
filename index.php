<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Shop - Your Ultimate Sports Gear Destination</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .font-russo { font-family: 'Russo One', sans-serif; }
        .sports-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-red-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="font-russo text-2xl">SPORTS SHOP</div>
                <div class="flex items-center space-x-4">
                    <!-- Catalog Button -->
                    <button id="catalogBtn" class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition">
                        Katalog
                    </button>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="history.php" class="hover:text-red-200">Riwayat Pembelian</a>
                        <a href="logout.php" class="hover:text-red-200">Logout</a>
                    <?php else: ?>
                        <a href="register.php" class="hover:text-red-200">Register</a>
                        <a href="login.php" class="hover:text-red-200">Login</a>
                    <?php endif; ?>
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

    <!-- Hero Banner -->
    <div class="relative h-[500px] overflow-hidden">
        <img src="https://images.pexels.com/photos/3621104/pexels-photo-3621104.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" 
             class="w-full h-full object-cover" alt="Sports Banner">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="text-center text-white">
                <h1 class="font-russo text-5xl mb-4">Selamat Datang</h1>
                <p class="text-2xl">Silahkan Belanja</p>
            </div>
        </div>
    </div>

    <!-- Best Sellers -->
    <div class="container mx-auto px-4 py-12">
        <h2 class="font-russo text-3xl text-red-600 mb-8 text-center">Best Sellers</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Nike Mercurial -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.pexels.com/photos/2385477/pexels-photo-2385477.jpeg" 
                     class="w-full h-48 object-cover" alt="Nike Mercurial">
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">Nike Mercurial</h3>
                    <p class="text-gray-600 mb-2">Sepatu Sepakbola</p>
                    <p class="text-red-600 font-bold">Rp 2,000,000</p>
                </div>
            </div>

            <!-- Indonesia Home Jersey -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.pexels.com/photos/6786901/pexels-photo-6786901.jpeg" 
                     class="w-full h-48 object-cover" alt="Indonesia Home Jersey">
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">Indonesia Home Jersey</h3>
                    <p class="text-gray-600 mb-2">Jersey Sepakbola</p>
                    <p class="text-red-600 font-bold">Rp 1,000,000</p>
                </div>
            </div>

            <!-- Nike Lunar Gato -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.pexels.com/photos/5730956/pexels-photo-5730956.jpeg" 
                     class="w-full h-48 object-cover" alt="Nike Lunar Gato">
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">Nike Lunar Gato</h3>
                    <p class="text-gray-600 mb-2">Sepatu Futsal</p>
                    <p class="text-red-600 font-bold">Rp 1,000,000</p>
                </div>
            </div>

            <!-- Indonesia Badminton Jersey -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="https://images.pexels.com/photos/6203791/pexels-photo-6203791.jpeg" 
                     class="w-full h-48 object-cover" alt="Indonesia Badminton Jersey">
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">Indonesia Badminton Jersey</h3>
                    <p class="text-gray-600 mb-2">Jersey Bulutangkis</p>
                    <p class="text-red-600 font-bold">Rp 500,000</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-red-600 text-white py-8">
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
