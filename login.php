<?php
include 'files/db_connect.php'; ?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; PWD</title>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <style>
    :root {
      --primary-color: rgba(0, 123, 94, 0.84);
      --secondary-color: rgb(22, 76, 45);
      --accent-color: #4cc9f0;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }

    body {
      background-color: #f8fafc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }

    .login-container {
      display: flex;
      min-height: 100vh;
      flex-wrap: wrap;
    }

    .login-image {
      flex: 1 1 50%;
      min-height: 100vh;
      background: linear-gradient(rgba(2, 101, 55, 0.97), rgba(0, 155, 155, 0.8)),
        url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 2rem;
      color: white;
      position: relative;
    }

    .login-image-content {
      max-width: 80%;
      margin: 0 auto;
    }

    .login-image h1 {
      font-weight: 700;
      font-size: clamp(1.8rem, 3vw, 2.5rem);
      margin-bottom: 1rem;
      line-height: 1.3;
    }

    .login-image p {
      font-size: clamp(1rem, 1.2vw, 1.1rem);
      opacity: 0.9;
      line-height: 1.6;
      margin-bottom: 1.5rem;
    }

    .login-image-features {
      position: absolute;
      bottom: 2rem;
      left: 0;
      right: 0;
      text-align: center;
    }

    .login-form-container {
      flex: 1 1 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      min-height: 100vh;
    }

    .card-login {
      width: 100%;
      max-width: 500px;
      border: none;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      margin: 0 auto;
    }

    .card-header {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 1.5rem;
      border-bottom: none;
    }

    .card-header h4 {
      font-weight: 600;
      margin: 0;
      font-size: 1.5rem;
    }

    .card-body {
      padding: 2rem;
    }

    .nav-tabs {
      border-bottom: 2px solid #e9ecef;
      margin-bottom: 1.5rem;
      flex-wrap: nowrap;
    }

    .nav-tabs .nav-link {
      border: none;
      padding: 0.75rem 1rem;
      font-weight: 500;
      color: #6c757d;
      transition: all 0.3s;
      white-space: nowrap;
      font-size: 0.9rem;
    }

    .nav-tabs .nav-link.active {
      color: var(--primary-color);
      background: transparent;
      border-bottom: 2px solid var(--primary-color);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-control {
      height: 45px;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      padding-left: 15px;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .btn-block {
      height: 45px;
      border-radius: 8px;
      font-weight: 500;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
    }

    .btn-success {
      background-color: rgb(4, 34, 21);
      border-color: #4cc9f0;
    }

    .btn-success:hover {
      background-color: rgb(2, 95, 62);
      border-color: rgb(6, 123, 88);
    }

    .text-muted {
      margin-top: 2rem;
      text-align: center;
    }

    .text-muted a {
      margin: 0.5rem;
      display: inline-block;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
      .login-image h1 {
        font-size: 2rem;
      }

      .nav-tabs .nav-link {
        padding: 0.75rem;
      }
    }

    @media (max-width: 992px) {
      .login-container {
        flex-direction: column;
      }

      .login-image {
        flex: 1 1 100%;
        min-height: auto;
        padding: 3rem 2rem;
        order: -1;
      }

      .login-image-content {
        max-width: 100%;
      }

      .login-image-features {
        position: static;
        margin-top: 2rem;
      }

      .login-form-container {
        flex: 1 1 100%;
        min-height: auto;
        padding: 2rem 1rem;
      }

      .card-login {
        max-width: 100%;
      }
    }

    @media (max-width: 576px) {
      .login-image {
        padding: 2rem 1rem;
      }

      .login-image h1 {
        font-size: 1.5rem;
      }

      .login-image p {
        font-size: 0.9rem;
      }

      .card-body {
        padding: 1.5rem;
      }

      .nav-tabs .nav-link {
        padding: 0.5rem;
        font-size: 0.8rem;
      }

      .text-muted a {
        display: block;
        width: 100%;
        margin: 0.5rem 0;
      }
    }
  </style>
</head>

<body>

  <?php



  include 'files/db_connect.php';

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'];
    $password = $_POST['password'];

    if ($type === 'PWD') {
      $id_number = $_POST['id_number'];
      $query = "SELECT * FROM users WHERE id_number = ? AND type = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "ss", $id_number, $type);
    } else {
      $license_id = $_POST['license_id'];
      $query = "SELECT * FROM officials WHERE license_id = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "s", $license_id);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
      $user = mysqli_fetch_assoc($result);

      if (password_verify($password, $user['password'])) {
        session_start();
        session_regenerate_id(true);

        $_SESSION['logged_in'] = true;

        if ($type === 'PWD') {
          $_SESSION['type'] = 'PWD';
          $_SESSION['pwd_user'] = [
            'id' => $user['id'],
            'id_number' => $user['id_number'],
            'name' => $user['name'],
            'email' => $user['email'],
            'mobile_number' => $user['mobile_number'],
            'type' => $user['type']
          ];
          $redirect = 'pwd/index.php';
          $username = $user['name'];
        } else {
          $_SESSION['type'] = $user['type'];
          $_SESSION['official_user'] = [
            'id' => $user['id'],
            'license_id' => $user['license_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'mobile_number' => $user['mobile_number'],
            'type' => $user['type']
          ];

          $username = $user['name'];

          if ($user['type'] === 'health_officer') {
            $redirect = 'health/index';
          } elseif ($user['type'] === 'medical_officer') {
            $redirect = 'medical/index';
          } elseif ($user['type'] === 'county_officer') {
            $redirect = 'supervisor/index';
          } else {
            $redirect = 'index';
          }
        }

        echo " 
          <script>
              Swal.fire({
                  icon: 'success',
                  title: 'Login Successful',
                  text: 'Welcome back, " . htmlspecialchars($user['type']) . "!'
              }).then(() => {
                  window.location.href = '$redirect';
              });
          </script>";
      } else {
        echo " 
          <script>
              Swal.fire({
                  icon: 'error',
                  title: 'Incorrect Password',
                  text: 'Please check your password and try again.'
              });
          </script>";
      }
    } else {
      echo " 
        <script>
            Swal.fire({
                icon: 'error',
                title: 'User Not Found',
                text: 'No account found with the provided credentials.'
            });
        </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
  }
  ?>


  <div class="login-container">
    <div class="login-image">
      <div class="login-image-content">
        <h1>Welcome to PWD Portal</h1>
        <p>Access your account to manage your profile, view services, and connect with healthcare professionals.</p>
      </div>
      <div class="login-image-features">
        <p><i class="fas fa-shield-alt"></i> Secure login</p>
        <p><i class="fas fa-headset"></i> 24/7 Support</p>
      </div>
    </div>

    <div class="login-form-container">
      <div class="card card-login">
        <div class="card-header">
          <h4>Login Portal</h4>
        </div>
        <div class="card-body">
          <!-- Nav Tabs -->
          <ul class="nav nav-tabs mb-4 justify-content-center" id="loginTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="pwd-tab" data-toggle="tab" href="#pwd" role="tab" aria-controls="pwd"
                aria-selected="true">PWD</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="official-tab" data-toggle="tab" href="#official" role="tab"
                aria-controls="official" aria-selected="false">Official</a>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="loginTabContent">
            <!-- PWD Login -->
            <div class="tab-pane fade show active" id="pwd" role="tabpanel" aria-labelledby="pwd-tab">
              <form method="POST" action="" id="pwdLoginForm">
                <input type="hidden" name="type" value="PWD">
                <div class="form-group">
                  <label for="id_number">ID Number</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    </div>
                    <input type="text" class="form-control" name="id_number" id="id_number"
                      placeholder="Enter your ID number" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="pwd_password">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" class="form-control" name="password" id="pwd_password"
                      placeholder="Enter your password" required>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login as PWD
                  </button>
                </div>
              </form>
            </div>

            <!-- Official Login -->
            <div class="tab-pane fade" id="official" role="tabpanel" aria-labelledby="official-tab">
              <form method="POST" action="" id="officialLoginForm" class="needs-validation" novalidate>
                <div class="form-group" hidden>
                  <label for="type">Official Type</label>
                  <select class="form-control" name="type" id="type" required>
                    <option value="">-- Select Type --</option>
                    <option value="health_officer">Health Officer</option>
                    <option value="medical_officer">Medical Officer</option>
                    <option value="county_officer">County Officer</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="license_id">License Number</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                    </div>
                    <input type="text" class="form-control" name="license_id" id="license_id"
                      placeholder="Enter your license number" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="official_password">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" class="form-control" name="password" id="official_password"
                      placeholder="Enter your password" required>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-user-md"></i> Login as Official
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div class="mt-5 text-muted text-center">
            <p>Don't have an account?</p>
            <a href="register.php" class="btn btn-outline-primary">Register as PWD</a>
            <a href="official_reg.php" class="btn btn-outline-secondary">Register as Official</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap & jQuery -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>

  <script>
    function toggleFields() {
      let type = document.querySelector('select[name="type"]').value;
      document.getElementById("idGroup").style.display = (type === "PWD") ? "block" : "none";
      document.getElementById("licenseGroup").style.display = (type !== "PWD") ? "block" : "none";
    }
  </script>
</body>

</html>