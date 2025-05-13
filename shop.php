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

// Ensure the product ID is valid
$clickedProductId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($clickedProductId > 0) {
    // Fetch the clicked product
    $clickedProductQuery = "SELECT * FROM product_table WHERE id = ?";
    $clickedProductStmt = $conn->prepare($clickedProductQuery);
    $clickedProductStmt->bind_param("i", $clickedProductId);
    $clickedProductStmt->execute();
    $clickedProductResult = $clickedProductStmt->get_result();
    
    if ($clickedProductResult->num_rows > 0) {
        $clickedProduct = $clickedProductResult->fetch_assoc();
    } else {
        $clickedProduct = null; // No product found
    }
} else {
    $clickedProduct = null; // Invalid or missing ID
}

// Fetch related products (excluding the clicked product)
$relatedProductsQuery = "SELECT * FROM product_table WHERE id != ? ORDER BY RAND() LIMIT 10";
$relatedProductsStmt = $conn->prepare($relatedProductsQuery);
$relatedProductsStmt->bind_param("i", $clickedProductId);
$relatedProductsStmt->execute();
$relatedProductsResult = $relatedProductsStmt->get_result();

// Fetch latest products for "What's New" section
$latestProductsQuery = "SELECT * FROM product_table ORDER BY created_at DESC LIMIT 10";
$latestProductsResult = $conn->query($latestProductsQuery);

$allOtherProductsQuery = "
    SELECT p.* FROM product_table p 
    LEFT JOIN (SELECT id FROM product_table ORDER BY created_at DESC LIMIT 10) latest 
    ON p.id = latest.id 
    WHERE latest.id IS NULL 
    ORDER BY p.created_at DESC";
$allOtherProductsResult = $conn->query($allOtherProductsQuery);



// Handle Add to Cart
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
    <title>Shop - Product Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        /* Shared styling */
        .products-container,
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

        .product-card h3, .product-card h5 {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 16px;
            color: black;
            font-weight: bold;
        }

        .product-card a {
            text-decoration: none;
            color: inherit;
        }

        /* "What's New" Section */
        .whats-new a {
    text-decoration: none; /* Remove underline */
    color: inherit; /* Ensure the text color remains consistent */
}

        .whats-new h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Product Details */
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
            color:black;
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

        /* General Styling for Product Sections */
.whats-new, 
.all-products {
    margin-bottom: 40px; /* Adds space between sections */
}

.whats-new h2, 
.all-products h2 {
    font-size: 24px;
    margin-bottom: 20px;
    text-align: center; /* Centers the headings */
    color: #333; /* Dark gray text */
}

/* Product container styling */
.products-container {
    display: flex;
    flex-wrap: wrap; /* Ensures products wrap to the next line if necessary */
    justify-content: center; /* Centers products in the container */
    gap: 20px; /* Adds spacing between product cards */
}

/* Individual product card */
.product-card {
    width: 200px; /* Fixed width */
    background: white;
    padding: 15px;
    border: 1px solid #ddd; /* Light gray border */
    border-radius: 10px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s; /* Smooth effects */
    text-decoration: none; /* Removes underline from links */
    color: inherit; /* Keeps text color consistent */
}

/* Hover effect for product cards */
.product-card:hover {
    transform: scale(1.05); /* Slightly enlarges the card */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Adds a shadow effect */
}

/* Product images inside cards */
.product-card img {
    width: 100%;
    height: 150px;
    object-fit: cover; /* Ensures images fit without distortion */
    border-radius: 10px;
}

/* Product details */
.product-card h5 {
    font-size: 16px;
    margin: 10px 0;
    color: #444; /* Medium-dark gray */
}

/* Product price styling */
.product-card p {
    font-size: 16px;
    color: black;
    font-weight: bold;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .products-container {
        flex-direction: column; /* Stack products in a column on smaller screens */
        align-items: center; /* Center them */
    }

    .product-card {
        width: 90%; /* Make the product cards take up more space on small screens */
    }
}

    </style>
</head>
<body>
    <!-- "What's New" Section -->
<!-- "What's New" Section -->
<div class="whats-new">
    <h2>What's New</h2>
    <div class="products-container">
        <?php while ($product = $latestProductsResult->fetch_assoc()): ?>
            <a href="product_list.php?id=<?php echo $product['id']; ?>" class="product-card">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product">
                <h5><?php echo htmlspecialchars($product['details']); ?></h5>
                <p>Ksh <?php echo number_format($product['price'], 2); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<!-- Display all other products -->
<div class="all-products">
    <h2>More Products</h2>
    <div class="products-container">
        <?php while ($product = $allOtherProductsResult->fetch_assoc()): ?>
            <a href="product_list.php?id=<?php echo $product['id']; ?>" class="product-card">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product">
                <h5><?php echo htmlspecialchars($product['details']); ?></h5>
                <p>Ksh <?php echo number_format($product['price'], 2); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</div>



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
