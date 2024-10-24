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

// Validate sort column
$allowed_sort_columns = ['id', 'name', 'price', 'created_at'];
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = 'id';
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 8;
$offset = ($page - 1) * $per_page;

// Update the query to include description
$query = "SELECT * FROM products WHERE 
          name LIKE '%$search%' OR
          description LIKE '%$search%'
          ORDER BY $sort $order 
          LIMIT $per_page OFFSET $offset";

$result = mysqli_query($conn, $query);

// Update total records query
$total_query = "SELECT COUNT(*) as count FROM products WHERE 
                name LIKE '%$search%' OR 
                description LIKE '%$search%'";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_records / $per_page);

// Helper function to safely delete a product
function deleteProduct($conn, $product_id)
{
    try {
        // Start transaction
        mysqli_begin_transaction($conn);

        // First delete related cart entries
        $delete_cart = "DELETE FROM cart WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $delete_cart);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);

        // Get image filename before deleting product
        $image_query = "SELECT image FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $image_query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);

        // Delete the product
        $delete_product = "DELETE FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_product);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);

        // If successful, commit transaction
        mysqli_commit($conn);

        // Delete the image file if it exists
        if ($product && $product['image']) {
            $image_path = 'uploads/products/' . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        return true;
    } catch (Exception $e) {
        // If there's an error, rollback changes
        mysqli_rollback($conn);
        return false;
    }
}

// Handle product deletion with error handling
if (isset($_POST['delete_product'])) {
    $product_id = (int)$_POST['delete_product'];

    if (deleteProduct($conn, $product_id)) {
        $_SESSION['success_message'] = "Product deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting product. Please try again.";
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
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
                <a href="users.php" class="flex items-center px-6 py-3 text-white hover:bg-gray-700">
                    <span class="mx-3">Users</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-white bg-gray-700">
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
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Products Management</h2>
                    <button onclick="openAddModal()"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Add New Product
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <form class="flex gap-4">
                        <input
                            type="text"
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search products by name or description..."
                            class="flex-1 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative h-64 flex items-center justify-center bg-gray-100">
                                <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="max-h-64 w-auto object-contain p-2">
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-1">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                                <div class="inline-block bg-green-100 px-3 py-1 rounded-full">
                                    <span class="text-lg font-bold text-green-700">
                                        ₹<?php echo number_format($product['price'], 2); ?>
                                    </span>
                                </div>
                                <div class="text-gray-600 mt-3 mb-4 h-20 overflow-y-auto">
                                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                                </div>
                                <div class="flex justify-between items-center border-t pt-4">
                                    <a href="#"
                                        onclick="openEditModal({
                           id: '<?php echo $product['id']; ?>', 
                           name: '<?php echo htmlspecialchars($product['name']); ?>', 
                           price: '<?php echo $product['price']; ?>', 
                           description: '<?php echo htmlspecialchars($product['description']); ?>',
                           image: '<?php echo htmlspecialchars($product['image']); ?>'
                           }); return false;"
                                        class="bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200">
                                        Edit
                                    </a>
                                    <form method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="delete_product" value="<?php echo $product['id']; ?>">
                                        <button type="submit"
                                            class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
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

    <!-- Add Product Modal HTML -->
    <div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Add New Product</h2>
                <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="add_products.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Product Name *
                    </label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                        Price *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-600">₹</span>
                        <input type="number" name="price" id="price" step="0.01" required min="0"
                            class="w-full pl-8 pr-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description *
                    </label>
                    <textarea name="description" id="description" rows="4" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter detailed product description..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                        Product Image *
                    </label>
                    <input type="file" name="image" id="image" required accept="image/*"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal HTML -->
    <div id="editProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-backdrop">
        <div class="bg-white p-8 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto modal-content">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Edit Product</h2>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="update_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_name">
                        Product Name *
                    </label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_price">
                        Price *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-600">₹</span>
                        <input type="number" name="price" id="edit_price" step="0.01" required min="0"
                            class="w-full pl-8 pr-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">
                        Description *
                    </label>
                    <textarea name="description" id="edit_description" rows="4" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter detailed product description..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_image">
                        Product Image
                    </label>
                    <div class="mb-2">
                        <img id="current_image_preview" src="" alt="Current product image"
                            class="w-32 h-32 object-cover rounded border hidden">
                    </div>
                    <input type="file" name="image" id="edit_image" accept="image/*"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image. Accepted formats: JPG, PNG, GIF</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal management for products interface
        const modalHandlers = {
            // DOM Elements
            addModal: document.getElementById('addProductModal'),
            editModal: document.getElementById('editProductModal'),

            // Show add product modal
            showAddModal() {
                if (this.addModal) {
                    this.addModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            },

            // Hide add product modal
            hideAddModal() {
                if (this.addModal) {
                    this.addModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';

                    // Reset form
                    const form = this.addModal.querySelector('form');
                    if (form) form.reset();
                }
            },

            // Show edit product modal
            showEditModal(product) {
                if (!this.editModal) return;

                // Populate form fields
                const fields = {
                    'edit_id': product.id,
                    'edit_name': product.name,
                    'edit_price': product.price,
                    'edit_description': product.description
                };

                Object.entries(fields).forEach(([id, value]) => {
                    const element = document.getElementById(id);
                    if (element) element.value = value;
                });

                // Handle image preview
                const imagePreview = document.getElementById('current_image_preview');
                if (imagePreview && product.image) {
                    imagePreview.src = `uploads/products/${product.image}`;
                    imagePreview.classList.remove('hidden');
                }

                // Show modal
                this.editModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            },

            // Hide edit product modal
            hideEditModal() {
                if (this.editModal) {
                    this.editModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';

                    // Reset form
                    const form = this.editModal.querySelector('form');
                    if (form) form.reset();

                    // Hide image preview
                    const imagePreview = document.getElementById('current_image_preview');
                    if (imagePreview) {
                        imagePreview.classList.add('hidden');
                        imagePreview.src = '';
                    }
                }
            },

            // Initialize modal event listeners
            init() {
                // Close modals when clicking outside
                document.addEventListener('click', (e) => {
                    if (e.target.classList.contains('modal-backdrop')) {
                        this.hideAddModal();
                        this.hideEditModal();
                    }
                });

                // Close modals on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.hideAddModal();
                        this.hideEditModal();
                    }
                });

                // Prevent modal content clicks from closing the modal
                const modalContents = document.querySelectorAll('.modal-content');
                modalContents.forEach(content => {
                    content.addEventListener('click', (e) => e.stopPropagation());
                });

                // Handle file input change for image preview
                const editImageInput = document.getElementById('edit_image');
                if (editImageInput) {
                    editImageInput.addEventListener('change', function(e) {
                        const imagePreview = document.getElementById('current_image_preview');
                        if (imagePreview && this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imagePreview.src = e.target.result;
                                imagePreview.classList.remove('hidden');
                            };
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                }
            }
        };

        // Initialize modal handlers
        document.addEventListener('DOMContentLoaded', () => {
            modalHandlers.init();
        });

        // Export functions for global use
        window.openAddModal = () => modalHandlers.showAddModal();
        window.closeAddModal = () => modalHandlers.hideAddModal();
        window.openEditModal = (product) => modalHandlers.showEditModal(product);
        window.closeEditModal = () => modalHandlers.hideEditModal();
    </script>
</body>

</html>