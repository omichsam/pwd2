<?php
session_start();
ob_start();
include 'files/db_connect.php';

// Initialize SweetAlert script variable
$swal_script = '';

// Function to sanitize input data
function sanitize_input($data)
{
  return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Sanitize all inputs
  $type = isset($_POST['type']) ? sanitize_input($_POST['type']) : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';

  // Validate required fields
  if (empty($type) || empty($password)) {
    $swal_script = "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Fields',
                    text: 'Please fill in all required fields.'
                });
            </script>";
  } else {
    try {
      if ($type === 'PWD') {
        $id_number = isset($_POST['id_number']) ? sanitize_input($_POST['id_number']) : '';
        $query = "SELECT * FROM users WHERE id_number = ? AND type = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $id_number, $type);
      } else {
        $license_id = isset($_POST['license_id']) ? sanitize_input($_POST['license_id']) : '';
        $official_type = isset($_POST['official_type']) ? sanitize_input($_POST['official_type']) : '';

        $query = "SELECT * FROM officials WHERE license_id = ? AND type = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $license_id, $official_type);
      }

      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
          // Check if account is inactive (status = 0) for medical/health officers
          if (($user['active'] == 0) && ($user['type'] === 'medical_officer' || $user['type'] === 'health_officer')) {
            $swal_script = "
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Account Not Activated',
                                        text: 'Your account needs to be activated by the county. Please contact the county office to arrange activation.',
                                        showConfirmButton: true
                                    });
                                });
                            </script>";
          } else {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            if ($type === 'PWD') {
              $_SESSION['type'] = 'PWD';
              $_SESSION['pwd_user'] = [
                'id' => $user['id'],
                'id_number' => $user['id_number'],
                'name' => $user['name'],
                'email' => $user['email'],
                'county_id' => $user['county_id'],
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

              // Set redirect based on user type
              switch ($user['type']) {
                case 'health_officer':
                  $redirect = 'health/index.php';
                  break;
                case 'medical_officer':
                  $redirect = 'medical/index.php';
                  break;
                case 'county_officer':
                  $redirect = 'supervisor/index.php';
                  break;
                default:
                  $redirect = 'index.php';
              }
            }

            // Successful login SweetAlert
            $swal_script = "
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Login Successful',
                                        text: 'Welcome back, {$user['name']}!',
                                        showConfirmButton: true,
                                        timer: 2000
                                    }).then(() => {
                                        window.location.href = '$redirect';
                                    });
                                });
                            </script>";
          }
        } else {
          $swal_script = "
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Incorrect Password',
                                    text: 'Please check your password and try again.'
                                });
                            });
                        </script>";
        }
      } else {
        $swal_script = "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'User Not Found',
                                text: 'No account found with the provided credentials.'
                            });
                        });
                    </script>";
      }

      mysqli_stmt_close($stmt);
    } catch (Exception $e) {
      $swal_script = "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Database Error',
                            text: 'An error occurred while processing your request.'
                        });
                    });
                </script>";
      // Log the error for debugging
      error_log("Login error: " . $e->getMessage());
    }
  }
  mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to PWD Access</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">

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

  <style>
    :root {
      --teal-primary: #008080;
      --teal-light: #4da6a6;
      --teal-dark: #006666;
      --teal-darker: #004d4d;
      --light-gray: #f8f9fa;
      --dark-color: #2c3e50;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    /* Left Side - Static Content with Cards */
    .auth-hero {
      position: fixed;
      left: 0;
      top: 0;
      width: 50%;
      height: 100vh;
      background: linear-gradient(to bottom right, var(--teal-dark), var(--teal-darker));
      color: white;
      overflow: hidden;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .hero-content {
      max-width: 600px;
      margin: 0 auto;
    }

    .hero-title {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 1.5rem;
      color: white;
    }

    .hero-subtitle {
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      opacity: 0.9;
    }

    /* Benefit Cards - 2×2 Grid */
    .benefits-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .benefit-card {
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      padding: 1.5rem;
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.1);
      height: 100%;
    }

    .benefit-card:hover {
      background-color: rgba(255, 255, 255, 0.15);
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .benefit-icon {
      font-size: 2rem;
      color: var(--teal-light);
      margin-bottom: 1rem;
    }

    .benefit-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .benefit-description {
      font-size: 1rem;
      opacity: 0.9;
      line-height: 1.5;
    }

    /* Registration Buttons */
    .registration-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
    }

    .reg-btn {
      background-color: transparent;
      border: 2px solid teal;
      color: teal;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
      text-decoration: none;
      text-align: center;
    }

    .reg-btn:hover {
      background-color: white;
      color: var(--teal-dark);
      transform: translateY(-2px);
    }

    /* Right Side - Scrollable Form */
    .right-side {
      position: fixed;
      right: 0;
      top: 0;
      width: 50%;
      height: 100vh;
      overflow-y: auto;
      padding: 2rem;
      background-color: white;
      scroll-behavior: smooth;
    }

    /* Custom scrollbar */
    .right-side::-webkit-scrollbar {
      width: 8px;
    }

    .right-side::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    .right-side::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
    }

    .right-side::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    .form-container {
      max-width: 500px;
      margin: 0 auto;
      padding: 2rem;
    }

    .logo {
      font-size: 2rem;
      font-weight: bold;
      color: var(--teal-primary);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }

    .logo-icon {
      margin-right: 0.75rem;
      color: var(--teal-primary);
    }

    .auth-title {
      font-size: 1.8rem;
      font-weight: bold;
      color: var(--dark-color);
      margin-bottom: 0.5rem;
    }

    .auth-subtitle {
      color: #6c757d;
      margin-bottom: 2rem;
      font-size: 1.1rem;
    }

    /* Form styles */
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
      color: var(--teal-primary);
      background: none;
      border-bottom: 3px solid var(--teal-primary);
    }

    .form-control,
    .form-select {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #ced4da;
      margin-bottom: 1.25rem;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--teal-light);
      box-shadow: 0 0 0 0.25rem rgba(0, 128, 128, 0.25);
    }

    .btn-primary {
      background-color: var(--teal-primary);
      border: none;
      padding: 0.75rem;
      font-weight: 500;
      width: 100%;
      border-radius: 8px;
    }

    .btn-primary:hover {
      background-color: var(--teal-dark);
    }

    /* Responsive styles */
    @media (max-width: 991.98px) {
      .auth-hero {
        position: relative;
        width: 100%;
        height: auto;
        padding: 2rem;
      }

      .right-side {
        position: relative;
        width: 100%;
        height: auto;
      }

      .benefits-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 575.98px) {
      .benefits-grid {
        grid-template-columns: 1fr;
      }

      .registration-buttons {
        flex-direction: column;
        gap: 0.75rem;
      }
    }
  </style>
</head>

<body>

  <!-- #region -->

  <div class="container-fluid p-0">
    <!-- Left side with teal background and cards -->
    <div class="auth-hero d-none d-lg-block">
      <div class="hero-content">
        <h1 class="hero-title">Welcome to PWD Access</h1>
        <p class="hero-subtitle">Join our community to access disability services and support programs</p>

        <div class="benefits-grid">
          <!-- Card 1 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="benefit-title">Secure Services</h3>
            <p class="benefit-description">Your information is protected with industry-standard security measures.</p>
          </div>

          <!-- Card 2 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-heartbeat"></i>
            </div>
            <h3 class="benefit-title">Health Support</h3>
            <p class="benefit-description">Access personalized healthcare services tailored to your needs.</p>
          </div>

          <!-- Card 3 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-clock"></i>
            </div>
            <h3 class="benefit-title">Quick Access</h3>
            <p class="benefit-description">Fast and easy registration process to get you the help you need.</p>
          </div>

          <!-- Card 4 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-comments"></i>
            </div>
            <h3 class="benefit-title">24/7 Support</h3>
            <p class="benefit-description">Our team is always available to assist you with any questions.</p>
          </div>
        </div>


      </div>
    </div>

    <!-- Right side with scrollable form -->
    <div class="right-side">
      <div class="form-container">
        <div class="logo">
          <i class="fas fa-wheelchair-motion logo-icon"></i>
          <span>PWD Access</span>
        </div>

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
              <button type="submit" name="" class="btn btn-primary">Sign In as User</button>
            </form>
          </div>




          <!-- Official Login Form -->
          <div class="tab-pane fade" id="official" role="tabpanel">
            <form method="POST" action="" id="officialLoginForm">
              <input type="hidden" name="type" value="official">
              <div class="form-group">
                <label for="official_type">Official Type</label>
                <select class="form-select" name="official_type" id="official_type" required>
                  <option value="">-- Select Type --</option>
                  <option value="health_officer">Health Officer</option>
                  <option value="medical_officer">Approval Officer</option>
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
              <button type="submit" name="" class="btn btn-primary">Sign In as Official</button>
            </form>
          </div>

          <!-- Registration Buttons -->
          <div class="registration-buttons">
            <a href="register" class="reg-btn">Register as PWD</a>
            <a href="official_reg" class="reg-btn">Register as Official</a>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php
  if (isset($swal_script)) {
    echo $swal_script;
  }
  ?>
</body>

</html>
<?php ob_end_flush(); ?>