<?php 
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi input
    if (!empty($username) && !empty($email) && !empty($password)) {
        // Periksa apakah username atau email sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Username atau email sudah ada
            echo "Username or email already exists. Please choose another.";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Simpan pengguna ke database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hash);
            if ($stmt->execute()) {
                echo "Registration successful.";
            } else {
                echo "Registration failed.";
            }
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
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bubblegum+Sans&family=Fuzzy+Bubbles:wght@400;700&family=Permanent+Marker&display=swap" rel="stylesheet">
    <style>
        /* Reset default browser styles */
body, h1, form, input, button {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #333;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

h1 {
    margin-bottom: 20px;
    font-size:;
    color: #fff;
    font-family: "Archivo Black", sans-serif;
    text-align: center;
}



input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 50px;
    background-color: #F9F6EE;
    color: #333;
}

input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
    border-color: #007BFF;
    outline: none;
}

button {
        display: inline-flex;
        justify-content: center; /* Memusatkan teks secara horizontal */
        align-items: center;     /* Memusatkan teks secara vertikal */
        width: 100px;
        margin-top: 10px;
        padding: 10px;
        margin-left :  54px;
        padding: 10px;
        background-color: #F9F6EE;
        color: #333;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        border: none;
        border-radius: 100px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-size: 1rem;
        text-align :center;
        }

        button:hover {
            background-color: #333;
            color: #F9F6EE;
        }

a {
    display: inline-flex;
    padding:2px;
    margin-top: 1px;
    color: #007BFF;
    text-decoration: none;
    font-size: 0.9rem;
}

a:hover {
    text-decoration: underline;
}

.container {
    background-color: #333;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    width: 100%;
    max-width: 400px;
}

        form {
            display: flex;
            flex-direction: column;
        }
        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            text-align: center;
        }
    </style>

</head>
<body>

<form action="register.php" method="post">
    <div class="container">
    <div class="logo">
            <img src="kreartsiku/regis.png" alt="Logo" style="width: 351px; height: 96px;">
        </div>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <a href="login.php">Already have an account? Login here</a>
        <button type="submit">Register</button>
    </div>
</form>

<!-- Notification -->
<div id="notificationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p id="notificationMessage"><?php echo $message; ?></p>
    </div>
</div>

<script>
    // Show modal if there's a message
    window.onload = function() {
        var message = "<?php echo $message; ?>";
        if (message) {
            document.getElementById('notificationModal').style.display = "block";
            document.getElementById('notificationMessage').innerText = message;
        }
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('notificationModal').style.display = "none";
    }
</script>

</body>
</html>
