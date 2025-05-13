<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #e74c3c; /* Red color for error */
        }

        p {
            font-size: 18px;
        }

        .btn {
            background-color: #4CAF50; /* Green button */
            color: white;
            padding: 15px 32px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <h1>Error: Missing Total</h1>
    <p>It seems like there was an issue with your checkout. Please try again.</p>
    
    <!-- Button to return to the checkout page -->
    <a href="checkout.php" class="btn">Return to Checkout</a>
</body>
</html>
