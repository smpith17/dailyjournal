<?php
// Periksa apakah session sudah dimulai, jika belum maka panggil session_start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<div>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <form action="profile_data.php" method="POST" enctype="multipart/form-data">
            <!-- Ganti Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Ganti Password</label>
                <input type="password" class="form-control" name="password" placeholder="Tuliskan Password Baru Jika Ingin Mengganti Password Saja">
            </div>

            <!-- Ganti Foto Profil -->
            <div class="mb-3">
                <label for="photo" class="form-label">Ganti Foto Profile</label>
                <input type="file" class="form-control" name="photo">
            </div>

            <!-- Foto Profil Saat Ini -->
            <div>
                <label for="photo" class="form-label">Foto Profile Saat Ini</label><br>
                <?php 
                $profile_photo_path = "img/$username.jpg";
                if (file_exists($profile_photo_path)) {
                    // Tambahkan timestamp untuk menghindari cache
                    echo "<img src=\"$profile_photo_path?t=" . time() . "\" alt=\"Profile Photo\" width=\"100\">";
                } else {
                    echo "<img src=\"img/default.jpg\" alt=\"Default Profile Photo\" width=\"100\">"; // Default foto jika tidak ada
                }
                ?>
            </div>

            </br>
            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </body>
</div>
