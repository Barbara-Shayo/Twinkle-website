<?php
// Database connection setup
$host = "localhost";
$user = "root";
$password = "";
$database = "ecommerce_new";

$mysqli = new mysqli($host, $user, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $total = isset($_POST['total']) ? $_POST['total'] : null;
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : null;
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

    // Validate inputs
    if ($user_id && $total && $full_name && $address && $phone && $payment_method) {
        // Insert data into the database
        $sql = "INSERT INTO orders (user_id, total, created_at, Full_name, address, description, phone, payment_method) 
                VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('idssss', $user_id, $total, $full_name, $address, $description, $phone, $payment_method);

            if ($stmt->execute()) {
                $message = "Order processed successfully. Order ID: " . $stmt->insert_id;
            } else {
                $message = "Execution failed: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "Prepare failed: " . $mysqli->error;
        }
    } else {
        $message = "All fields are required.";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f5f5f5;
        }
        form {
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #007BFF;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 15px;
            background-color: #e7f7e4;
            border: 1px solid #d4edda;
            color: #155724;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Process Payment</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="process_payment.php" method="POST">
        <label for="user_id">User ID:</label>
        <input type="number" id="user_id" name="user_id" required>

        <label for="total">Total:</label>
        <input type="text" id="total" name="total" required>

        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required>

        <label for="payment_method">Payment Method:</label>
        <input type="text" id="payment_method" name="payment_method" required>

        <button type="submit">Submit Order</button>
    </form>
</body>
</html>
