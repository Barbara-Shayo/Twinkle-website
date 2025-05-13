<?php
include('includes/header.php');

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
    $productQuery = "SELECT * FROM product_table WHERE id = ?";
    $productStmt = $conn->prepare($productQuery);
    $productStmt->bind_param("i", $clickedProductId);
    $productStmt->execute();
    $productResult = $productStmt->get_result();

    if ($productResult->num_rows > 0) {
        $product = $productResult->fetch_assoc();
    } else {
        $product = null; // No product found
    }
} else {
    $product = null; // Invalid or missing ID
}

// Fetch related products (initially load 10)
$relatedProductsQuery = "SELECT * FROM product_table WHERE id != ? ORDER BY RAND() LIMIT 10";
$relatedProductsStmt = $conn->prepare($relatedProductsQuery);
$relatedProductsStmt->bind_param("i", $clickedProductId);
$relatedProductsStmt->execute();
$relatedProductsResult = $relatedProductsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <script src="script.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: radial-gradient(#fff,#ffd6d6);
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
            font-size: 16px;
            margin: 10px 0;
        }

        .product-info .price {
            font-size: 24px;
            color: black;
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

        .buy-now-btn {
            background: linear-gradient(90deg, #1a75ff, #004d99);
            color: white;
        }

        .buy-now-btn:hover {
            background: linear-gradient(90deg, #0056b3, #003366);
        }

        .related-products {
            margin-top: 20px;
        }

        .related-products h2 {
            font-size: 16px;
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
            font-size: 16px;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 16px;
            color:black;
            font-weight: normal;
        }

        .product-card a {
    text-decoration: none !important; /* Remove underline */
    color: black !important; /* Change text color */
    font-weight: bold; /* Keep bold styling */
}


.product-card a:hover {
    color: #555; /* Optional: Dark gray on hover */
}


        .view-more-btn {
    font-size: 16px;
    color: darkgray;
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: block;
    text-align: center;
    margin: 30px auto; /* Adds spacing and centers it */
    transition: color 0.3s;
}

.view-more-btn:hover {
    color: black;
}




    
    </style>
</head>
<body>

    <?php if ($product): ?>
        <div class="product-detail">
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="image">
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['details']); ?></h1>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="price">Ksh <?php echo number_format($product['price'], 2); ?></p>
                <div class="action-buttons">
                <form action="cart.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
    <input type="hidden" name="image" value="<?php echo $product['image']; ?>">
    <input type="hidden" name="details" value="<?php echo htmlspecialchars($product['details']); ?>">
    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
</form>


                </div>
                
            </div>
        </div>
        
    <?php endif; ?>

    <div class="related-products">
        <h2>Related Products</h2>
        <div id="product-container" class="product-container">
            <?php while ($row = $relatedProductsResult->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product_list.php?id=<?php echo $row['id']; ?>">
                        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="image">
                        <h3><?php echo htmlspecialchars($row['details']); ?></h3>
                        <p>Ksh <?php echo number_format($row['price'], 2); ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <button id="view-more-btn" class="view-more-btn">View More</button>
    </div>

<script>
let currentPage = 1; // Track the current page
document.getElementById('view-more-btn').addEventListener('click', function () {
    currentPage++;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `product_fetch.php?page=${currentPage}&exclude_id=<?php echo $clickedProductId; ?>`, true);

    xhr.onload = function () {
        if (this.status === 200) {
            const newProducts = this.responseText;
            const container = document.getElementById('product-container');
            container.insertAdjacentHTML('beforeend', newProducts);

            if (!newProducts.trim()) {
                document.getElementById('view-more-btn').style.display = 'none';
            }
        } else {
            console.error('Failed to fetch products. Status:', this.status);
        }
    };

    xhr.onerror = function () {
        console.error('AJAX error occurred.');
    };

    xhr.send();
});
</script>

</body>
</html>

<?php
// Close the prepared statements and database connection
if (isset($productStmt)) {
    $productStmt->close();
}
if (isset($relatedProductsStmt)) {
    $relatedProductsStmt->close();
}
$conn->close();
?>
