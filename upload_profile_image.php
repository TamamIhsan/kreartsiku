<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $updateSuccess = false; // Flag to check if update is successful

    // Process username and email updates
    if (isset($_POST['username']) && isset($_POST['email'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        
        $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = '$userId'";
        if ($conn->query($query)) {
            $updateSuccess = true; // Set flag if update is successful
        } else {
            $_SESSION['upload_error'] = "Error updating profile information: " . $conn->error;
        }
    }

    // Process profile image upload (only if a file is selected)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] != '') {
        $targetDir = "uploads/profiles/";
        $targetFile = $targetDir . basename($_FILES["profile_image"]["name"]);
        
        // Validate the image file
        if ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
                    // Update database with image path
                    $query = "UPDATE users SET profile_image = '$targetFile' WHERE id = '$userId'";
                    if ($conn->query($query)) {
                        $_SESSION['upload_success'] = "Successfully updated profile.";
                    } else {
                        $_SESSION['upload_error'] = "Error updating profile image: " . $conn->error;
                    }
                } else {
                    $_SESSION['upload_error'] = "Sorry, there was an error uploading your file.";
                }
            } else {
                $_SESSION['upload_error'] = "File is not an image.";
            }
        }
    }

    // Set success message if username/email updated but no image uploaded
    if ($updateSuccess && !isset($_SESSION['upload_success'])) {
        $_SESSION['upload_success'] = "Successfully updated profile.";
    }

    header("Location: profille.php"); // Corrected to profile.php
    exit();
}
?>
