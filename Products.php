<?php
//Conectare sesiune
session_start();
include_once 'DataBase.php'; 

$isLoggedIn = isset($_SESSION["user_id"]);

//Obtine produse din baza dedate dupa ce se executa un search
//Afisare produse
$products = [];

$sql = "SELECT * FROM products WHERE 1";

$searchInfo = []; 

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $sql .= " AND (name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%')";
    $searchInfo['searchTerm'] = $searchTerm;
}

if (isset($_GET['category']) && $_GET['category'] !== 'all') {
    $category = $_GET['category'];
    $sql .= " AND category = '$category'";
    $searchInfo['category'] = $category;
}

if (isset($_GET['min-price']) && $_GET['min-price'] !== '') {
    $minPrice = $_GET['min-price'];
    $sql .= " AND price >= '$minPrice'";
    $searchInfo['minPrice'] = $minPrice;
}

if (isset($_GET['max-price']) && $_GET['max-price'] !== '') {
    $maxPrice = $_GET['max-price'];
    $sql .= " AND price <= '$maxPrice'";
    $searchInfo['maxPrice'] = $maxPrice;
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

if (empty($products)) {
    $sql = "SELECT * FROM products ORDER BY RAND() LIMIT 12";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
}

$searchInfoJSON = json_encode($searchInfo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Afaceristu E-Commerce Website</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Explore Our Products</h1>
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
            <input type="text" id="search" name="search" placeholder="Enter keywords...">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="all">All Categories</option>
                <option value="electronics">Electronics</option>
                <option value="clothing">Clothing</option>
                <option value="books">Books</option>

            </select>
            <label for="min-price">Min Price:</label>
            <input type="number" id="min-price" name="min-price" placeholder="Min Price">
            <label for="max-price">Max Price:</label>
            <input type="number" id="max-price" name="max-price" placeholder="Max Price">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="product-container">
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
    </div>
</section>

<script>
    console.log('Search Information:', <?php echo $searchInfoJSON; ?>);
</script>

<footer>
    <p>&copy; 2023 Afaceristu E-Commerce Website.</p>
</footer>

</body>
</html>

