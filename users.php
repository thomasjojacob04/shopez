<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit(); // Important to prevent further code execution
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ecommerce";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Handle sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column to prevent SQL injection
$allowed_sort_columns = ['id', 'username', 'email', 'created_at'];
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = 'id';
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query
$query = "SELECT id, username, email, created_at FROM users WHERE 
          username LIKE '%$search%' OR 
          email LIKE '%$search%' 
          ORDER BY $sort $order 
          LIMIT $per_page OFFSET $offset";

$result = mysqli_query($conn, $query);

// Get total records for pagination
$total_query = "SELECT COUNT(*) as count FROM users WHERE 
                username LIKE '%$search%' OR 
                email LIKE '%$search%'";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_records / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
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
                <a href="admin.php" class="flex items-center px-6 py-3 text-white hover:bg-gray-700">
                    <span class="mx-3">Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-white bg-gray-700">
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
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Users Management</h2>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <form class="flex gap-4">
                        <input 
                            type="text" 
                            name="search" 
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search users..." 
                            class="flex-1 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-6 py-3 border-b text-left">
                                    <a href="?sort=id&order=<?php echo $sort === 'id' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="font-semibold hover:text-blue-600">
                                        ID
                                    </a>
                                </th>
                                <th class="px-6 py-3 border-b text-left">
                                    <a href="?sort=username&order=<?php echo $sort === 'username' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>"
                                       class="font-semibold hover:text-blue-600">
                                        Username
                                    </a>
                                </th>
                                <th class="px-6 py-3 border-b text-left">
                                    <a href="?sort=email&order=<?php echo $sort === 'email' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>"
                                       class="font-semibold hover:text-blue-600">
                                        Email
                                    </a>
                                </th>
                                <th class="px-6 py-3 border-b text-left">
                                    <a href="?sort=created_at&order=<?php echo $sort === 'created_at' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>"
                                       class="font-semibold hover:text-blue-600">
                                        Created At
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($row['id']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($row['username']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="px-6 py-4 border-b"><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-center">
                    <div class="flex space-x-2">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>"
                           class="px-4 py-2 border rounded <?php echo $page === $i ? 'bg-blue-500 text-white' : 'hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>