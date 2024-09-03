<?php
if (isset($_POST['delete_image'])) {
    $foto_id = $_POST['foto_id'];

    // Ambil informasi file gambar sebelum menghapus dari database
    $result = mysqli_query($conn, "SELECT NamaFile FROM foto WHERE FotoID='$foto_id'");
    if ($fileData = mysqli_fetch_array($result)) {
        $filePath = "uploads/" . $fileData['NamaFile'];

        // Hapus data dari tabel foto
        if (mysqli_query($conn, "DELETE FROM foto WHERE FotoID='$foto_id'")) {
            // Hapus semua likes terkait gambar tersebut
            mysqli_query($conn, "DELETE FROM likefoto WHERE FotoID='$foto_id'");

            // Hapus semua komentar terkait gambar tersebut
            mysqli_query($conn, "DELETE FROM komentar WHERE FotoID='$foto_id'");

            // Hapus file gambar dari folder uploads
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Redirect user after deletion
            header("Location: ?url=home");
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Gambar tidak ditemukan di database.";
    }
}

if (isset($_POST['delete_image'])) {
    $foto_id = $_POST['foto_id'];
    $result = mysqli_query($conn, "SELECT NamaFile FROM foto WHERE FotoID='$foto_id'");
    if ($fileData = mysqli_fetch_array($result)) {
        $filePath = "uploads/" . $fileData['NamaFile'];
        if (mysqli_query($conn, "DELETE FROM foto WHERE FotoID='$foto_id'")) {
            mysqli_query($conn, "DELETE FROM likefoto WHERE FotoID='$foto_id'");
            mysqli_query($conn, "DELETE FROM komentar WHERE FotoID='$foto_id'");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            header("Location: ?url=home");
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Gambar tidak ditemukan di database.";
    }
}

if (isset($_POST['edit_title'])) {
    $foto_id = $_POST['foto_id'];
    $new_title = mysqli_real_escape_string($conn, $_POST['new_title']);
    $update_query = "UPDATE foto SET JudulFoto='$new_title' WHERE FotoID='$foto_id'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: ?url=home");
    } else {
        echo "Error updating title: " . mysqli_error($conn);
    }
}




?>

<div class="container my-4 p-5 bg-hero rounded">
    <div class="py-5 text-white">
        <p class="display-5 fw-bold">Galeri Foto</p>
        <p class="fs-4 col-md-8">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Doloremque cupiditate quisquam corrupti beatae eaque vel dicta odio maiores similique aperiam?</p>
    </div>
</div>
<div class="container">
    <div class="row">
        <?php 
        $tampil=mysqli_query($conn, "SELECT * FROM foto INNER JOIN user ON foto.UserID=user.UserID");
         foreach ($tampil as $tampils): ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card">
                    <img src="uploads/<?= htmlspecialchars($tampils['NamaFile']) ?>" class="object-fit-cover" style="aspect-ratio: 16/9;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($tampils['JudulFoto']) ?></h5>
                        <p class="card-text text-muted">by: <?= htmlspecialchars($tampils['Username']) ?></p>
                        <a href="?url=detail&&id=<?= htmlspecialchars($tampils['FotoID']) ?>" class="btn btn-success">Detail</a>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $tampils['UserID']): ?>
                            <form action="?url=home" method="post" class="d-inline mb-2">
                                <input type="hidden" name="foto_id" value="<?= htmlspecialchars($tampils['FotoID']) ?>">
                                <input type="text" name="new_title" class="form-control mb-2" value="<?= htmlspecialchars($tampils['JudulFoto']) ?>">
                                <button type="submit" name="edit_title" class="btn btn-primary btn-sm">Edit Title</button>
                            </form>
                            <form action="?url=home" method="post" class="d-inline">
                                <input type="hidden" name="foto_id" value="<?= htmlspecialchars($tampils['FotoID']) ?>">
                                <button type="submit" name="delete_image" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
    </div>
</div>