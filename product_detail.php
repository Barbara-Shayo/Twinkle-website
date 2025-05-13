<?php
include('includes/header.php');

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "ecommerce_new"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the product ID is valid from the URL
$clickedProductId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If ID is not valid, redirect to an error page or show a message
if ($clickedProductId <= 0) {
    echo "Invalid Product ID. Please try again.";
    exit();
}

// Fetch the clicked product from the database
$clickedProductQuery = "SELECT * FROM product_table WHERE id = ?";
$clickedProductStmt = $conn->prepare($clickedProductQuery);
$clickedProductStmt->bind_param("i", $clickedProductId);
$clickedProductStmt->execute();
$clickedProductResult = $clickedProductStmt->get_result();

if ($clickedProductResult->num_rows > 0) {
    $clickedProduct = $clickedProductResult->fetch_assoc();
} else {
    $clickedProduct = null; // No product found with that ID
}

// Fetch related products (excluding the clicked product) 
$relatedProductsQuery = "SELECT * FROM product_table WHERE id != ? ORDER BY RAND() LIMIT 10";
$relatedProductsStmt = $conn->prepare($relatedProductsQuery);
$relatedProductsStmt->bind_param("i", $clickedProductId);
$relatedProductsStmt->execute();
$relatedProductsResult = $relatedProductsStmt->get_result();

// Handle Add to Cart logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['id'];
    $productImage = $_POST['image'];
    $productPrice = $_POST['price'];

    // Initialize cart if not already initialized
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add the product to the cart
    $_SESSION['cart'][] = [
        'id' => $productId,
        'image' => $productImage,
        'price' => $productPrice
    ];

    // Redirect to the cart page
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .product-detail {
            display: flex;
            gap: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .product-detail img {
            width: 400px;
            height: auto;
            border-radius: 10px;
        }

        .product-info {
            flex: 1;
        }

        .product-info h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .product-info p {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-info .price {
            font-size: 24px;
            color: #ff4d4d;
            margin: 20px 0;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .action-buttons button {
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-to-cart-btn {
            background: linear-gradient(90deg, #ff4d4d, #ff1a1a);
            color: white;
        }

        .add-to-cart-btn:hover {
            background: linear-gradient(90deg, #d63f3f, #c12020);
        }

        .related-products {
            margin-top: 20px;
        }

        .related-products h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product-card {
            width: 200px;
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 16px;
            color: #ff4d4d;
            font-weight: bold;
        }

        .product-card a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

    <?php if ($clickedProduct): ?>
        <!-- Product Details Section -->
        <div class="product-detail">
            <img src="images/<?php echo htmlspecialchars($clickedProduct['image']); ?>" alt="image">
            <div class="product-info">
                <h1><?php echo htmlspecialchars($clickedProduct['details']); ?></h1>
                <p><?php echo htmlspecialchars($clickedProduct['description']); ?></p>
                <p class="price">Ksh <?php echo number_format($clickedProduct['price'], 2); ?></p>
                <div class="action-buttons">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $clickedProduct['id']; ?>">
                        <input type="hidden" name="image" value="<?php echo $clickedProduct['image']; ?>">
                        <input type="hidden" name="price" value="<?php echo $clickedProduct['price']; ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>Product not found. Please check the URL or try again later.</p>
    <?php endif; ?>

    <!-- Related Products Section -->
    <div class="related-products">
        <h2>Related Products</h2>
        <div class="product-container">
            <?php while ($product = $relatedProductsResult->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product_list.php?id=<?php echo $product['id']; ?>">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="image">
                        <h3><?php echo htmlspecialchars($product['details']); ?></h3>
                        <p>Ksh <?php echo number_format($product['price'], 2); ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>

<?php
// Close the prepared statements and database connection
if (isset($clickedProductStmt)) {
    $clickedProductStmt->close();
}
if (isset($relatedProductsStmt)) {
    $relatedProductsStmt->close();
}
$conn->close();
?>
