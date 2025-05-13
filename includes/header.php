<?php
// Start session if not already started (useful for tracking cart or user login state)
session_start();

// Check if user is logged in when accessing cart.php
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) === 'cart.php') {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Save current page URL for redirection
    header('Location: new_login.php'); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <style>
/* General Header Styles */
/* General Header Styles */
.header {
    display: flex;
    justify-content: center; /* Center all content horizontally */
    align-items: center;
    width: 100%;
    padding: 10px 20px;
    background-color: #f8f8f8;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
}

/* Container for Website Name, Search Bar, and Cart/Login Links */
.header-content {
    display: flex;
    justify-content: center; /* Center all content together */
    align-items: center;
    width: 100%;
    max-width: 1200px; /* Limit the width for better control */
}

/* Website Name */
.website-name {
    font-size: 22px;
    font-weight: bold;
    color: black;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-right: 5px; /* Reduced space between website name and search bar */
}

/* Search Bar Section */
.search-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 500px; /* Set a max-width to avoid search bar stretching */
    margin-left: 5px; /* Reduced space between search bar and website name */
}

.search-container form {
    display: flex;
    width: 100%;
}

.search-container input {
    padding: 8px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 25px;
    width: 80%;
    margin-right: 10px;
}

.search-container button {
    padding: 8px 14px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
}

.search-container button:hover {
    background-color: #0056b3;
}

/* Cart and Login Links */
.cart-login {
    display: flex;
    align-items: center;
}

.cart-link, .new_login-link {
    margin-left: 15px;
    font-size: 14px;
    text-decoration: none;
    color: #333;
}

.cart-link:hover, .new_login-link:hover {
    color: #007bff;
}


    </style>
</head>
<body>
<div class="header">
    <div class="header-content">
        <div class="website-name">T&S Store</div>
        
        <!-- Search Bar Section -->
        <div class="search-container">
            <form action="shop.php" method="GET">
                <input type="text" name="search_query" placeholder="I'm looking for..." 
                    value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <!-- Cart and Login Links -->
        <div class="cart-login">
            <a href="cart.php" class="cart-link">🛒 Cart</a>
            <a href="new_login.php" class="new_login-link">🔒 Login</a>
        </div>
    </div>
</div>
