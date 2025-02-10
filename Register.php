<?php
//Start sesiune
session_start();
include_once 'DataBase.php';

$isLoggedIn = isset($_SESSION["user_id"]);

//Adaugare user in baza de date si logare automata
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql_check_username = "SELECT id FROM users WHERE username = '$username'";
    $result = $conn->query($sql_check_username);

    if (!$result) {
        $message = "Error checking username: " . $conn->error;
    } elseif ($result->num_rows > 0) {
        $message = "Username is already in use. Please choose another username.";
    } else {
        $sql_insert = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashedPassword', '$email')";

        if ($conn->query($sql_insert) === TRUE) {
            $sql_get_user_info = "SELECT id, username, email FROM users WHERE username = '$username'";
            $result_user_info = $conn->query($sql_get_user_info);

            if ($result_user_info && $result_user_info->num_rows > 0) {
                $user_info = $result_user_info->fetch_assoc();

                $_SESSION["user_id"] = $user_info["id"];
                $_SESSION["username"] = $user_info["username"];
                $_SESSION["email"] = $user_info["email"];
            }

            header("Location: home.php");
            exit();
        } else {
            $message = "Error: " . $sql_insert . "<br>" . $conn->error . "<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Afaceristu E-Commerce Website</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Create an Account</h1>
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

<section id="register-form">
    <h2>Register</h2>
<form action="Register.php" method="post">
    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Register</button>
</form>
</section>
<script>
    <?php if (!empty($message)) : ?>
        alert("<?php echo $message; ?>");
    <?php endif; ?>
</script>

<footer>
    <p>&copy; 2023 Afaceristu E-Commerce Website.</p>
</footer>

</body>
</html>
