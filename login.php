<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Validasi input
    if (!empty($username_or_email) && !empty($password)) {
        // Periksa apakah user menggunakan username atau email
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $password_hash);
            $stmt->fetch();
            // Verifikasi password
            if (password_verify($password, $password_hash)) {
                // Set sesi dan redirect
                $_SESSION['user_id'] = $user_id;
                header("Location: index.php");  // Redirect ke index.php
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "No account found with that username or email.";
        }
        $stmt->close();
    } else {
        echo "Please fill all fields.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #333;
    color: #f0f0f0;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

nav {
    background-color: #333;
    padding: 10px 20px;
}

nav .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav h1 {
    margin: 0;
    color: #F9F6EE;
}

.btn-nav {
    color: #F9F6EE;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    background-color: #555;
    transition: background-color 0.3s;
}

.btn-nav:hover {
    background-color: #777;
}

main {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full viewport height */
    padding: 20px;
}

.login-container {
    background-color: #333;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    width: 100%;
    max-width: 400px;
}

.login-container h2 {
    margin-top: 0;
}

input[type="text"], input[type="password"] {
    width: 95%;
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 50px;
    background-color: #F9F6EE;
    color: #333;
}

button {
    width: 100px;
    padding: 10px;
    background-color: #F9F6EE;
    color: #333;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    border: none;
    border-radius: 100px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #333;
    color: #F9F6EE;
}

.error-message {
    color: #E74C3C;
    margin-bottom: 15px;
}

.logo {
     justify-content: center;
    align-items: center;display: flex;
   
}

.register-link {
    color: #007BFF;
    text-decoration: none;
    font-size: 0.9rem;
}

.register-link:hover {
    text-decoration: underline;
}

/* CSS untuk container yang mengatur button dan link */
.button-link-container {
    display: flex;
    justify-content: space-between; /* Membuat button di kanan dan link di kiri */
    align-items: center;
    margin-top: 10px; /* Jarak dari form */
}

.button-link-container a {
    flex: 1;
}

.button-link-container button {
    margin-left: 25px; /* Posisikan tombol di ujung kanan */
    margin-right: ; /* Geser tombol lebih ke kanan dengan margin */
    font-size: 1rem;
}


    </style>
</head>
<body>
<main>
    <div class="login-container">
        <div class="logo">
            <img src="kreartsiku/logo3.png" alt="Logo" style="width: 351px; height: 96px;">
        </div>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="text" name="username_or_email" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <!-- Flex container untuk button di kanan dan link di kiri -->
            <div class="button-link-container">
                <a href="register.php" class="register-link">Don't have an account? Register here</a>
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</main>



</body>
</html>
