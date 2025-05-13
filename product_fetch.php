<?php
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

// Get offset and limit from POST request
$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
$limit = 6; // Number of products to load at a time

// Fetch products
$query = "SELECT * FROM product_table WHERE is_featured = 1 ORDER BY RAND() LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Output products as HTML
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<a href="product_list.php?id=' . $row['id'] . '" class="product-link">';
        echo '<div class="product-card">';
        echo '<img src="images/' . $row['image'] . '" alt="Product">';
        echo '<h5>' . $row['details'] . '</h5>';
        echo '<p>Ksh ' . number_format($row['price'], 2) . '</p>';
        echo '</div>';
        echo '</a>';
    }
} else {
    echo ''; // No more products to load
}

$conn->close();
?>
