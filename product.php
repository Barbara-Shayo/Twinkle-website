<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = ''; // Update with your database password
$database = 'ecommerce_new'; // Update with your database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Fetch products from the database
$query = "SELECT * FROM product_table ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($query);

if (!$result) {
    die('Error fetching products: ' . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <style>
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
            padding: 15px;
            background: #fff;
        }

        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product-card p {
            margin: 10px 0;
        }

        .product-card .price {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Products</h1>
    <div class="product-container">
        <?php
        // Display products dynamically
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>
                        <img src='{$row['image']}' alt='{$row['description']}'>
                        <p>{$row['description']}</p>
                        <p class='price'>Ksh " . number_format($row['price'], 2) . "</p>
                        <p>{$row['details']}</p>
                    </div>";
            }
        } else {
            echo "<p>No products available</p>";
        }

        // Free result set and close connection
        $result->free();
        $conn->close();
        ?>
    </div>
</body>
</html>
