<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $product['image'];

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target_dir = "E:\XAMPP\htdocs\ecommerce\uploads";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sdssi", $name, $price, $description, $image, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: view_products.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link rel="stylesheet" href="edit_products.css"> -->
    <script>
        function previewImage() {
            var file = document.querySelector('input[type=file]').files[0];
            var reader = new FileReader();
            reader.onloadend = function() {
                document.getElementById('imagePreview').src = reader.result;
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').src = "<?php echo '../uploads/' . $product['image']; ?>";
            }
        }
    </script>

<style>
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10vh;
            /* Optional: Full height to center vertically as well */
        }
    </style>
</head>

<body>

    <main class="bg-gray-900 flex items-center justify-center min-h-screen">
        <div class="relative mt-2 p-4 w-full max-w-2xl h-full md:h-auto">
            <div class="relative p-5 bg-white rounded-lg shadow dark:bg-gray-950 sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Product</h3>
                    <button type="button" onclick="window.location.href='view_products.php'" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="grid gap-4 mb-4 sm:grid-cols-1">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" class=" p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>

                        <div>
                            <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price:</label>
                            <input type="number" step="0.01" id="price" name="price" value="<?php echo $product['price']; ?>" class="p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description:</label>
                            <textarea id="description" name="description" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required><?php echo $product['description']; ?></textarea>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="image">Image:</label>
                            <input class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-700 block p-2.5 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" type="file" id="image" name="image" accept="image/*" onchange="previewImage()">
                        </div>

                        <div class="button-container">
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>
<!-- 
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="image">Add Product Image:</label>
                            <input class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-700 block p-2.5 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" id="image" name="image" accept="image/*" onchange="previewImage()" required>
                        </div>
                        <div class="button-container">
                            <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add New Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div> -->