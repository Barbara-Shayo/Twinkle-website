// Function to add product to the cart
function addToCart(productId, productDetails, productPrice, productImage) {
    const data = {
        id: productId,
        details: productDetails,
        price: productPrice,
        image: productImage
    };

    // Send data to the server using fetch
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data), // Send product details as JSON
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Product added to cart successfully!');
        } else {
            alert('Failed to add product to cart.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
