<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'ecommerce_new');

// Check connection
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input (you can add more validations here)
    if (empty($username) || empty($email) || empty($password)) {
        die('Error: Please fill in all fields.');
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL statement to insert the new user into the database
    $stmt = $mysqli->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    if (!$stmt) {
        die('SQL Error: ' . $mysqli->error);
    }

    // Bind the parameters and execute the query
    $stmt->bind_param('sss', $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        // Registration successful, redirect to login page
        header('Location: new_login.php');
        exit(); // Make sure no other code runs after redirection
    } else {
        die('Error: ' . $stmt->error); // Handle any errors
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$mysqli->close();
?>
