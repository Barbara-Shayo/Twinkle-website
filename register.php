<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Global reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: 'Arial', sans-serif; /* You can replace this with any font */
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Container for the form */
        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Title styling */
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* Label styling */
        label {
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        /* Input fields styling */
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            color: #333;
        }

        /* Button styling */
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50; /* Green color */
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Button hover effect */
        button:hover {
            background-color: #45a049;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .register-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <form action="register_handler.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required><br>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
