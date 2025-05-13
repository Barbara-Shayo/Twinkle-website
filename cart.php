<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<div class="popup" id="loginPopup">
            <h3>You need to log in to view your cart</h3>
            <a href="new_login.php">Login</a>
          </div>';
    exit; // Stop further execution
}

// Initialize the cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['id'] ?? null;
    $details = $_POST['details'] ?? 'No description provided';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';
    
    // Avoid duplicate entries
    $itemFound = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['details'] === $details) {
            $item['quantity'] += 1;
            $itemFound = true;
            break;
        }
    }
    if (!$itemFound) {
        $_SESSION['cart'][] = [
            'id' => $productId, // Add the product ID here
            'details' => $details,
            'price' => $price,
            'image' => $image,
            'quantity' => 1,
        ];
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle deleting items from the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $itemToDelete = $_POST['item_index']; // Index of the item to delete
    if (isset($_SESSION['cart'][$itemToDelete])) {
        unset($_SESSION['cart'][$itemToDelete]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Calculate total price of items in the cart
$totalPrice = 0;






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f2f2f2; /* Light grey background */
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        a, button {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        a:hover, button:hover {
            background-color: #0056b3;
        }
        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button.delete-btn {
            background-color: #dc3545;
        }
        button.delete-btn:hover {
            background-color: #c82333;
        }
        /* Popup styling */
        .popup {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: none; /* Removes background color */
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    z-index: 1000;
    width: 300px;
}

.popup h3 {
    background: none; /* Add background only to text area if needed */
    padding: 10px;
    border-radius: 10px;
}

.popup a {
    display: inline-block;
    margin-top: 10px;
    color: white; /* White text */
    background-color: lightcoral; /* Light red background for button */
    text-decoration: none;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 50px; /* Rounded button */
    text-align: center;
}

.popup a:hover {
    background-color: #e57373; /* Slightly darker red on hover */
}




    </style>
</head>
<body>

<h1>Your Shopping Cart</h1>

<?php 


// Ensure cart session is set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (empty($_SESSION['cart'])): ?>
    <!-- Popup for empty cart -->
    <div class="popup" id="cartPopup">
        <h3>Your Cart is Empty</h3>
        <a href="product_list.php">Explore Items</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("cartPopup").style.display = "block";
        });
    </script>

<?php else: ?>

        <table>
            <tr>
                <th>Details</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <?php 
                    $itemTotal = $item['price'] * $item['quantity']; 
                    $totalPrice += $itemTotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['details']) ?></td>
                    <td>KSH <?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>KSH <?= number_format($itemTotal, 2) ?></td>
                    <td><img src="images/<?= htmlspecialchars($item['image']) ?>" alt="Product Image"></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="item_index" value="<?= $index ?>">
                            <button type="submit" name="delete_item" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
        </table>
        <h3>Total: KSH <?= number_format($totalPrice, 2) ?></h3>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        <?php endif; ?> <!-- Properly closing the if statement -->

       

</body>
</html>
