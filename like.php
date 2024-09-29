<?php
session_start();
include('db.php');

if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $image_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Periksa apakah user sudah like gambar ini
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
    $stmt->bind_param("ii", $user_id, $image_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Jika belum, tambahkan like baru
        $stmt = $conn->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $image_id);
        $stmt->execute();
        $response = ['success' => true, 'liked' => true];
    } else {
        // Jika sudah, hapus like
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
        $stmt->bind_param("ii", $user_id, $image_id);
        $stmt->execute();
        $response = ['success' => true, 'liked' => false];
    }

    echo json_encode($response);
}
?>
