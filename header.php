<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header>
        <nav class="bg-white dark:bg-gray-950 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <!-- Logo on the left -->
                <a href="main.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <span class="self-center text-3xl font-bold whitespace-nowrap dark:text-white">ShopEZ</span>
                </a>

                <!-- Navigation links in center -->
                <div class="flex-1 flex justify-center items-center hidden md:flex" id="navbar-sticky">
                    <ul class="flex flex-row space-x-8">
                        <li>
                            <a href="main.php#" class="block py-2 px-3 text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500" aria-current="page">Home</a>
                        </li>
                        <li>
                            <a href="main.php#product-catalog" class="block py-2 px-3 text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500">Products</a>
                        </li>
                        <li>
                            <a href="main.php#contact-us" class="block py-2 px-3 text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500">Contact</a>
                        </li>
                    </ul>
                </div>

                <!-- Cart, Login/Logout buttons and mobile menu on the right -->
                <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Menu -->
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-900 dark:text-white"><?php echo $_SESSION['username']; ?></span>
                            <!-- Cart Icon -->
                            <a href="cart.php" class="text-gray-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-500">
                                <div class="relative">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <?php
                                    // Get cart count
                                    $user_id = $_SESSION['user_id'];
                                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
                                    $stmt->bind_param("i", $user_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $count = $result->fetch_assoc()['count'];
                                    if ($count > 0):
                                    ?>
                                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                            <?php echo $count; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <a href="logout.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Logout</a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-4">
                            <a href="userlogin.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login</a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile menu button -->
                    <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 ml-4" aria-controls="navbar-sticky" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu (hidden by default) -->
                <div class="hidden md:hidden w-full" id="mobile-menu">
                    <ul class="flex flex-col p-4 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <li>
                            <a href="#" class="block py-2 px-3 text-white bg-blue-700 rounded" aria-current="page">Home</a>
                        </li>
                        <li>
                            <a href="#product-catalog" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Products</a>
                        </li>
                        <li>
                            <a href="#contact-us" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Contact</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li>
                                <a href="cart.php" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Cart</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Add JavaScript for mobile menu toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('[data-collapse-toggle="navbar-sticky"]');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>
</body>

</html>