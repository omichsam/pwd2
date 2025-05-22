<?php
// Start session and include database connection
session_start();
ob_start();
include 'files/db_connect.php';

// Handle login form submission
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
    $official_type = $_POST['official_type'];
    $query = "SELECT * FROM officials WHERE license_id = ? AND type = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $license_id, $official_type);
  }

  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
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

        if ($user['type'] === 'health_officer') {
          $redirect = 'health/index.php';
        } elseif ($user['type'] === 'medical_officer') {
          $redirect = 'medical/index.php';
        } elseif ($user['type'] === 'county_officer') {
          $redirect = 'supervisor/index.php';
        } else {
          $redirect = 'index.php';
        }
      }

      $swal_script = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Welcome back, {$user['name']}!',
                    confirmButtonColor: '#008080'
                }).then(() => {
                    window.location.href = '$redirect';
                });
            </script>";
    } else {
      $swal_script = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Password',
                    text: 'Please check your password and try again.',
                    confirmButtonColor: '#008080'
                });
            </script>";
    }
  } else {
    $swal_script = "<script>
            Swal.fire({
                icon: 'error',
                title: 'User Not Found',
                text: 'No account found with the provided credentials.',
                confirmButtonColor: '#008080'
            });
        </script>";
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to PWD County</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="assets/modules/weather-icon/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="assets/modules/summernote/summernote-bs4.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet" href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-94034622-3');
  </script>
  <!-- /END GA -->


  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --primary-color: #008080;
      --primary-light: #4da6a6;
      --primary-dark: #006666;
      --secondary-color: #5cb85c;
      --dark-color: #2c3e50;
      --light-color: #ecf0f1;
    }

    body {
      /* font-family: 'Poppins', sans-serif; */
      background-color: #f9f9f9;
    }

    .auth-container {
      display: flex;
      min-height: 100vh;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .auth-hero {
      flex: 1;
      background: linear-gradient(135deg, rgba(0, 128, 128, 0.85), rgba(0, 96, 96, 0.9)),
        url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
      background-size: cover;
      background-position: center;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      position: relative;
      align-items: center;
      /* 
        flex: 1;
      background: linear-gradient(135deg, rgba(0, 128, 128, 0.9), rgba(0, 96, 96, 0.95)),
        url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
      background-size: cover;
      background-position: center;  //
      color: white;
      padding: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center; */


    }

    .auth-hero::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      opacity: 0.9;
      z-index: 0;
    }

    .auth-hero-content {
      position: relative;
      z-index: 1;
      background: linear-gradient(135deg, rgba(0, 128, 128, 0.9), rgba(0, 96, 96, 0.95)),
        url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
      background-size: cover;
    }

    .auth-form {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      background-color: white;
    }

    .logo {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--primary-color);
      margin-bottom: 2rem;
    }

    .auth-title {
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 0.5rem;
    }

    .auth-subtitle {
      color: #7f8c8d;
      margin-bottom: 2rem;
    }

    .nav-tabs {
      border-bottom: none;
      margin-bottom: 2rem;
    }

    .nav-tabs .nav-link {
      border: none;
      color: #95a5a6;
      font-weight: 500;
      padding: 0.5rem 1rem;
      margin-right: 1rem;
    }

    .nav-tabs .nav-link.active {
      color: var(--primary-color);
      background: none;
      border-bottom: 3px solid var(--primary-color);
    }

    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #ddd;
      margin-bottom: 1.5rem;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.25rem rgba(0, 128, 128, 0.25);
    }

    .btn-primary {
      background-color: var(--primary-color);
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      font-weight: 500;
      width: 100%;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
    }

    .auth-footer {
      text-align: center;
      margin-top: 2rem;
      color: #95a5a6;
    }

    .auth-footer a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }

    .feature-list {
      list-style: none;
      padding: 0;
    }

    .feature-list li {
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }

    .feature-icon {
      background-color: rgba(255, 255, 255, 0.2);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
    }

    @media (max-width: 992px) {
      .auth-container {
        flex-direction: column;
      }

      .auth-hero {
        padding: 2rem;
        text-align: center;
      }

      .auth-form {
        padding: 2rem;
      }

      .feature-list li {
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <div class="auth-container">
      <!-- Left side with image and overlay -->
      <div class="auth-hero d-none d-lg-flex">
        <div class="auth-hero-content">
          <h1 class="display-4 fw-bold mb-4">Welcome to PWD County</h1>
          <p class="lead mb-5">Join our community to access disability services and support programs designed for you.
          </p>

          <ul class="feature-list">
            <li>
              <span class="feature-icon">
                <i class="fas fa-user-shield"></i>
              </span>
              <span>Secure and confidential services</span>
            </li>
            <li>
              <span class="feature-icon">
                <i class="fas fa-heartbeat"></i>
              </span>
              <span>Personalized healthcare support</span>
            </li>
            <li>
              <span class="feature-icon">
                <i class="fas fa-clock"></i>
              </span>
              <span>Quick and easy registration</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Right side with login form -->
      <div class="auth-form">
        <div class="logo">PWD County</div>

        <h2 class="auth-title">Access Your Account</h2>
        <p class="auth-subtitle">Sign in to continue to your dashboard</p>

        <ul class="nav nav-tabs" id="loginTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button"
              role="tab">User Login</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="official-tab" data-bs-toggle="tab" data-bs-target="#official" type="button"
              role="tab">Official Login</button>
          </li>
        </ul>

        <div class="tab-content" id="loginTabsContent">
          <!-- User Login Form -->
          <div class="tab-pane fade show active" id="user" role="tabpanel">
            <form method="POST" action="" id="userLoginForm">
              <input type="hidden" name="type" value="PWD">
              <div class="form-group">
                <label for="id_number">ID Number</label>
                <input type="text" class="form-control" name="id_number" id="id_number"
                  placeholder="Enter your PWD ID number" required>
              </div>
              <div class="form-group">
                <label for="user-password">Password</label>
                <input type="password" class="form-control" name="password" id="user-password"
                  placeholder="Enter your password" required>
              </div>
              <div class="d-flex justify-content-between mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember-user">
                  <label class="form-check-label" for="remember-user">Remember me</label>
                </div>
                <a href="#" class="text-decoration-none">Forgot password?</a>
              </div>
              <button type="submit" class="btn btn-primary">Sign In as User</button>
            </form>
          </div>

          <!-- Official Login Form -->
          <div class="tab-pane fade" id="official" role="tabpanel">
            <form method="POST" action="" id="officialLoginForm">
              <input type="hidden" name="type" value="OFFICIAL">
              <div class="form-group">
                <label for="official_type">Official Type</label>
                <select class="form-control" name="official_type" id="official_type" required>
                  <option value="">-- Select Type --</option>
                  <option value="health_officer">Health Officer</option>
                  <option value="medical_officer">Medical Officer</option>
                  <option value="county_officer">County Officer</option>
                </select>
              </div>
              <div class="form-group">
                <label for="license_id">License Number</label>
                <input type="text" class="form-control" name="license_id" id="license_id"
                  placeholder="Enter your license number" required>
              </div>
              <div class="form-group">
                <label for="official-password">Password</label>
                <input type="password" class="form-control" name="password" id="official-password"
                  placeholder="Enter your password" required>
              </div>
              <div class="d-flex justify-content-between mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember-official">
                  <label class="form-check-label" for="remember-official">Remember me</label>
                </div>
                <a href="#" class="text-decoration-none">Forgot password?</a>
              </div>
              <button type="submit" class="btn btn-primary">Sign In as Official</button>
            </form>
          </div>
        </div>

        <div class="auth-footer">
          <!-- Don't have an account? <a href="#">Register here</a> -->
          Create account for <a href="register.php" class="btn border-info text-info">PWD Applicant</a>
          <nbsp>|</nbsp> <a href="official_reg.php" class="btn border-dark text-dark">Officials</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.getElementById('logoutBtn').addEventListener('click', function (e) {
      Swal.fire({
        title: 'Are you sure you want to logout?',
        text: "You will need to login again to access your account.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'No, stay logged in'
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect to logout.php
          window.location.href = 'files/logout.php';
        }
      });
    });
  </script>
  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>

  <!-- JS Libraies -->
  <script src="assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
  <script src="assets/modules/chart.min.js"></script>
  <script src="assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
  <script src="assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
  <script src="assets/modules/summernote/summernote-bs4.js"></script>
  <script src="assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="assets/js/page/index-0.js"></script>

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>



  <!-- JS Libraies -->
  <script src="assets/modules/datatables/datatables.min.js"></script>
  <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
  <script src="assets/modules/jquery-ui/jquery-ui.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="assets/js/page/modules-datatables.js"></script>

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>

  <?php if (isset($swal_script))
    echo $swal_script; ?>
</body>

</html>
<?php ob_end_flush(); ?>