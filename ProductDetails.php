<?php
//Conectare sesiune
session_start();
include_once 'DataBase.php'; 

$isLoggedIn = isset($_SESSION["user_id"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Afaceristu E-Commerce Website</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Product Details</h1>
</header>

<nav>
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="SellProduct.php">Sell a Product</a></li>
        <div class="icon">
            <a href="Cart.php">
                    <img src="Pictures/Icons/carts.png" alt="Profile Icon">
            </a>
        </div>
        

        <?php
        if ($isLoggedIn) {
            echo '<div class="icon">
                      <a href="UserInfo.php">
                          <img src="Pictures/Icons/profile.png" alt="Profile Icon">
                      </a>
                  </div>';
        } else {
            echo '<li><a href="register.php">Register</a></li>
                  <li><a href="login.php">Login</a></li>';
        }
        ?>
    </ul>
</nav>

<section>
    <div class="product-details">
        <?php
        //Prezinta produsul si adaugal in cart
        $productId = isset($_GET['id']) ? $_GET['id'] : null;

        if (!$productId) {
            echo '<p>Invalid product ID.</p>';
        } else {
            echo '<script>console.log("Product ID: ' . $productId . '");</script>';

            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();

                echo '<h2>' . $product["name"] . '</h2>';
                echo '<p>Description: ' . $product["description"] . '</p>';
                echo '<p>Price: $' . $product["price"] . '</p>';

                echo '<img src="' . $product["images"] . '" alt="Product Image">';

                echo '<form action="" method="post">';
                echo '<input type="hidden" name="productId" value="' . $productId . '">';

                if ($isLoggedIn) {
                    echo '<button type="submit" name="addToCart">Add to Cart</button>';
                } else {
                    echo '<p>Please <a href="login.php">login</a> to add this product to your cart.</p>';
                }

                echo '</form>';

                } else {
                    echo '<p>Product not found.</p>';
                }

            $stmt->close(); 
            }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addToCart'])) {
            $cartProduct = array(
                $productId => array(
                    'quantity' => 1,
                ),
            );

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }

            $_SESSION['cart'] += $cartProduct;
                        echo '<script>alert("Product added to cart.");</script>';
        }
        ?>
    </div>
</section>

<footer>
    <p>&copy; 2023 Afaceristu E-Commerce Website.</p>
</footer>

</body>
</html>
