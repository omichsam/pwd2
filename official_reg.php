<?php
include 'files/register_officials.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PWD Access - Official Portal</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
      font-size: 1.5rem;
      /* Adjusted icon size */
      color: var(--teal-light);
      margin-bottom: 0.75rem;
    }

    .benefit-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .benefit-description {
      font-size: 0.9rem;
      opacity: 0.9;
      line-height: 1.5;
    }

    /* Login Button - Right Side */
    .login-btn-container {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
    }

    .login-btn {
      background-color: transparent;
      border: 2px solid rgb(1, 57, 57);
      color: rgb(1, 57, 57);
      padding: 0.5rem 1.25rem;
      border-radius: 6px;
      font-weight: 500;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
    }

    .login-btn:hover {
      background-color: rgb(1, 57, 57);
      /* color: var(--teal-dark); */
      color: white;
      transform: translateY(-2px);
    }

    .login-btn i {
      margin-right: 0.5rem;
      font-size: 0.9rem;
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
      max-width: 600px;
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
      font-size: 1.8rem;
      /* Adjusted to match text */
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
    .form-section {
      margin-bottom: 2.5rem;
    }

    .section-title {
      color: var(--teal-primary);
      font-weight: 600;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid var(--teal-light);
    }

    .form-control,
    .form-select {
      padding: 0.75rem 1rem;
      border-radius: 10px;
      border: 1px solid #ced4da;
      /* margin-bottom: 1.25rem;  */
      height: calc(2.9rem + 2px); //*Fixed height*/
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--teal-light);
      box-shadow: 0 0 0 0.25rem rgba(0, 128, 128, 0.25);
    }

    /* Input Group Styles */
    .input-group {
      height: calc(2.25rem + 2px);
      /* Match Bootstrap's default input height */
      align-items: center;
      /* Vertically center contents */
    }

    .input-group-text {
      height: 100%;
      /* Take full height of parent */
      width: 40px;
      /* Fixed width for consistency */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      /* Remove default padding */
      background-color: #f1f5f9;
      /* Light gray background */
      border: 1px solid #ced4da;
      /* Match Bootstrap's border */
      border-right: none;
      /* Remove double border */
    }

    .input-group-text i {
      font-size: 1rem;
      /* Match input text size */
      color: rgb(3, 81, 60);
      /* Gray icon color */
    }

    .input-group .form-control {
      height: 100%;
      /* Take full height of parent */
      border-left: none;
      /* Remove double border */
    }

    /* Fix for first input in group to have proper rounded corners */
    .input-group>.form-control:not(:first-child) {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }

    .btn-primary {
      background-color: var(--teal-primary);
      border: none;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: var(--teal-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 101, 80, 0.2);
    }

    /* Password toggle */
    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      transition: color 0.2s;
      font-size: 1rem;
      /* Adjusted icon size */
    }

    .password-toggle:hover {
      color: var(--teal-primary);
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
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <!-- Left side with teal background and cards -->
    <div class="auth-hero d-none d-lg-block">
      <div class="hero-content">
        <div class="text-center mb-5">
          <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
            style="width: 80px; height: 80px; background-color: rgba(255,255,255,0.2);">
            <i class="fas fa-id-card-alt text-white fs-3"></i>
          </div>
          <h1 class="hero-title">Official Registration Portal</h1>
          <p class="hero-subtitle">Streamlined access for government officials and service providers</p>
        </div>

        <h3 class="h4 mb-4">Benefits of Official Registration:</h3>
        <div class="benefits-grid">
          <!-- Card 1 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-bolt"></i>
            </div>
            <h3 class="benefit-title">Quick Verification</h3>
            <p class="benefit-description">Fast-tracked approval process for qualified officials</p>
          </div>

          <!-- Card 2 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="benefit-title">Performance Analytics</h3>
            <p class="benefit-description">Track service delivery metrics and impact</p>
          </div>

          <!-- Card 3 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-users-cog"></i>
            </div>
            <h3 class="benefit-title">Case Management</h3>
            <p class="benefit-description">Efficient tools for managing beneficiary cases</p>
          </div>

          <!-- Card 4 -->
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-file-alt"></i>
            </div>
            <h3 class="benefit-title">Documentation</h3>
            <p class="benefit-description">Secure digital record-keeping system</p>
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

        <h2 class="auth-title">Official Registration</h2>
        <p class="auth-subtitle">Provide personal information for your registration</p>

        <form id="registerForm" action="" method="POST">
          <!-- Personal Information Section -->
          <div class="form-section">
            <h6 class="section-title">Personal Information</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Full Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                  <input id="name" type="text" class="form-control" name="name" required placeholder="Your full name">
                </div>
              </div>

              <div class="col-md-6">
                <label for="id_number" class="form-label">National ID</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                  <input id="id_number" type="text" class="form-control" name="id_number" required
                    placeholder="National ID number">
                </div>
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                  <input id="email" type="email" class="form-control" name="email" required
                    placeholder="example@domain.com">
                </div>
              </div>

              <div class="col-md-6">
                <label for="mobileNumber" class="form-label">Mobile Number</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-phone"></i></span>
                  <input id="mobileNumber" type="text" class="form-control" name="mobileNumber" required
                    placeholder="0712345678">
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Information Section -->
          <div class="form-section">
            <h6 class="section-title">Professional Information</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="license_id" class="form-label">License ID</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                  <input id="license_id" type="text" class="form-control" name="license_id" required
                    placeholder="Enter your license ID">
                </div>
              </div>



              <div class="col-md-6">
                <label for="type" class="form-label">Official Type</label>
                <select class="form-select" name="type" id="type" required>
                  <option value="">Select your role</option>
                  <option value="health_officer">Health Officer</option>
                  <option value="medical_officer">Approval Officer</option>
                  <option value="county_officer">County Officer</option>
                </select>
              </div>

              <div class="col-md-6">
                <label for="specialisation" class="form-label">Specialisation</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                  <input id="specialisation" type="text" class="form-control" name="specialisation" required
                    placeholder="Your specialisation">
                </div>
              </div>

              <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-building"></i></span>
                  <input id="department" type="text" class="form-control" name="department" required
                    placeholder="Your department">
                </div>
              </div>

              <div class="col-12">
                <label for="county_id" class="form-label">County</label>
                <select class="form-select" name="county_id" required>
                  <option value="">Select your county</option>
                  <?php
                  include 'files/db_connect.php';
                  $counties = mysqli_query($conn, "SELECT id, county_name FROM counties ORDER BY county_name");
                  while ($row = mysqli_fetch_assoc($counties)) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['county_name']) . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Account Security Section -->
          <div class="form-section">
            <h6 class="section-title">Account Security</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <div class="password-container">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control" name="password" required
                      placeholder="Create password">
                  </div>
                  <i class="password-toggle fas fa-eye" onclick="togglePassword('password')"></i>
                </div>
                <small class="text-muted">Minimum 8 characters</small>
              </div>

              <div class="col-md-6">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="password-container">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="confirmPassword" type="password" class="form-control" name="confirmPassword" required
                      placeholder="Confirm password">
                  </div>
                  <i class="password-toggle fas fa-eye" onclick="togglePassword('confirmPassword')"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-md text-center">
              <i class="fas fa-user-plus me-2"></i> Complete Registration
            </button>


          </div>
          <!-- Login Button - Right Side -->
          <div class="login-btn-containe_r d-grid">
            &nbsp;&nbsp; <a href="login" class="btn btn-success btn-md p-2">
              <i class="fas fa-sign-in-alt"></i> Already have account Login Here
            </a>
          </div>



          <div class="text-center small text-muted mt-4 ">
            <p class="font-weight-bolder"><b>For security reasons, please:</b></p>
            <ul class="list-inline mb-0">
              <li class="list-inline-item"><i class="fas fa-shield-alt text-success me-1"></i> Use strong passwords</li>
              <li class="list-inline-item"><i class="fas fa-lock text-success me-1"></i> Log out after session</li>
              <li class="list-inline-item"><i class="fas fa-sync-alt text-success me-1"></i> Update credentials
                regularly</li>
            </ul>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Password toggle functionality
    function togglePassword(fieldId) {
      const passwordField = document.getElementById(fieldId);
      const toggleIcon = passwordField.nextElementSibling;

      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
      } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
      }
    }
  </script>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status === 'empty') {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Fields',
        text: 'Please fill in all required fields.'
      });
    } else if (status === 'pass_mismatch') {
      Swal.fire({
        icon: 'error',
        title: 'Password Mismatch',
        text: 'Passwords do not match.'
      });
    } else if (status === 'exists') {
      Swal.fire({
        icon: 'info',
        title: 'Already Exists',
        text: 'A user with this license ID or email already exists.'
      });
    } else if (status === 'success') {
      Swal.fire({
        title: 'Success!',
        text: 'Official has been registered.',
        icon: 'success',
        confirmButtonText: 'Proceed to Login'
      }).then(() => {
        window.location.href = 'login.php';
      });
    } else if (status === 'fail') {
      Swal.fire({
        icon: 'error',
        title: 'Registration Failed',
        text: 'Could not save data to the database.'
      });
    }
  </script>

</body>

</html>