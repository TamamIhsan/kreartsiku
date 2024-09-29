<?php
session_start();
include('db.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepared statement untuk menghindari SQL Injection
$stmt = $conn->prepare("
    SELECT images.id, images.filename, images.user_id, images.judul, images.tagar, users.username, COUNT(likes.id) AS like_count 
    FROM images 
    LEFT JOIN users ON images.user_id = users.id 
    LEFT JOIN likes ON images.id = likes.image_id 
    WHERE images.judul LIKE ? OR images.tagar LIKE ? OR users.username LIKE ?
    GROUP BY images.id
");


// Menambahkan wildcard untuk pencarian
$searchParam = '%' . $search . '%';
$stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);

$stmt->execute();
$result = $stmt->get_result();

// Ambil gambar profil pengguna
$queryProfile = "SELECT profile_image FROM users WHERE id = ?";
$stmtProfile = $conn->prepare($queryProfile);
$stmtProfile->bind_param('i', $userId);
$stmtProfile->execute();
$resultProfile = $stmtProfile->get_result();
$user = $resultProfile->fetch_assoc();
$profileImage = !empty($user['profile_image']) ? $user['profile_image'] : "kreartsiku/iconpro.png"; // Gambar default jika belum ada

// Kueri untuk mendapatkan ID gambar yang telah disukai oleh pengguna
$userLikesQuery = "SELECT image_id FROM likes WHERE user_id = ?";
$stmtLikes = $conn->prepare($userLikesQuery);
$stmtLikes->bind_param('i', $userId);
$stmtLikes->execute();
$userLikesResult = $stmtLikes->get_result();
$likes = $userLikesResult->fetch_all(MYSQLI_ASSOC); //array

// Kueri untuk mendapatkan ID gambar yang telah disimpan oleh pengguna
$userSavedQuery = "SELECT image_id FROM saved_images WHERE user_id = ?";
$stmtSaved = $conn->prepare($userSavedQuery);
$stmtSaved->bind_param('i', $userId);
$stmtSaved->execute();
$userSavedResult = $stmtSaved->get_result();
$savedImages = $userSavedResult->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$stmtProfile->close();
$stmtLikes->close();
$stmtSaved->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KreArtsiku</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/scripts.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Fuzzy+Bubbles:wght@400;700&family=Permanent+Marker&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bubblegum+Sans&family=Fuzzy+Bubbles:wght@400;700&family=Permanent+Marker&display=swap" rel="stylesheet">
    <style>
        nav h1 {
        margin: 0; /* Menghapus margin default */
        margin-left: 10px; /* Jarak dengan logo */
        font-size: 24px;
        font-family: "Archivo Black", sans-serif;
        font-weight: 400;
        font-style: normal;
        }
        
        .search-form {
        display: flex;
        align-items: center;
        border: 1px solid #ccc; /* Warna border */
        border-radius: 20px; /* Membuat input membulat */
        overflow: hidden;
        transition: all 0.3s ease;
        background-color: #fff; /* Warna background input */
        margin-left: 60%; /* Memindahkan form ke kanan */
        }

        /* Input pencarian */
        .search-form input[type="text"] {
            border: none;
            padding: 10px;
            outline: none; /* Menghilangkan outline saat focus */
            width: 200px; /* Ukuran input */
            font-size: 16px;
            background-color: transparent;
            margin-left: 5%;
        }

        /* Tombol pencarian */
        .search-form button {
            border: none;
            background-color: #fff; /* Warna tombol */
            padding: 10px 16px;
            cursor: pointer;
            color: white;
            transition: background-color 0.3s;
        }

        /* Ikon search di dalam tombol */
        .search-form button i {
            font-size: 18px;
            color:#333;
        }

        /* Hover effect pada tombol */
        .search-form button:hover {
            background-color: #fff; /* Warna saat di hover */
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
    <nav>
    <div class="container">
    <a href="index.php">
    <img src="kreartsiku/logo2.png" alt="Logo" style="width: 60px; height: 60px;">
</a>
        <!-- <h1>KreArtsiku</h1> -->
        <a href="upload.php" class="btn-create">
            <i class="fi fi-tr-add"></i>
        </a>

        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="search" placeholder="Search" required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <div class="user-icon">
            <a href="#" onclick="toggleProfileMenu(event)">
                <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="profile-image">
            </a>
            <div class="profile-dropdown" id="profile-dropdown">
                <a href="profilee.php">Lihat Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>

    </nav>

    <!-- Main Content -->
    <main>
    <div id="gallery">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="image-item">
        <img src="uploads/<?= htmlspecialchars($row['filename']) ?>" alt="Image" 
    onclick="openPopup('<?= htmlspecialchars($row['filename']) ?>', 
                        '<?= htmlspecialchars($row['username']) ?>', 
                        <?= $row['like_count'] ?>, 
                        <?= in_array($row['id'], array_column($likes, 'image_id')) ? 'true' : 'false' ?>, 
                        <?= $row['id'] ?>, 
                        '<?= htmlspecialchars($row['judul']) ?>', 
                        '<?= htmlspecialchars($row['tagar']) ?>')">


    <!-- Ellipsis icon -->
    <div class="ellipsis-menu" onclick="toggleMenu(<?= $row['id'] ?>)">
        <i class="fas fa-ellipsis-h"></i>
    </div>

    <!-- Popup menu -->
    <div class="menu-popup" id="menu-popup-<?= $row['id'] ?>">
    <button onclick="saveImage(<?= $row['id'] ?>)">
        <?= in_array($row['id'], array_column($savedImages, 'image_id')) ? 'Saved' : 'Save' ?>
    </button>
    <?php if ($row['user_id'] == $userId): ?>
        <button onclick="location.href='update.php?id=<?= $row['id'] ?>'">Edit</button>
        <button onclick="deleteImage(<?= $row['id'] ?>)">Delete</button>
        
    <?php endif; ?>
</div>


</div>

    <?php endwhile; ?> <!-- This should now match the opening while -->
</div>

    </main>


<!-- Popup for images -->
<div id="popup" class="popup">
    <span class="close" onclick="closePopup()">&times;</span>
    <div class="popup-content">
        <img id="popup-img" src="" alt="Popup Image">
        
        <div class="popup-details">
            <!-- Buttons for like and save -->
            <div class="popup-actions">
                <button class="btn-like" data-image-id="<?= $row['id'] ?>" onclick="handleLikeClick(<?= $row['id'] ?>)">
                    <i id="popup-like-icon" class="fas fa-heart"></i>
                    <span id="popup-like-count">0</span> Like
                </button>
                <button class="btn-save">Save</button>
            </div>
            
            <!-- User info, title, tags -->
            <div class="popup-user-info">
                <p id="popup-username">Uploaded by: Username</p>
                <p id="popup-title">Title: </p>
                <p id="popup-tags">Tags: </p>
            </div>
            
    </div>
</div>



    <div id="toast" class="toast-notification"></div>
    <script src="assets/js/scr.js"></script>
</body>
</html>
