<?php
session_start();
include('db.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];
$imageId = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($imageId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
    exit();
}

// Cek apakah pengguna sudah menyimpan gambar
$checkQuery = $conn->prepare("SELECT * FROM saved_images WHERE user_id = ? AND image_id = ?");
$checkQuery->bind_param("ii", $userId, $imageId);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    // Jika gambar sudah disimpan, hapus dari daftar saved
    $deleteQuery = $conn->prepare("DELETE FROM saved_images WHERE user_id = ? AND image_id = ?");
    $deleteQuery->bind_param("ii", $userId, $imageId);

    if ($deleteQuery->execute()) {
        echo json_encode(['success' => true, 'saved' => false]);
    } else {
        error_log("Delete query error: " . $deleteQuery->error);
        echo json_encode(['success' => false, 'message' => 'Failed to remove saved image']);
    }
} else {
    // Jika belum disimpan, tambahkan ke daftar saved
    $insertQuery = $conn->prepare("INSERT INTO saved_images (user_id, image_id) VALUES (?, ?)");
    $insertQuery->bind_param("ii", $userId, $imageId);
    
    if ($insertQuery->execute()) {
        echo json_encode(['success' => true, 'saved' => true]);
    } else {
        error_log("Insert query error: " . $insertQuery->error);
        echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    }
}
?>
