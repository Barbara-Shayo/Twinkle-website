<?php
// footer.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <!-- Font Awesome for Social Media Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Footer Styles */
        .footer {
            background: #f0f0f0; /* Light grey background */
            color: #8a8a8a;
            font-size: 14px; /* Base font size */
            padding: 30px 0 10px;
        }

        .footer p {
            color: #8a8a8a;
            font-size: 14px; /* Slightly larger font for paragraphs */
        }

        .footer h3 {
            color: #333; /* Darker heading color */
            margin-bottom: 10px;
            font-size: 18px; /* Increased size for h3 */
        }

        .footer .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer-col-1, .footer-col-2, .footer-col-3, .footer-col-4 {
            min-width: 200px;
            margin-bottom: 15px;
        }

        .footer-col-1 {
            flex-basis: 28%;
        }

        .footer-col-2 {
            flex: 1;
            text-align: center;
        }

        .footer-col-2 img {
            width: 150px;
            margin-bottom: 15px;
        }

        .footer-col-3, .footer-col-4 {
            flex-basis: 12%;
            text-align: center;
        }

        .footer ul {
            list-style-type: none;
            padding: 0;
        }

        .footer ul li {
            margin-bottom: 8px;
        }

        .footer ul li a {
            text-decoration: none;
            color: #8a8a8a;
            font-size: 14px; /* Slightly larger font for links */
        }

        .footer ul li a:hover {
            color: #333; /* Darker hover color */
        }

        .footer-bottom {
            text-align: center;
            margin-top: 15px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .footer .icon {
            width: 20px;
            display: inline-block;
            margin: 0 3px;
        }
         /* Admin Link */
         .admin-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        .admin-link a {
            color: #ff6b81;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
        }

        .admin-link a:hover {
            color: #e63950;
        }
    </style>
</head>
<body>
    <!------ Footer Section -------->
    <div class="footer">
        <div class="container">
            <!-- App Download Section -->
            <div class="footer-col-1">
                <h3>Download Our App</h3>
                <p>Download the app for Android and iOS mobile phones.</p>
            </div>

            <!-- Logo and Mission -->
            <div class="footer-col-2">
            <img src="http://localhost/E-commerce/images/TS-logo.png" alt="Logo">


                <p>To apply services and products that enhance physical appearance and confidence for our clients.</p>
            </div>

            <!-- Social Media Links -->
            <div class="footer-col-3">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a></li>
                    <li><a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a></li>
                    <li><a href="#"><i class="fa-brands fa-tiktok"></i> TikTok</a></li>
                    <li><a href="#"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="footer-col-4">
                <h3>Contact Us</h3>
                <ul>
                    <li><a href="mailto:okelohope@gmail.com">Email: okelohope@gmail.com</a></li>
                    <li><a href="tel:+254797664235">Phone/WhatsApp: +254797664235</a></li>
                </ul>
            </div>
       
     <!-- Admin Login Link -->
     <div class="admin-link">
            <a href="admin/admin_login.php">Sign Admin</a>
        </div>
    
</body>
</html>
