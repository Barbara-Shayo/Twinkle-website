<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart for testing
if (isset($_GET['add'])) {
    $_SESSION['cart'][] = ['id' => 1, 'name' => 'Test Product'];
    echo "Item added to cart.";
}

// Remove item from cart for testing
if (isset($_GET['remove'])) {
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] == 1) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            echo "Item removed from cart.";
        }
    }
}

// Display cart contents

?>
<a href="?add">Add Item</a>
<a href="?remove">Remove Item</a>
