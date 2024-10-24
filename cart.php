<?php
session_start(); // Add this at the very top
include 'db.php';

// Check if user is logged in - if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit(); // Important to prevent further code execution
}

include 'header.php';

// Get cart items for logged in user
$cart_items = [];
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <h1 class="mt-12 text-3xl font-bold text-white mb-8">Shopping Cart</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <p class="text-white text-center">Your cart is empty.</p>
                <a href="products.php" class="mt-4 block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <?php 
                $total = 0;
                foreach ($cart_items as $item): 
                    $total += $item['price'] * $item['quantity'];
                ?>
                    <div class="flex items-center justify-between border-b border-gray-700 py-4">
                        <div class="flex items-center">
                            <img src="uploads/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-20 h-20 object-cover rounded">
                            <div class="ml-4">
                                <h2 class="text-white font-bold"><?php echo $item['name']; ?></h2>
                                <p class="text-gray-400">₹<?php echo $item['price']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <form action="update_cart.php" method="POST" class="flex items-center">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" 
                                       class="w-16 px-2 py-1 bg-gray-700 text-white rounded mr-2">
                                <button type="submit" class="text-blue-500 hover:text-blue-400">Update</button>
                            </form>
                            <form action="remove_from_cart.php" method="POST" class="ml-4">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-400">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-6">
                    <div class="flex justify-between text-white">
                        <span class="font-bold">Total:</span>
                        <span class="font-bold">₹<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <?php
                    $whatsappMessage = "Order Details:\n\n";
                    foreach ($cart_items as $item) {
                        $whatsappMessage .= "{$item['name']} x {$item['quantity']} - ₹" . ($item['price'] * $item['quantity']) . "\n";
                    }
                    $whatsappMessage .= "\nTotal: ₹{$total}";
                    ?>
                    
                    <a href="https://api.whatsapp.com/send?phone=+919400528164&text=<?php echo urlencode($whatsappMessage); ?>" 
                       class="mt-4 block w-full bg-green-600 text-white text-center py-3 rounded-lg hover:bg-green-700"
                       target="_blank">
                        Order via WhatsApp
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>