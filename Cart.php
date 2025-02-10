<?php
session_start();
include_once 'DataBase.php'; 

$isLoggedIn = isset($_SESSION["user_id"]);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateCart'])) {
    
        $newCart = array();

        foreach ($_POST['quantity'] as $productId => $quantity) {
            $productId = (int)$productId; 

            if ($quantity > 0) {
                $newCart[$productId] = (int)$quantity;
            }
        }

        $_SESSION['cart'] = $newCart;

        echo '<script>alert("Cart updated.");</script>';
    } elseif (isset($_POST['buy'])) {

        echo '<script>alert("Transaction finalized.");</script>';

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $quantity = (int)$quantity;

            updateProductQuantity((int)$productId, $quantity);
        }

        $_SESSION['cart'] = array();
    }
}

$cartItems = $_SESSION['cart'];
$products = array();

foreach ($cartItems as $productId => $quantity) {
    $productId = (int)$productId;

    $sql = "SELECT * FROM products WHERE id = $productId";
    $result = $conn->query($sql);

    if (!$result) {
        echo '<p>Error fetching product details: ' . $conn->error . '</p>';
        echo '<p>SQL Query: ' . $sql . '</p>';
    }

    if ($result->num_rows > 0) {
        $productDetails = $result->fetch_assoc();
        $productDetails['quantity'] = $quantity;
        $products[] = $productDetails;
        echo '<script>console.log("Product ID: ' . $productId . '");</script>';
    } else {
        echo '<script>console.log("Product with ID: ' . $productId . 'not found in the database");</script>';
    }
}

//calculeaza pretul final si updateaza cantitatea din depozit
function calculateTotalPrice($products) {
    $total = 0;
    foreach ($products as $product) {
        $total += floatval($product['price']) * floatval($product['quantity']);
    }
    return $total;
}

function updateProductQuantity($productId, $quantity) {
    global $conn;

    $updateQuery = "UPDATE products SET quantity_in_stock = quantity_in_stock - $quantity WHERE id = $productId";
    
    $result = $conn->query($updateQuery);

    if (!$result) {
        echo "Error updating product quantity: " . $conn->error;
    }

}

echo '<script>';
echo 'console.log("Product IDs in Cart: ' . implode(', ', array_keys($cartItems)) . '");';
echo '</script>';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Afaceristu E-Commerce Website</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Shopping Cart</h1>
</header>

<nav>
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="sellProduct.php">Sell a Product</a></li>

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
    <?php if (!empty($products)): ?>
        <form action="cart.php" method="post">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['description']; ?></td>
                        <td>$<?php echo $product['price']; ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $product['id']; ?>]" min="0" value="<?php echo $product['quantity']; ?>">
                        </td>
                        <td>$<?php echo floatval($product['price']) * floatval($product['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <p>Total: $<?php echo calculateTotalPrice($products); ?></p>

            <input type="submit" name="updateCart" value="Update Cart">
            <input type="submit" name="buy" value="Buy">
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</section>
<footer>
    <p>&copy; 2023 Afaceristu E-Commerce Website.</p>
</footer>

</body>
</html>