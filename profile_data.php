<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$password = '';
$photo = '';

// Jika ada perubahan password
if (!empty($_POST['password'])) {
    // Enkripsi password baru dengan MD5
    $password = md5($_POST['password']);
}

// Jika ada perubahan foto profil
if (!empty($_FILES['photo']['name'])) {
    // Cek ekstensi file gambar
    $photo_name = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_path = 'img/' . $username . '.jpg';  // Simpan dengan nama username

    // Validasi ekstensi gambar
    $valid_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));

    if (in_array($file_extension, $valid_extensions)) {
        // Pindahkan file gambar ke folder img
        if (move_uploaded_file($photo_tmp, $photo_path)) {
            $photo = $username . '.jpg';  // Nama file yang digunakan untuk gambar
        } else {
            echo "Gagal mengupload gambar. Pastikan folder img/ memiliki izin yang benar.";
            exit();
        }
    } else {
        echo "Hanya gambar dengan ekstensi JPG, JPEG, dan PNG yang diizinkan.";
        exit();
    }
}

// Update data ke database jika ada perubahan
if ($password || $photo) {
    $update_query = "UPDATE user SET ";

    $params = [];
    $values = [];

    // Update password jika ada perubahan
    if ($password) {
        $params[] = "password = ?";  // Kolom password di tabel 'user'
        $values[] = $password;
    }

    // Update foto profil jika ada perubahan
    if ($photo) {
        $params[] = "foto = ?";  // Kolom foto di tabel 'user'
        $values[] = $photo;
    }

    $update_query .= implode(', ', $params) . " WHERE username = ?";
    $values[] = $username;

    // Persiapkan statement
    $stmt = $conn->prepare($update_query);

    // Periksa jika query prepare gagal
    if ($stmt === false) {
        die('Error preparing query: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(str_repeat('s', count($values)), ...$values);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>
            alert('Profile updated successfully!');
            window.location.href = 'admin.php?page=profile';  // Kembali ke halaman profile
        </script>";
    } else {
        echo "<script>
            alert('Failed to update profile!');
            window.location.href = 'admin.php?page=profile';  // Kembali ke halaman profile
        </script>";
    }

    $stmt->close();
}

$conn->close();
?>
