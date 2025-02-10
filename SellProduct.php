<?php
//Start sesiune
session_start();
include_once 'DataBase.php'; 

$isLoggedIn = isset($_SESSION["user_id"]);

//Adaugare produse in baza de date
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var($_POST['productName'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES['image']['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES['image']['size'] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
    $sql_insert = "INSERT INTO products (name, description, price, quantity_in_stock, images, category) 
                   VALUES ('$name', '$description', $price, $quantity, '$targetFile', '$category')";

            if ($conn->query($sql_insert) === TRUE) {
                echo '<script>alert("Product added successfully");</script>';
            } else {
                echo "Error: " . $sql_insert . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell a Product - Afaceristu E-Commerce Website</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Sell a Product</h1>
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
    <?php
    if ($isLoggedIn) {
        echo '<form action="" method="post" enctype="multipart/form-data">';
        echo '<label for="productName">Product Name:</label>';
        echo '<input type="text" id="productName" name="productName" required>';
        echo '<br>';
        echo '<label for="description">Description:</label>';
        echo '<textarea id="description" name="description" required></textarea>';
        echo '<br>';
        echo '<label for="price">Price:</label>';
        echo '<input type="number" id="price" name="price" required>';
        echo '<br>';
        echo '<label for="quantity">Quantity:</label>';
        echo '<input type="number" id="quantity" name="quantity" required>';
        echo '<br>';
        echo '<label for="category">Category:</label>';
        echo '<select id="category" name="category" required>
                <option value="all">All Categories</option>
                <option value="electronics">Electronics</option>
                <option value="clothing">Clothing</option>
                <option value="books">Books</option>
                <!-- Add more categories as needed -->
            </select>';
        echo '<br>';
        echo '<label for="image">Product Image:</label>';
        echo '<input type="file" id="image" name="image" accept="image/*" required>';
        echo '<br>';
        echo '<button type="submit">Sell Product</button>';
        echo '</form>';
    } else {
        echo '<p>Please log in to sell a product.</p>';
        echo '<a href="login.php"><button>Login</button></a>';
    }
    ?>
</section>

<footer>
    <p>&copy; 2023 Afaceristu E-Commerce Website.</p>
</footer>

</body>
</html>
