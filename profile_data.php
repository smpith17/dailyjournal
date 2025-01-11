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
    $password = md5($_POST['password']);
}

// Jika ada perubahan foto profil
if (!empty($_FILES['photo']['name'])) {
    $photo_name = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_path = 'img/' . $username . '.jpg';  // Simpan dengan nama username

    $valid_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));

    if (in_array($file_extension, $valid_extensions)) {
        // Hapus file lama jika ada
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }

        // Pindahkan file gambar ke folder img
        if (move_uploaded_file($photo_tmp, $photo_path)) {
            $photo = $username . '.jpg';
        } else {
            echo "<script>alert('Gagal mengupload gambar. Pastikan folder img/ memiliki izin yang benar.');</script>";
            exit();
        }
    } else {
        echo "<script>alert('Hanya gambar dengan ekstensi JPG, JPEG, dan PNG yang diizinkan.');</script>";
        exit();
    }
}

// Update data ke database jika ada perubahan
if ($password || $photo) {
    $update_query = "UPDATE user SET ";
    $params = [];
    $values = [];

    if ($password) {
        $params[] = "password = ?";
        $values[] = $password;
    }

    if ($photo) {
        $params[] = "foto = ?";
        $values[] = $photo;
    }

    $update_query .= implode(', ', $params) . " WHERE username = ?";
    $values[] = $username;

    $stmt = $conn->prepare($update_query);

    if ($stmt === false) {
        die('Error preparing query: ' . $conn->error);
    }

    $stmt->bind_param(str_repeat('s', count($values)), ...$values);

    if ($stmt->execute()) {
        echo "<script>
            alert('Profile updated successfully!');
            window.location.href = 'admin.php?page=profile';
        </script>";
    } else {
        echo "<script>
            alert('Failed to update profile!');
            window.location.href = 'admin.php?page=profile';
        </script>";
    }

    $stmt->close();
}

$conn->close();
?>
