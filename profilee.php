<?php
session_start();
include('db.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Ambil informasi pengguna
$query = "SELECT * FROM users WHERE id = '$userId'";
$userResult = $conn->query($query);
$user = $userResult->fetch_assoc();

// Ambil gambar yang diupload oleh pengguna
$uploadsQuery = "SELECT * FROM images WHERE user_id = '$userId'";
$uploadsResult = $conn->query($uploadsQuery);

// Ambil gambar yang disimpan oleh pengguna
$savedQuery = "SELECT images.* FROM saved_images 
               JOIN images ON saved_images.image_id = images.id 
               WHERE saved_images.user_id = '$userId'";
$savedResult = $conn->query($savedQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/stylepro.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #333;
}

.container {
    max-width: 1050px;
    margin: 20px auto;
    padding: 20px;
    background-color: #333;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.profile-title {
    text-align: center;
    color: #fff;
}

.profile-info {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.profile-pic {
    flex: 1;
    text-align: center;
}

.profile-pic img {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.user-details {
    flex: 2;
    padding-left: 20px;
    color: #fff;
}

.user-details h2 {
    margin-bottom: 10px;
}

.user-details p {
    margin: 5px 0;
}

h2 {
    margin-top: 40px;
    color: #fff;
}

.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    grid-gap: 10px;
    justify-content: center;
}

.gallery-item {
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s;
}

.gallery-item img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s;
}

.gallery-item img:hover {
    transform: scale(1.05);
}


.gallery-item img:hover {
    transform: scale(1.05);
}

.back-button {
    position: absolute;
    top: 40px;
    left: 100px;
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
    box-shadow:none;
}

.back-button i {
    font-size: 20px; /* Sesuaikan ukuran ikon */
    text-align: center;
    display: inline-block; /* Pastikan ikon adalah inline-block */
    
}

.back-button:hover {
    color: #fff;
    background-color: #333;
}

.user-icon {
    position: relative; /* Membuat posisi relatif untuk dropdown */
    display: inline-block; /* Menyusun elemen secara inline */
}

.profile-dropdown {
    display: none; /* Sembunyikan dropdown secara default */
    position: absolute;
    top: 40px; /* Jarak dari bagian atas */
    right: 0;
    background-color: #fff; /* Latar belakang putih */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    z-index: 10; /* Pastikan dropdown di atas elemen lain */
    padding:10px;
    margin-right:20px;
}

.profile-dropdown a {
    display: inline-block; /* Buat tautan tampil dalam satu baris secara horizontal */
    padding: 10px; /* Padding untuk tautan */
    text-decoration: none; /* Hapus garis bawah pada tautan */
    color: #333; /* Warna teks */
    font-size: 15px;
    white-space: nowrap; /* Pastikan teks tidak turun ke baris baru */
}

.profile-dropdown a:hover {
    background-color: #f0f0f0; /* Warna latar belakang saat hover */
    border-radius: 5px;
}

        
        .gallery {
            display: flex;
            flex-wrap: wrap;
        }

        .gallery-item {
            margin: 5px;
        }

        .gallery img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }




.button-container {
    display: flex;
    justify-content: center; /* Posisikan tombol di tengah */
    gap: 10px; /* Jarak antar tombol */
    margin-bottom: 50px;
    margin-top:100px;
}

.toggle-button i {
    font-size: 18px; /* Sesuaikan ukuran ikon */
    margin-right:10px;
}

.toggle-button {
    display: flex;
    align-items: center; /* Rapatkan ikon secara vertikal */
    justify-content: center; /* Pusatkan ikon di dalam tombol */
    padding: 12px; /* Sesuaikan padding untuk lebih center */
    width: 100px; /* Atur lebar tombol agar konsisten */
    height: 50px; /* Tinggi tombol sama untuk proporsi */
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    color: #333;
    border: none;
    border-radius: 50px; /* Membuat tombol bulat */
    cursor: pointer;
    transition: background-color 0.3s;
}

.toggle-button:hover {
    background-color: #333;
    color: #fff;
}

.hidden {
    display: none;
}

    </style>
</head>
<body>


<div class="container">
 <a href="index.php" class="back-button">
<i class="fas fa-arrow-left"></i></a>
    <div class="user-icon">
        <a href="#" onclick="toggleProfileMenu(event)">
            <i class="fas fa-ellipsis-h"></i> <!-- Ikon titik tiga -->
        </a>
        <div class="profile-dropdown" id="profile-dropdown">
            <a href="profille.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h1 class="profile-title"><?= htmlspecialchars($user['username']) ?>'s Profile</h1>

    <div class="profile-info">
        <div class="profile-pic">
            <img src="<?= htmlspecialchars($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'kreartsiku/iconpro.png'; ?>" alt="Profile Picture">
        </div>
        <div class="user-details">
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        </div>
    </div>

    <div class="button-container">
    <button class="toggle-button" onclick="showSection('uploads')">
        <i class="fa-solid fa-upload"></i> <!-- Ikon upload -->
        <p>upload</p>
    </button>
    <button class="toggle-button" onclick="showSection('saved')">
        <i class="fa-solid fa-bookmark"></i> <!-- Ikon bookmark -->
        <p>save</p>
    </button>
</div>


    <!-- Section for uploaded photos -->
    <div id="uploads-section" class="gallery">
        <?php while ($row = $uploadsResult->fetch_assoc()): ?>
            <div class="gallery-item">
                <img src="uploads/<?= htmlspecialchars($row['filename']) ?>" alt="Uploaded Image">
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Section for saved photos (hidden by default) -->
    <div id="saved-section" class="gallery hidden">
        <?php while ($row = $savedResult->fetch_assoc()): ?>
            <div class="gallery-item">
                <img src="uploads/<?= htmlspecialchars($row['filename']) ?>" alt="Saved Image">
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    function toggleProfileMenu(event) {
        event.stopPropagation(); // Mencegah klik dari menyebar
        const dropdown = document.getElementById('profile-dropdown');
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
    }

    // Tutup dropdown jika pengguna mengklik di luar dropdown
    window.onclick = function(event) {
        const dropdown = document.getElementById('profile-dropdown');
        if (dropdown.style.display === 'block' && !event.target.closest('.user-icon')) {
            dropdown.style.display = 'none';
        }
    }

    // Function to toggle between uploads and saved photos
    function showSection(section) {
        const uploadsSection = document.getElementById('uploads-section');
        const savedSection = document.getElementById('saved-section');

        if (section === 'uploads') {
            uploadsSection.classList.remove('hidden');
            savedSection.classList.add('hidden');
        } else if (section === 'saved') {
            uploadsSection.classList.add('hidden');
            savedSection.classList.remove('hidden');
        }
    }
</script>

</body>
</html>