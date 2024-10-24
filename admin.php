<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Assuming we have a database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ecommerce";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get count of users and products
$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$products_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];

// Admin details from session (these would be set during login)
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';
$admin_last_login = $_SESSION['last_login'] ?? date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 w-64 h-full bg-gray-800">
            <div class="flex items-center justify-center h-20 bg-gray-900">
                <h1 class="text-white text-2xl font-bold">Admin Panel</h1>
            </div>
            <nav class="mt-6">
                <a href="#" class="flex items-center px-6 py-3 text-white bg-gray-700">
                    <span class="mx-3">Dashboard</span>
                </a>
                <a href="users.php" class="flex items-center px-6 py-3 text-white hover:bg-gray-700">
                    <span class="mx-3">Users</span>
                </a>
                <a href="products.php" class="flex items-center px-6 py-3 text-white hover:bg-gray-700">
                    <span class="mx-3">Products</span>
                </a>
                <a href="adminlogout.php" class="flex items-center px-6 py-3 text-red-400 hover:bg-gray-700 mt-auto">
                    <span class="mx-3">Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 p-8">
            <!-- Admin Profile -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Welcome Back!</h2>
                    <p class="text-gray-500">Last Login: <?php echo htmlspecialchars($admin_last_login); ?></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Name</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($admin_name); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Email</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($admin_email); ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Users Card -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Total Users</h3>
                            <p class="text-gray-500 mt-1">Registered accounts</p>
                        </div>
                        <span class="text-3xl font-bold text-blue-600"><?php echo $users_count; ?></span>
                    </div>
                    <a href="users.php" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Manage Users →
                    </a>
                </div>

                <!-- Products Card -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Total Products</h3>
                            <p class="text-gray-500 mt-1">Active products</p>
                        </div>
                        <span class="text-3xl font-bold text-green-600"><?php echo $products_count; ?></span>
                    </div>
                    <a href="products.php" class="mt-4 inline-block text-green-600 hover:text-green-800">
                        Manage Products →
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>