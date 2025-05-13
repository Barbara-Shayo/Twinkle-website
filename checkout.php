<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit;
}
$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit;
}
$total = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json"); // Set response type to JSON

    // Validate required input fields
    $required_fields = ["user_id", "name", "address_line1", "city", "state", "country", "postal_code"];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            echo json_encode(["success" => false, "message" => "Invalid input data: Missing $field"]);
            exit();
        }
    }

    // Sanitize input
    $user_id = intval($_POST["user_id"]);
    $name = trim($_POST["name"]);
    $address_line1 = trim($_POST["address_line1"]);
    $address_line2 = trim($_POST["address_line2"] ?? "");
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $country = trim($_POST["country"]);
    $postal_code = trim($_POST["postal_code"]);


    // Check if the user already has an address
    $stmt = $conn->prepare("SELECT name, address_line1, address_line2, city, state, country, postal_code FROM user_addresses WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch existing data
        $stmt->bind_result($db_name, $db_address1, $db_address2, $db_city, $db_state, $db_country, $db_postal);
        $stmt->fetch();

        // Track updated fields
        $updates = [];
        if ($name !== $db_name) $updates[] = "Name";
        if ($address_line1 !== $db_address1) $updates[] = "Address Line 1";
        if ($address_line2 !== $db_address2) $updates[] = "Address Line 2";
        if ($city !== $db_city) $updates[] = "City";
        if ($state !== $db_state) $updates[] = "State";
        if ($country !== $db_country) $updates[] = "Country";
        if ($postal_code !== $db_postal) $updates[] = "Postal Code";

        if (!empty($updates)) {
            // Update only if changes are detected
            $update_stmt = $conn->prepare("UPDATE user_addresses SET name = ?, address_line1 = ?, address_line2 = ?, city = ?, state = ?, country = ?, postal_code = ? WHERE user_id = ?");
            $update_stmt->bind_param("sssssssi", $name, $address_line1, $address_line2, $city, $state, $country, $postal_code, $user_id);
            if ($update_stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Address updated successfully",
                    "updated_fields" => $updates
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update address"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No changes detected"]);
        }
    } else {
        // Insert new address
        $insert_stmt = $conn->prepare("INSERT INTO user_addresses (user_id, name, address_line1, address_line2, city, state, country, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("isssssss", $user_id, $name, $address_line1, $address_line2, $city, $state, $country, $postal_code);
        if ($insert_stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Address saved successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to save address"]);
        }
    }

    exit();
}

// Fetch User's Existing Address
$sql = "SELECT name, address_line1, address_line2, city, state, country, postal_code FROM user_addresses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $address_line1, $address_line2, $city, $state, $country, $postal_code);

$address_html = "Please enter your address.";
$button_text = "Enter Address";
if ($stmt->fetch()) {
    $address_html = "
        <strong>Name:</strong> $name<br>
        <strong>Address Line 1:</strong> $address_line1<br>
        <strong>City:</strong> $city<br>
        <strong>State:</strong> $state<br>
        <strong>Country:</strong> $country<br>
        <strong>Postal Code:</strong> $postal_code<br>
        " . (!empty($address_line2) ? "<strong>Address Line 2:</strong> $address_line2<br>" : "");
    $button_text = "Change Address";
}
$stmt->close();
$conn->close();

// Initialize totalAmount to prevent undefined variable error
$totalAmount = 0;

// Check if the session cart exists and is not empty
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
}



// MPESA Payment details
$institutionCode = "3224330"; // MPESA Business Number
$orderId = "ORD" . rand(100000, 999999); // Generate a random Order ID
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
      <!-- Define user_id from PHP Session -->
      <script>
        var user_id = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
        console.log("User ID:", user_id); // ✅ Debugging: Check if user_id is set
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: radial-gradient(#fff,#ffd6d6);
        }
        .grid-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px; /* Increase this value for more spacing */
    margin: 20px;
}
.container {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px; /* Adds space below each container */
}

        h2 {
            border-bottom: none;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .product-display {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .product-display img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product-details {
            flex-grow: 1;
        }
        .product-price {
            color: #888;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
/* Basic styling for the modal container */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if content overflows */
    background-color: rgba(0, 0, 0, 0.4); /* Semi-transparent black background */
    padding-top: 50px; /* Adjust padding to center the modal */
}

/* Modal Content (the actual pop-up box) */
.modal-content {
    background-color: #fff; /* White background */
    margin: 0 auto; /* Center the modal */
    padding: 20px;
    border-radius: 8px; /* Rounded corners */
    width: 90%; /* Set width to be responsive */
    max-width: 500px; /* Max width for larger screens */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
    max-height: 80vh; /* Limit height of modal */
    overflow-y: auto; /* Allow scrolling if content is too tall */
}

/* The Close Button (X) */
.close {
    color: #aaa;
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.pay-now-btn {
            display: none; /* Hidden initially */
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .place-order-btn {
            padding: 10px 20px;
            background: blue;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

          /* Popup styling */
          .popup-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: none;
            width: 300px;
            text-align: center;
        }
        .popup-box a {
            display: block;
            margin-top: 10px;
            color: blue;
            text-decoration: underline;
        }

/* Modal Title Styling */
h3 {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

/* Input field styling */
input[type="text"] {
    width: 100%; /* Full width */
    padding: 12px 15px; /* Padding inside the input */
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box; /* Make sure padding doesn't mess up the layout */
}

button {
    width: 100%;
    padding: 12px 0;
    background-color: #4CAF50; /* Green button */
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background-color: #45a049; /* Slightly darker green on hover */
}

/* Modify only the "Change Address" button */
button:nth-of-type(1) {  
    background: none; /* Remove background */
    border: none; /* Remove border */
    color: #004080; /* Dark blue text */
    font-size: 16px;
    cursor: pointer;
    text-decoration: underline; /* Make it look like a link */
    width: auto; /* Prevent full width */
    padding: 0;
    margin-top: 0;
}

button:nth-of-type(1):hover {
    color: #002060; /* Slightly darker blue on hover */
    text-decoration: none; /* Remove underline on hover */
}

#placeOrderBtn {
    background-color: #28a745 !important; /* Green color */
    color: white !important;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    padding: 12px 20px;
    width: auto; /* Prevents full width */
    text-decoration: none !important; /* Removes any underline */
}

#placeOrderBtn:hover {
    background-color: #218838 !important; /* Darker green on hover */
    text-decoration: none !important; /* Ensures underline doesn't appear */
}



/* Responsiveness */
@media screen and (max-width: 600px) {
    .modal-content {
        width: 90%;
        max-width: 350px;
    }

    .close {
        font-size: 26px;
        right: 15px;
        top: 10px;
    }

    h3 {
        font-size: 22px;
    }

    input[type="text"], button {
        font-size: 14px;
    }
}


    </style>
</head>
<body>
<div class="grid-container">
    <!-- Left Column -->
    <div>
        <div class="container" id="shipping-address">
            <h2>Shipping Address</h2>
            <div id="user-address">
                <p id="user-details"><?= $address_html ?></p>
            </div>
            <button id="address-action-btn"><?= $button_text ?></button>
        </div>

        <div id="address-modal" class="modal">
            <div class="modal-content">
                <span id="close-address-modal" class="close">&times;</span>
                <h3>Enter Address</h3>
                <input type="text" id="address_line1" placeholder="Address_line 1" required><br>
                <input type="text" id="address_line2" placeholder="Address_line 2 (Optional)"><br>
                <input type="text" id="city" placeholder="City" required><br>
                <input type="text" id="state" placeholder="State" required><br>
                <input type="text" id="name" placeholder="Name" required><br>
                <input type="text" id="country" placeholder="Country" required><br>
                <input type="text" id="postal_code" placeholder="Postal_code" required><br>
                <button id="saveAddressBtn">Save Address</button>
                <button id="cancel-address">Cancel</button>
            </div>
        </div>

        <div class="container" id="payment-method">
    <h2>Payment Method</h2>
    <div class="radio-group">
        <label for="paypal">
            <input type="radio" name="payment" id="paypal" value="paypal"> PayPal
        </label>
        <label for="mpesa">
            <input type="radio" name="payment" id="mpesa" value="mpesa"> MPESA
        </label>
    </div>
    <p id="mpesa-details"></p> <!-- Display saved MPESA number here -->
</div>

<!-- MPESA Modal -->
<div id="mpesa-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-mpesa-modal">&times;</span>
        <h3>Enter MPESA Number</h3>
        <input type="text" id="mpesa-number" placeholder="MPESA Number">
        <button id="save-mpesa">Save</button>
    </div>
</div>
        <!-- Cart Summary -->
        <div class="container">
            <h2>Cart Summary</h2>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="product-display">
                    <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="Product Image">
                    <div class="product-details">
                        <p><?= htmlspecialchars($item['details']) ?></p>
                        <p>Quantity: <?= $item['quantity'] ?></p>
                        <p class="product-price">Price: KSH <?= number_format($item['price'], 2) ?></p>
                    </div>
                </div>
                <?php $total += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>

            <p><strong>Total: KSH <?= number_format($total, 2) ?></strong></p>
        </div>
    </div>

    <!-- Summary Container (On the Right) -->
    <div class="summary-container" style="float: right; width: 30%;">
        <h3>Summary</h3>
        <p><strong>Total:</strong> KSH <?php echo number_format($totalAmount, 2); ?></p>
        <button id="placeOrderBtn" class="btn btn-success">Place Order</button>
    </div>
</div>

<!-- MPESA Number Input Popup -->
<div id="mpesaNumberPopup" class="popup-box">
    <div class="popup-content">
        <h3>Enter MPESA Number</h3>
        <label for="mpesaNumberInput">MPESA Number:</label>
        <input type="text" id="mpesaNumberInput" class="form-control" placeholder="e.g., 07XXXXXXXX">
        <button id="saveMpesaNumber" class="btn btn-primary">Save Number</button>
        <button id="closeMpesaPopup" class="btn btn-danger">Close</button>
    </div>
</div>

<!-- MPESA Payment Instructions Popup -->
<div id="paymentPopup" class="popup-box">
    <div class="popup-content">
        <h3>Payment Details</h3>
        <p><strong>Institution Code:</strong> <span><?php echo $institutionCode; ?></span></p>
        <p>Follow these steps to complete your payment:</p>
        <ol>
            <li>Go to your SIM toolkit.</li>
            <li>Choose "Lipa na MPESA".</li>
            <li>Enter <strong><?php echo $institutionCode; ?></strong> as the business number.</li>
            <li>Enter your order ID <strong><?php echo $orderId; ?></strong> as the account number.</li>
            <li>Enter <strong>KSH <span id="popupAmount"></span></strong>.</li>
            <li>Enter your MPESA PIN to complete the payment.</li>
        </ol>

        <!-- Single Close Button (Auto Redirects) -->
        <button id="closePaymentPopup" class="btn btn-danger">Close</button>
    </div>
</div>

<!-- Summary Container (Where the Total Amount is Stored) -->
<div id="summaryContainer">
    <p>Total Amount: KSH <span id="totalAmount">2500</span></p> <!-- Example total -->
</div>

<style>
#summaryContainer {
    display: none !important;
}
</style>




<script>
document.addEventListener("DOMContentLoaded", function () {
    const placeOrderBtn = document.getElementById("placeOrderBtn");
    const mpesaRadio = document.getElementById("mpesa");
    const mpesaNumberPopup = document.getElementById("mpesaNumberPopup");
    const saveMpesaNumber = document.getElementById("saveMpesaNumber");
    const closeMpesaPopup = document.getElementById("closeMpesaPopup");
    const paymentPopup = document.getElementById("paymentPopup");
    const closePaymentPopup = document.getElementById("closePaymentPopup");
    const mpesaNumberInput = document.getElementById("mpesaNumberInput");
    const popupAmount = document.getElementById("popupAmount");
    const totalAmount = document.getElementById("totalAmount").textContent; // Fetch total from summary

    // Step 1: Show MPESA Number Input Popup when MPESA is selected
    mpesaRadio.addEventListener("change", function () {
        if (mpesaRadio.checked) {
            mpesaNumberPopup.style.display = "flex";
        }
    });

    // Step 2: Save MPESA number and enable "Pay Now"
    saveMpesaNumber.addEventListener("click", function () {
        const mpesaNumber = mpesaNumberInput.value.trim();

        // Validate MPESA number
        if (!mpesaNumber || isNaN(mpesaNumber) || mpesaNumber.length < 10) {
            alert("Please enter a valid MPESA number (at least 10 digits).");
            return;
        }

        // Enable "Pay Now" button
        placeOrderBtn.textContent = "Pay Now";
        placeOrderBtn.classList.remove("btn-success");
        placeOrderBtn.classList.add("btn-primary");
        placeOrderBtn.disabled = false;

        // Hide MPESA number popup
        mpesaNumberPopup.style.display = "none";
    });

    // Step 3: Show Payment Instructions Popup when clicking "Pay Now"
    placeOrderBtn.addEventListener("click", function () {
        if (placeOrderBtn.textContent === "Pay Now") {
            popupAmount.textContent = totalAmount; // Set amount dynamically from summary
            paymentPopup.style.display = "flex";
        }
    });

    // Close MPESA Number Popup
    closeMpesaPopup.addEventListener("click", function () {
        mpesaNumberPopup.style.display = "none";
    });

    // Close Payment Popup
    closePaymentPopup.addEventListener("click", function () {
        paymentPopup.style.display = "none";
    });

    // Close popups if user clicks outside
    window.addEventListener("click", function (e) {
        if (e.target === mpesaNumberPopup) {
            mpesaNumberPopup.style.display = "none";
        }
        if (e.target === paymentPopup) {
            paymentPopup.style.display = "none";
        }
    });
     // Close Payment Popup & Redirect to Shop
     closePaymentPopup.addEventListener("click", function () {
        paymentPopup.style.display = "none";
        window.location.href = "shop.php"; // Redirect to shop
    });
});
</script>

<style>
/* General Popup Styling */
.popup-box {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    text-align: center;
    border-radius: 10px;
    width: 350px;
}
.popup-content {
    max-width: 100%;
}
</style>


<script>
    var user_id = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Ensure elements exist before adding event listeners
    const addressBtn = document.getElementById("address-action-btn");
    const closeModal = document.getElementById("cancel-address");
    const saveBtn = document.getElementById("saveAddressBtn");
    const modal = document.getElementById("address-modal");
    const userDetails = document.getElementById("user-details");

    if (addressBtn) {
        addressBtn.addEventListener("click", function () {
            modal.style.display = "block";
        });
    }

    if (closeModal) {
        closeModal.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default form submission

            const user_id = "<?= $user_id ?>"; // Ensure this is defined in PHP
            if (!user_id) {
                alert("User ID is missing. Please log in.");
                return;
            }

            // Get input values
            const formData = new FormData();
            formData.append("user_id", user_id);
            formData.append("name", document.getElementById("name").value);
            formData.append("address_line1", document.getElementById("address_line1").value);
            formData.append("address_line2", document.getElementById("address_line2").value || "");
            formData.append("city", document.getElementById("city").value);
            formData.append("state", document.getElementById("state").value);
            formData.append("country", document.getElementById("country").value);
            formData.append("postal_code", document.getElementById("postal_code").value);

            fetch("checkout.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                console.log("Server Response:", data); // Debugging

                if (data.success === true || data.success === "true") {
                    alert("✅ Address saved successfully!");
                    location.reload(); // Refresh to show updated address
                } else {
                    alert("❌ Error: " + (data.message || "Unknown error occurred"));
                }
            })
            .catch(error => {
                alert("⚠️ Failed to save address. Please try again.");
                console.error("Error:", error);
            });
        });
    }
});
</script>
</body>
</html>
