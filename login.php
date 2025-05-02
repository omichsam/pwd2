<?php
include 'files/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; PWD</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet"> -->
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- SweetAlert2 -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
  <!-- Sweetalert -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'UA-94034622-3');
  </script>

  <style>
    body {
      background-color: #f0f2f5;
    }

    .card-login {
      max-width: 420px;
      margin: auto;
      margin-top: 60px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
    }

    .nav-tabs .nav-link {
      font-weight: 500;
    }

    .card-header h4 {
      margin-bottom: 0;
    }
  </style>
</head>

<body>

  <?php
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
      $query = "SELECT * FROM officials WHERE license_id = ? AND type = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "ss", $license_id, $type);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
      $user = mysqli_fetch_assoc($result);

      if (password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['type'] = $type;
        $_SESSION['user'] = [
          'id' => $user['id'],                  // or user_id if column name differs
          'name' => $user['name'],
          'email' => $user['email'],
          'id_number' => $user['id_number'], 
          'mobile_number' => $user['mobile_number']
        ];

        if ($type === 'PWD') {
          $redirect = 'pwd/index.php';
        } elseif ($type === 'Medical_Official') {
          $redirect = 'medical/index.php';
        } elseif ($type === 'Health_Official') {
          $redirect = 'medical/index.php';
        } else {
          $redirect = 'supervisor/index.php';
        }

        echo "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Welcome back!'
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
  <div class="container">
    <div class="card card-login">
      <div class="card-header text-center">
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
            <a class="nav-link" id="official-tab" data-toggle="tab" href="#official" role="tab" aria-controls="official"
              aria-selected="false">Official</a>
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
                <input type="text" class="form-control" name="id_number" id="id_number" required>
                <div class="invalid-feedback">Please enter your ID Number.</div>
              </div>

              <div class="form-group">
                <label for="pwd_password">Password</label>
                <input type="password" class="form-control" name="password" id="pwd_password" required>
                <div class="invalid-feedback">Please enter your password.</div>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login as PWD</button>
              </div>
            </form>
          </div>

          <!-- Official Login -->
          <div class="tab-pane fade" id="official" role="tabpanel" aria-labelledby="official-tab">
            <form method="POST" action="" id="officialLoginForm" class="needs-validation" novalidate>
              <div class="form-group">
                <label for="type">Official Type</label>
                <select class="form-control" name="type" id="type" required>
                  <option value="">-- Select Type --</option>
                  <option value="Health Officer">Health Officer</option>
                  <option value="Medical Officer">Medical Officer</option>
                  <option value="County Officer">County Officer</option>
                </select>
                <div class="invalid-feedback">Please select your official type.</div>
              </div>

              <div class="form-group">
                <label for="license_id">License Number</label>
                <input type="text" class="form-control" name="license_id" id="license_id" required>
                <div class="invalid-feedback">Please enter your License Number.</div>
              </div>

              <div class="form-group">
                <label for="official_password">Password</label>
                <input type="password" class="form-control" name="password" id="official_password" required>
                <div class="invalid-feedback">Please enter your password.</div>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-success btn-block">Login as Official</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap & jQuery -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Validation -->
  <!-- <script>
    (function () {
      'use strict';
      window.addEventListener('load', function () {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.forEach.call(forms, function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script> -->

  <script>
    function toggleFields() {
      let type = document.querySelector('select[name="type"]').value;
      document.getElementById("idGroup").style.display = (type === "PWD") ? "block" : "none";
      document.getElementById("licenseGroup").style.display = (type !== "PWD") ? "block" : "none";
    }
  </script>


  <!-- < ?php if (!empty($message)): ?>
    <script>
      document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = new FormData(this);

        fetch('files/login_handler.php', {
          method: 'POST',
          body: form
        })
          .then(res => res.json())
          .then(data => {
            Swal.fire({
              icon: data.status === 'success' ? 'success' : 'error',
              title: data.message,
              showConfirmButton: false,
              timer: 2000
            }).then(() => {
              if (data.status === 'success') {
                window.location.href = data.redirect;
              }
            });
          })
          .catch(() => {
            Swal.fire('Error', 'Something went wrong.', 'error');
          });
      });
    </script>

  < ?php endif; ?> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>
  <!-- <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> -->
</body>

</html>