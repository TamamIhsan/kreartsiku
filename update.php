
<?php
session_start();
include('db.php');

// Pastikan pengguna sudah login sebelum mengedit gambar
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Periksa jika ID gambar ada
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil detail gambar berdasarkan ID
    $stmt = $conn->prepare("SELECT filename, user_id FROM images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    // Periksa apakah gambar ditemukan dan apakah pengguna adalah pemilik gambar
    if (!$image || $image['user_id'] != $_SESSION['user_id']) {
        echo "Unauthorized access.";
        exit();
    }

    // Proses pembaruan gambar
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $newFilename = $_FILES['image']['name'];
            $tmpname = $_FILES['image']['tmp_name'];
            $upload_dir = 'uploads/';
            $upload_file = $upload_dir . basename($newFilename);

            // Hapus file lama
            unlink('uploads/' . $image['filename']);

            // Pastikan direktori uploads ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($tmpname, $upload_file)) {
                // Perbarui informasi gambar di database
                $stmt = $conn->prepare("UPDATE images SET filename = ? WHERE id = ?");
                $stmt->bind_param("si", $newFilename, $id);
                $stmt->execute();
                $stmt->close();
                header("Location: index.php");
                exit();
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "File upload error: " . $_FILES['image']['error'];
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Image</title>
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

        .upload-container {
            background-color: #333;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            color: #fff;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        img {
            border-radius: 10px;
            margin-bottom: 20px;
            max-width: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        input[type="file"] {
            width: 100%;
            margin: 5px 0;
            border: none;
        }

        button {
            width: 100px;
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

        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            background-color: #333;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #444;
        }

        /* Back button */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #333;
            border-radius: 50%;
            text-decoration: none;
            color: #fff;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }

        .back-button i {
            font-size: 20px;
            text-align: center;
            display: inline-block;
        }

        .back-button:hover {
            background-color: #f0f0f0;
            color: #333;
        }
        .small-image {
    max-width: 150px;
    max-height: 150px;
}

    </style>
</head>
<body>
<a href="index.php" class="back-button">
<i class="fa-solid fa-arrow-left-long"></i></a>
<div class="upload-container">
    <h1>Edit</h1>
    <form action="update.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <img src="uploads/<?php echo $image['filename']; ?>" alt="Current Image" class="small-image">

        <input type="file" name="image" required>
        <button type="submit">Save</button>
    </form>
</div>
</body>
</html>
