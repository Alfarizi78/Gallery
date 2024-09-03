<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-12">
            <?php
include 'koneksi.php';

// Mengambil data pengguna
$query = "SELECT NamaLengkap, Alamat, Email, ProfilePicture FROM user WHERE UserID = ?";
$stmt = $conn->prepare($query);

$id = $_SESSION['user_id']; // Mengambil ID dari session
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nama, $alamat, $email, $profilePicture);
$stmt->fetch();
$stmt->close(); // Tutup statement setelah mengambil data

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePicture'])) {
    // (Kode untuk meng-upload foto tetap sama)
    $targetDir = "uploads/";
    $targetFile = $targetDir . uniqid() . '-' . basename($_FILES['profilePicture']['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['profilePicture']['tmp_name']);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($targetFile)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES['profilePicture']['size'] > 10000000000) { // Limit to 500KB
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
            $query = "UPDATE user SET ProfilePicture = ? WHERE UserID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $targetFile, $id);
            $stmt->execute();
            $stmt->close();

            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteProfilePicture'])) {
    if ($profilePicture && file_exists($profilePicture)) {
        unlink($profilePicture); // Hapus file dari server
    }

    // Set ProfilePicture menjadi null di database
    $query = "UPDATE user SET ProfilePicture = NULL WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7f9, #e0e5ec);
        margin: 0;
        padding: 0;
        height: 100vh;
        overflow: hidden; /* Prevent scrollbars from appearing */
    }
    .profile-container {
        width: 100%;
        max-width: 400px; /* Reduced width */
        background: #ffffff;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        border-radius: 10px; /* Reduced border-radius */
        padding: 15px; /* Reduced padding */
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    h1 {
        color: #333;
        margin-bottom: 15px; /* Reduced margin */
        font-size: 1.5rem; /* Reduced font size */
    }
    .profile-picture {
        position: relative;
        margin-bottom: 15px; /* Reduced margin */
    }
    .profile-picture img {
        width: 100px; /* Reduced size */
        height: 100px; /* Reduced size */
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff; /* Reduced border width */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        z-index: 1; /* Ensure image is on top */
    }
    .profile-picture img:hover {
        transform: scale(1.2); /* Increased scale for pop-out effect */
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.3); /* Increased shadow for pop-out effect */
    }
    .profile-info {
        margin-bottom: 15px; /* Reduced margin */
    }
    .profile-info p {
        font-size: 14px; /* Reduced font size */
        color: #555;
        margin: 8px 0; /* Reduced margin */
        background: #f8f9fa;
        padding: 8px; /* Reduced padding */
        border-radius: 6px; /* Reduced border-radius */
    }
    .upload-form {
        display: block;
    }
    .upload-form input[type="file"] {
        margin-bottom: 8px; /* Reduced margin */
        padding: 8px; /* Reduced padding */
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #fafafa;
    }
    .upload-form input[type="submit"] {
        padding: 8px 16px; /* Reduced padding */
        border: none;
        border-radius: 4px;
        color: #ffffff;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .upload-form input[type="submit"]:hover {
        transform: translateY(-1px); /* Reduced hover effect */
    }
    .upload-form input[type="submit"].upload {
        background-color: #28a745;
    }
    .upload-form input[type="submit"].upload:hover {
        background-color: #218838;
    }
    .upload-form input[type="submit"].delete {
        background-color: #dc3545;
    }
    .upload-form input[type="submit"].delete:hover {
        background-color: #c82333;
    }


    </style>
</head>
<body>

<div class="profile-container">
    <h1>Profil Pengguna</h1>
    <div class="profile-picture">
        <img src="<?php echo $profilePicture ? htmlspecialchars($profilePicture) : 'default-profile.png'; ?>" alt="Foto Profil">
    </div>
    <div class="profile-info">
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($nama); ?></p>
        <p><strong>Alamat:</strong> <?php echo htmlspecialchars($alamat); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    </div>
    <div class="upload-form">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="profilePicture" accept="image/*" required>
            <input type="submit" value="Unggah Foto Profil" class="upload">
        </form>
        <form action="" method="post">
            <input type="submit" name="deleteProfilePicture" value="Hapus Foto Profil" class="delete">
        </form>
    </div>
</div>

</body>
</html>
