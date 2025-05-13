<?php
include('function.php');
return generateAccessToken();
include('includes/header.php');

// Database connection
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "ecommerce_new"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Featured Products
$featuredQuery = "SELECT * FROM product_table WHERE is_featured = 1 LIMIT 4";
$featuredResult = $conn->query($featuredQuery);

// Fetch Latest Products (What's New)
$latestQuery = "SELECT * FROM product_table ORDER BY created_at DESC LIMIT 6";
$latestResult = $conn->query($latestQuery);

// Fetch initial "You May Also Like" products (first 6 products)
$mayLikeQuery = "SELECT * FROM product_table WHERE is_featured = 1 ORDER BY RAND() LIMIT 12";
$mayLikeResult = $conn->query($mayLikeQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Website</title>
    <style>
        /* Include CSS from earlier with slider styles */
       /* General body styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: radial-gradient(#fff,#ffd6d6);
}

a {
    text-decoration: none;
    color: inherit;
}

a:hover {
    text-decoration: underline;
}

/* Section container styles */
.section-container {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    margin: 20px auto;
    max-width: 1200px;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
}

/* Individual container styles */
.whats-new-container,
.may-like-container {
    padding: 10px;
}
#mayLikeProducts {
    display: grid;
    grid-template-columns: repeat(6, 1fr); /* 6 items per row */
    gap: 20px;
}


.product-item {
    text-align: center;
}


/* Product grid and card styles */
/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Auto-fit products */
    gap: 10px;
    justify-items: center;
}


.view-more-container {
    text-align: center;
    margin: 20px 0;
}

#viewMoreBtn {
    background-color: transparent; /* No background color */
    color: grey; /* Grey text */
    text-decoration: none; /* Remove underline */
    font-weight: normal; /* Remove bold text */
    border: none; /* Remove border */
    cursor: pointer; /* Change cursor to pointer for better UX */
}

#viewMoreBtn:hover {
    color: darkgrey; /* Optional: Slightly change color on hover */
    text-decoration: none; /* Ensure no underline on hover */
}


/* Product card styles */
.product-card {
    padding: 10px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s, border 0.3s;
    border: none; /* Remove default border */
    border-radius: 5px;
    background-color: transparent; /* Default: transparent background */
}

/* Ensure product links are styled correctly */
.product-link {
    text-decoration: none; /* No underline for product links */
    color: inherit; /* Inherit text color */
}

.product-link:hover {
    text-decoration: none; /* No underline on hover */
    color: inherit; /* Maintain original color */
}

/* Product card styles */
.product-card {
    padding: 10px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s, border 0.3s;
    border: none; /* Remove default border */
    border-radius: 5px;
    background-color: transparent; /* Default: transparent background */
}

.product-card:hover {
    transform: scale(1.05);
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    background-color: white; /* Background color on hover */
    border: 1px solid #eaeaea; /* Add border on hover */
}

.product-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
}


/* Slider container styles */
.slider-container {
    margin: 30px auto;
    position: relative;
    width: 100%;
    max-width: 1200px;
    height: 500px;
    background-color: #f8f8f8;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
}

/* Individual slide styles */
.slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 50px;
    opacity: 0;
    transition: opacity 1.5s ease-in-out;
}

.slide.active {
    opacity: 1;
}

.slide img {
    flex: 1;
    height: 100%;
    object-fit: cover;
    border-radius: 0 20px 20px 0;
}

.slide-text {
    flex: 1;
    font-size: 36px;
    font-weight: bold;
    color: #333;
    text-align: left;
    padding: 20px;
}

/* Section header styles */
/* Section header styles */
.section-header {
    text-align: center; /* Center-align the title */
    margin: 20px 0; /* Add spacing around the titles */
}

.section-title {
    font-size: 24px;
    font-weight: bold; /* Bold text */
    display: inline-block; /* Ensures proper spacing for the border */
    color: black; /* Title text color */
    margin: 0; /* Remove default margin */
    padding-bottom: 5px; /* Add space between text and the underline */
    border-bottom: 3px solid red; /* Bold red underline */
}



/* View More button styles */
.view-more-btn {
    font-size: 14px;
    font-weight: normal;
    color: grey; /* Change text color to grey */
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: none; /* Ensure no underline */
    padding: 0;
}

.view-more-btn:hover {
    color: darkgrey; /* Optional: Slightly darker grey on hover */
    text-decoration: none; /* Ensure no underline on hover */
}

.section-container .whats-new-btn {
    font-size: 14px;
    color: darkgrey; /* Dark grey text */
    text-decoration: none; /* Remove underline */
    font-weight: normal; /* Remove bold text */
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.section-container .whats-new-btn:hover {
    color: grey; /* Slightly lighter grey on hover */
    text-decoration: none; /* Ensure no underline on hover */
}

</style>
</head>
<body>

<!-- Slider -->
<div class="slider-container">
    <div class="slide active">
        <div class="slide-text">Massive Discounts</div>
        <img src="images/slide5.jpeg" alt="Slide 1">
    </div>
    <div class="slide">
        <div class="slide-text">Free Shipping</div>
        <img src="images/slide1.jpeg" alt="Slide 2">
    </div>
    <div class="slide">
        <div class="slide-text">New Year Sale</div>
        <img src="images/slide3.jpeg" alt="Slide 3">
    </div>
    <div class="slide">
        <div class="slide-text">Best Quality Products</div>
        <img src="images/slide7.jpeg" alt="Slide 4">
    </div>
</div>

<!-- What's New Section -->
<div class="section-header">
    <h2 class="section-title">What's New</h2>
</div>
<div class="section-container">
    <a href="shop.php" class="view-more-btn">View More</a>
    <div class="whats-new-container">
        <div class="products-grid">
            <?php while ($row = $latestResult->fetch_assoc()): ?>
                <a href="product_list.php?id=<?php echo $row['id']; ?>" class="product-link">
                    <div class="product-card">
                        <img src="images/<?php echo $row['image']; ?>" alt="Product">
                        <h5><?php echo $row['details']; ?></h5>
                        <p>Ksh <?php echo number_format($row['price'], 2); ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- You May Also Like Section -->
<div class="section-header">
    <h2 class="section-title">You May Also Like</h2>
</div>
<div class="section-container may-like-container">
    <div class="products-grid" id="mayLikeProducts">
        <?php
        $initialQuery = "SELECT * FROM product_table WHERE is_featured = 1 ORDER BY RAND() LIMIT 12";
        $initialResult = $conn->query($initialQuery);

        while ($row = $initialResult->fetch_assoc()) {
            echo '<a href="product_list.php?id=' . $row['id'] . '" class="product-link">';
            echo '<div class="product-card">';
            echo '<img src="images/' . $row['image'] . '" alt="Product">';
            echo '<h5>' . $row['details'] . '</h5>';
            echo '<p>Ksh ' . number_format($row['price'], 2) . '</p>';
            echo '</div>';
            echo '</a>';
        }
        ?>
    </div>
    <div class="view-more-container" id="viewMoreContainer">
        <button class="view-more-btn" id="viewMoreBtn">View More</button>
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', () => {
    let offset = 12; // Start after the initial 12 products
    const limit = 6;
    const viewMoreBtn = document.getElementById('viewMoreBtn');
    const productsGrid = document.getElementById('mayLikeProducts');

    viewMoreBtn.addEventListener('click', () => {
        viewMoreBtn.disabled = true; // Disable button to prevent multiple clicks
        viewMoreBtn.textContent = 'Loading...'; // Show loading text

        fetch('product_fetch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `offset=${offset}`,
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() !== '') {
                productsGrid.insertAdjacentHTML('beforeend', data);
                offset += limit;
                viewMoreBtn.disabled = false; // Re-enable button
                viewMoreBtn.textContent = 'View More'; // Reset button text
            } else {
                // If no more products, hide the button
                viewMoreBtn.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            viewMoreBtn.disabled = false; // Re-enable button on error
            viewMoreBtn.textContent = 'View More';
        });
    });
});



    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    setInterval(nextSlide, 5000); // Change slide every 5 seconds
</script>
 <!-- Include Footer -->
 <?php 
echo realpath('../images/TS-logo.png'); // Temporary debug
include 'includes/footer.php'; 
?>


</body>
</html>

<?php $conn->close(); ?>
