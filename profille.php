<?php
session_start();

// Cek jika ada pesan sukses atau error di session
if (isset($_SESSION['upload_success'])) {
    $message = $_SESSION['upload_success'];
    $status = 'success'; // Tambahkan status sukses
    unset($_SESSION['upload_success']); // Hapus pesan setelah ditampilkan
} elseif (isset($_SESSION['upload_error'])) {
    $message = $_SESSION['upload_error'];
    $status = 'error'; // Tambahkan status error
    unset($_SESSION['upload_error']); // Hapus pesan setelah ditampilkan
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fuzzy+Bubbles:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/regular.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bubblegum+Sans&family=Fuzzy+Bubbles:wght@400;700&family=Permanent+Marker&display=swap" rel="stylesheet">
    <title>Edit Image</title>
    <style>
        
        body {
    background-color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: "Archivo Black", sans-serif;
    font-weight: 400;
    font-style: normal;
}
.popup {
            display: none; /* Awalnya tidak tampil */
            position: fixed;
            left: 50%;
            top: 20%;
            transform: translate(-50%, -50%);
            background-color: #F9F6EE;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            text-align: center;
        }
        .popup i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .popup.success i {
            color: green;
        }
        .popup.error i {
            color: red;
        }
        

        .popup button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #333;
        }

        .popup button:hover {
            background-color: #ccc;
        }

        /* Background overlay */
        .overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }

        .upload-container {
            background-color: #333;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
        }

        .upload-container h2 {
            margin-top: 0;
            color: #F9F6EE;
        }

        input[type="text"], input[type="email"] {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 50px;
            background-color: #F9F6EE;
            color: #333;
        }
        input[type="file"] {
            
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
            align-items: center;
            display: flex;
        }

        .register-link {
            color: #007BFF;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        /* CSS for button and link container */
        .button-link-container {
            display: flex;
            justify-content: space-between; /* Position button on the right and link on the left */
            align-items: center;
            margin-top: 10px; /* Space from the form */
        }

        .button-link-container a {
            flex: 1;
        }

        .button-link-container button {
            margin-left: 300px;
            font-size: 1rem;
        }

h2 {
    color: #fff;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

label {
    color: #fff;
    margin-bottom: 10px;
}

input[type="file"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 10px;
}

button {
    width: 100px;
    padding: 10px;
    background-color: #F9F6EE;
    color: #333;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    border: none;
    border-radius: 200px;
    cursor
}

button:hover {
    background-color: #333;
            color: #fff;
}

.back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        width: 40px; /* Sesuaikan ukuran lingkaran */
        height: 40px; /* Sesuaikan ukuran lingkaran */
        display: flex;
        justify-content: center; /* Pusatkan ikon secara horizontal */
        align-items: center; /* Pusatkan ikon secara vertikal */
        background-color: #333;
        border-radius: 50%; /* Membuat tombol berbentuk lingkaran */
        text-decoration: none;
        color: #fff;
        font-size: 16px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s;
    }

.back-button i {
    font-size: 20px; /* Sesuaikan ukuran ikon */
    text-align: center;
    display: inline-block; /* Pastikan ikon adalah inline-block */
    
}

.back-button:hover {
    background-color: #333;
            color: #fff;
}
    </style>
</head>
<body>
<div class="overlay" id="overlay"></div>
<div class="popup" id="popup">
    <i id="popup-icon" class=""></i>
    <p id="popup-message"></p>
    <button onclick="closePopup()">OK</button>
</div>

<a href="profilee.php" class="back-button">
<i class="fa-solid fa-arrow-left-long"></i></a>

<div class="upload-container">
        <h2>Edit Profile</h2>
        <form action="upload_profile_image.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email">
    <input type="file" name="profile_image" accept="image/*"> 
    <div class="button-link-container">
        <button type="submit">Save</button>
    </div>
</form>

    </div>


<script>
// Function to display the pop-up with icon and message
function showPopup(message, type) {
    document.getElementById('popup-message').innerText = message;

    // Set icon based on type (success or error)
    var icon = document.getElementById('popup-icon');
    if (type === 'success') {
    icon.className = 'fa-solid fa-circle-check'; // Ikon regular untuk centang
    document.getElementById('popup').classList.add('success');
    document.getElementById('popup').classList.remove('error');
} else if (type === 'error') {
    icon.className = 'fa-solid fa-circle-xmark'; // Ikon solid untuk X
    document.getElementById('popup').classList.add('error');
    document.getElementById('popup').classList.remove('success');
}


    document.getElementById('popup').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

// Function to close the pop-up
function closePopup() {
    document.getElementById('popup').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

// Check if there's a message from the PHP session
<?php if (isset($message)): ?>
    showPopup("<?php echo $message; ?>", '<?php echo $status; ?>'); // Gunakan status dari PHP
<?php endif; ?>

</script>

</body>
</html>