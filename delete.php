// delete.php
<?php
session_start();
include('db.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT filename, user_id FROM images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Cek jika gambar ditemukan dan apakah pengguna adalah pemilik gambar
    if ($row && $row['user_id'] == $_SESSION['user_id']) {
        $filename = $row['filename'];
        unlink('uploads/' . $filename);
        
        $stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    
    $stmt->close();
}
header("Location: index.php");
?>
