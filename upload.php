<?php
session_start(); // Mulai sesi untuk mendapatkan user_id
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $filename = $_FILES['image']['name'];
        $tmpname = $_FILES['image']['tmp_name'];
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($filename);

        // Dapatkan input judul dan tagar
        $judul = $_POST['judul'];
        $tagar = $_POST['tagar'];
        

        // Tambahkan "#" jika belum ada
        if (strpos($tagar, '#') !== 0) {
            $tagar = '#' . $tagar;
        }

        // Pastikan direktori uploads ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($tmpname, $upload_file)) {
            // Simpan gambar, judul, dan tagar ke database
            $stmt = $conn->prepare("INSERT INTO images (filename, user_id, judul, tagar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $filename, $_SESSION['user_id'], $judul, $tagar);
            $stmt->execute();
            $stmt->close();
            
            // Set sesi untuk pesan sukses
            $_SESSION['upload_success'] = "File uploaded successfully!";
        } else {
            // Set sesi untuk pesan error
            $_SESSION['upload_error'] = "Failed to move uploaded file.";
        }
    } else {
        // Set sesi untuk pesan error jika ada masalah dengan file upload
        $_SESSION['upload_error'] = "File upload error: " . $_FILES['image']['error'];
    }

    // Redirect ke halaman upload untuk menampilkan pesan
    header("Location: upload.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fuzzy+Bubbles:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bubblegum+Sans&family=Fuzzy+Bubbles:wght@400;700&family=Permanent+Marker&display=swap" rel="stylesheet">
    
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

        .container {
            background-color: #333;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #fff;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"]  {
            width: 95%;
        padding: 10px;
        margin: 10px 0;
        border: none;
        border-radius: 50px;
        background-color: #F9F6EE;
        color: #333;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 2px 0;
            border: none;
            color: #fff;
            
        }

        button {
        width: 100px;
        margin-left:75%;
        margin-bottom:;
        padding: 10px;
        background-color: #F9F6EE;
        color: #333;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        border: none;
        border-radius: 200px;
        cursor: pointer;
        transition: background-color 0.3s;
        }

        button:hover {
            background-color: #333;
    color: #F9F6EE;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
    background-color: #f0f0f0;
    color: #333;
}
.logo {
    display: flex;
    justify-content: center;
    align-items: center;
}
.popup {
    display: none;
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background-color: #F9F6EE;
    color: #333;
    padding: 10px; /* Kurangi padding */
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    z-index: 1000;
    text-align: center;
    max-width: 300px; /* Batasi lebar maksimal */
    max-height: 150px; /* Batasi tinggi maksimal */
    overflow-y: hide; /* Tambahkan scrollbar jika konten melebihi tinggi maksimal */
    font-size: 14px; /* Perkecil ukuran font */
    box-sizing: border-box;
}

.popup i {
    font-size: 30px; /* Perkecil ukuran ikon */
    margin-bottom: 5px; /* Kurangi margin bawah */
}

.popup button {
    margin-top: 10px;
    background-color: #f0f0f0;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color: #333;
}
    </style>
</head>
<body>
    <div class="popup" id="popup">
        <i id="popup-icon" class=""></i>
        <p id="popup-message"></p>
        <button onclick="closePopup()">OK</button>
    </div>
    <div class="overlay" id="overlay"></div>

    <a href="index.php" class="back-button">
        <i class="fa-solid fa-arrow-left-long"></i>
    </a>
    
    <div class="container">
        <div class="logo">
            <img src="kreartsiku/uplods.png" alt="Logo" style="width: 351px; height: 96px;">
        </div>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="text" name="judul" placeholder="Judul gambar" required>
            <input type="text" name="tagar" placeholder="Tagar " required>
            <input type="file" name="image" required>
            <button type="submit">Upload</button>
        </form>
    </div>

    <script>
        // Fungsi untuk menampilkan popup
        function showPopup(message, type) {
            document.getElementById('popup-message').innerText = message;
            var icon = document.getElementById('popup-icon');

            if (type === 'success') {
                icon.className = 'fa-solid fa-check-circle';
                document.getElementById('popup').classList.add('success');
            } else {
                icon.className = 'fa-solid fa-times-circle';
                document.getElementById('popup').classList.add('error');
            }

            document.getElementById('popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Fungsi untuk menutup popup
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        // Cek jika ada pesan sukses atau error dari sesi PHP
        <?php
        if (isset($_SESSION['upload_success'])) {
            echo 'showPopup("' . $_SESSION['upload_success'] . '", "success");';
            unset($_SESSION['upload_success']); // Hapus setelah ditampilkan
        } elseif (isset($_SESSION['upload_error'])) {
            echo 'showPopup("' . $_SESSION['upload_error'] . '", "error");';
            unset($_SESSION['upload_error']); // Hapus setelah ditampilkan
        }
        ?>
    </script>
</body>
</html>