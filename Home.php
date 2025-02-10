<?php
//Conectare sesiune
session_start();
include_once 'DataBase.php'; 

$isLoggedIn = isset($_SESSION["user_id"]);

//Obtinere produse din baza de date
$products = [];

if (isset($_GET['term'])) {
    $searchTerm = $_GET['term'];

    $sql = "SELECT * FROM products WHERE name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    $sql = "SELECT * FROM products ORDER BY RAND() LIMIT 2";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afaceristu E-Commerce Website</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Welcome to Afaceristu E-Commerce Website</h1>
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
    <div id="search-bar">
        <form id="search-form" method="get">
            <label for="search">Search:</label>
            <input type="text" id="search" name="term" placeholder="Enter keywords...">
            <button type="submit">Search</button>
        </form>
    </div>

    <h2>Featured Products</h2>
<?php
if (!empty($products)) {
    foreach ($products as $product) {
        echo '<div class="product">';
        echo '<h3><a href="ProductDetails.php?id=' . $product["id"] . '">' . $product["name"] . '</a></h3>';
        echo '<p>' . $product["description"] . '</p>';
        echo '<p>Price: $' . $product["price"] . '</p>';
        echo '</div>';
    }
} else {
    echo '<p>No products found.</p>';
}
?>
</section>

<footer>
    <p>&copy; 2023 Afaceristu E-Commerce Website.</p>
</footer>

</body>
</html>