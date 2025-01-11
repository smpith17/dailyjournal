<?php
// Memulai session atau melanjutkan session yang sudah ada
session_start();

// Menyertakan kode dari file koneksi
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['user'];
  
  // Menggunakan MD5 untuk enkripsi password
  $password = md5($_POST['pass']);

  // Prepared statement untuk menghindari SQL injection
  $stmt = $conn->prepare("SELECT username, password FROM user WHERE username=?");

  // Parameter binding
  $stmt->bind_param("s", $username); // Menggunakan parameter string (s) untuk username
  
  // Eksekusi statement
  $stmt->execute();
  
  // Menampung hasil eksekusi
  $hasil = $stmt->get_result();
  
  // Mengambil baris dari hasil sebagai array asosiatif
  $row = $hasil->fetch_array(MYSQLI_ASSOC);

  // Cek apakah ada baris hasil data user yang cocok
  if (!empty($row)) {
    // Verifikasi password yang dimasukkan dengan yang ada di database (menggunakan MD5)
    if ($password == $row['password']) {
      // Jika password valid, simpan username di session
      $_SESSION['username'] = $row['username'];

      // Mengalihkan ke halaman admin
      header("location:admin.php");
      exit();
    } else {
      // Jika password salah
      header("location:login.php?error=invalid_credentials");
      exit();
    }
  } else {
    // Jika username tidak ditemukan
    header("location:login.php?error=user_not_found");
    exit();
  }

  // Menutup koneksi database
  $stmt->close();
  $conn->close();
} else {
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | My Daily Journal</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />
    <style>
      body {
        background-color: #ffd1dc; /* Background pink pastel */
      }
      .card {
        border-radius: 1.5rem;
      }
      .btn-danger {
        background-color: #ff4d4d;
        border: none;
      }
      .btn-danger:hover {
        background-color: #e63946;
      }
      .alert {
        border-radius: 1rem;
      }
      .border-success {
        border: 3px solid #28a745 !important; /* Border hijau */
      }
      .border-warning {
        border: 3px solid #ffc107 !important; /* Border kuning */
      }
    </style>
  </head>
  <body>
    <div class="container mt-5 pt-5">
      <div class="row">
        <div class="col-12 col-sm-8 col-md-6 m-auto">
          <div
            class="card border-0 shadow"
            id="login-card"
          >
            <div class="card-body">
              <div class="text-center mb-3">
                <i class="bi bi-person-circle h1 display-4"></i>
                <p class="fw-bold mb-0">My Daily Journal</p>
                <hr />
              </div>
              <form action="" method="post">
                <input
                  type="text"
                  name="user"
                  id="user-input"
                  class="form-control my-4 py-2 rounded-4"
                  placeholder="Username"
                  required
                />
                <input
                  type="password"
                  name="pass"
                  id="pass-input"
                  class="form-control my-4 py-2 rounded-4"
                  placeholder="Password"
                  required
                />
                <div class="text-center my-3 d-grid">
                  <button class="btn btn-danger rounded-4">Login</button>
                </div>
              </form>
            </div>
          </div>
          <div class="mt-4 text-center">
            <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($error == 'invalid_credentials') {
                    echo '<div class="alert alert-danger mt-3">Username atau Password Salah</div>';
                } elseif ($error == 'user_not_found') {
                    echo '<div class="alert alert-danger mt-3">Username Tidak Ditemukan</div>';
                }
            }
            ?>
          </div>
        </div>
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
<?php
}
?>
