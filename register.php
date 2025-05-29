<?php include 'files/register_user.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PWD County - Registration Portal</title>
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

    /* Left Side - Static Content */
    .auth-hero {
      position: fixed;
      left: 0;
      top: 0;
      width: 50%;
      height: 100vh;
      background: linear-gradient(to bottom right, #004d4d, #4da6a6);
      color: white;
      overflow: hidden;
    }

    .bg-overlay {
      position: relative;
      height: 100%;
      background-image: url('https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
    }

    .bg-overlay::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom right, #004d4d, #4da6a6);
      opacity: 0.95;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      height: 100%;
      padding: 3rem;
      display: flex;
      flex-direction: column;
    }

    /* Benefits Grid */
    .benefits-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .benefit-card {
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      padding: 1.5rem;
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .benefit-card:hover {
      background-color: rgba(255, 255, 255, 0.15);
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .benefit-icon {
      font-size: 1.8rem;
      color: var(--primary-light);
      margin-bottom: 1rem;
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

    /* Right Side - Scrollable Form */
    .right-side {
      position: fixed;
      right: 0;
      top: 0;
      width: 50%;
      height: 100vh;
      overflow-y: auto;
      padding: 2rem;
      background-color: var(--light-gray);
      scroll-behavior: smooth;
    }

    /* Custom scrollbar for right side */
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

    .form-card {
      max-width: 800px;
      margin: 0 auto;
      padding: 2.5rem;
      border-radius: 12px;
      background: white;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .auth-logo {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 2rem;
    }

    .auth-logo-icon {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-right: 1rem;
    }

    .auth-logo-text {
      font-size: 1.8rem;
      font-weight: bold;
      color: var(--primary-color);
    }

    .form-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .form-header h2 {
      font-weight: bold;
      color: #333;
      margin-bottom: 0.5rem;
    }

    .form-header p {
      color: #6c757d;
      font-size: 1.1rem;
    }

    /* Form inputs */
    .form-control,
    .form-select {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #ced4da;
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--primary-light);
      box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.25);
    }

    .input-group-text {
      background-color: #f1f5f9;
      border-radius: 8px 0 0 8px !important;
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
    }

    .password-toggle:hover {
      color: var(--primary-color);
    }

    /* Section dividers */
    .section-divider {
      display: flex;
      align-items: center;
      margin: 2.5rem 0;
      color: #6c757d;
      font-weight: 500;
      font-size: 1.1rem;
    }

    .section-divider::before,
    .section-divider::after {
      content: "";
      flex: 1;
      border-bottom: 1px solid #e9ecef;
    }

    .section-divider::before {
      margin-right: 1rem;
    }

    .section-divider::after {
      margin-left: 1rem;
    }

    /* Submit button */
    .btn-submit {
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

    .btn-submit:hover {
      background-color: teal !important;
      color: white;
      /* border: 2px solid teal ! important; */
      transform: translateY(-2px);
    }

    .btn-outline-light:hover {
      color: var(--dark-color);
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
      .auth-hero {
        position: relative;
        width: 100%;
        height: auto;
        min-height: 400px;
      }

      .right-side {
        position: relative;
        width: 100%;
        height: auto;
        margin-left: 0;
        padding: 1.5rem;
      }

      .benefits-grid {
        grid-template-columns: 1fr;
      }

      .form-card {
        padding: 1.5rem;
        box-shadow: none;
        border-radius: 0;
      }

      .section-divider {
        margin: 2rem 0;
      }
    }

    @media (max-width: 767.98px) {
      .hero-content {
        padding: 2rem 1.5rem;
      }

      .right-side {
        padding: 1rem;
      }

      .form-header h2 {
        font-size: 1.5rem;
      }

      .auth-logo-icon {
        font-size: 2rem;
      }

      .auth-logo-text {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <!-- Left Side - Static Content -->


  <div class="auth-hero d-none d-lg-block">
    <div class="bg-overlay h-100">
      <div class="hero-content">
        <div class="text-center mb-4">
          <h1 class="display-5 fw-bold mb-3">PWD Registration Portal</h1>
          <p class="lead opacity-75">Access disability services and support programs designed for you</p>
        </div>
        <div class="benefits-grid">
          <!-- Benefit 1 -->
          <div class="benefit-card">
            <div class="benefit-icon"><i class="fas fa-id-card-alt"></i></div>
            <h4 class="benefit-title">Quick Registration</h4>
            <p class="benefit-description">Simple process to get you verified and access services faster with
              minimal paperwork</p>
          </div>
          <!-- Benefit 2 -->
          <div class="benefit-card">
            <div class="benefit-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <h4 class="benefit-title">Personalized Support</h4>
            <p class="benefit-description">Tailored services based on your specific disability needs and location
            </p>
          </div>
          <!-- Benefit 3 -->
          <div class="benefit-card">
            <div class="benefit-icon"><i class="fas fa-chart-line"></i></div>
            <h4 class="benefit-title">Track Benefits</h4>
            <p class="benefit-description">Monitor your services,
              appointments and support history in one place</p>
          </div>
          <!-- Benefit 4 -->
          <div class="benefit-card">
            <div class="benefit-icon"><i class="fas fa-comments"></i></div>
            <h4 class="benefit-title">Direct Communication</h4>
            <p class="benefit-description">Connect easily with county officers and healthcare providers when
              you need assistance</p>
          </div>
        </div>
        <div class="mt-auto pt-4 text-center">
          <p class="mb-3"><strong>Already registered with us?</strong></p><a href="login.php"
            class="btn btn-outline-light"><i class="fas fa-sign-in-alt me-2"></i>Login to Your Account </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Right Side - Scrollable Form -->
  <div class="right-side">
    <div class="form-card">
      <div class="auth-logo"><i class="fas fa-wheelchair-motion auth-logo-icon"></i><span class="auth-logo-text">PWD
          County</span></div>
      <div class="form-header">
        <h2>Create Your Account</h2>
        <p>Register to access all county disability services</p>
      </div>
      <form method="POST" action="" id="registerForm">
        <!-- Personal Information -->
        <div class="row g-3 mb-4">
          <div class="col-md-6"><label class="form-label">Full Name</label>
            <div class="input-group mb-3">
              <!-- <span class="input-group-text"><i class="fas fa-user"></i></span> -->
              <input type="text" class="form-control" name="name" placeholder="John Doe" required>
            </div>
          </div>
          <div class="col-md-6"><label class="form-label">Email Address</label>
            <div class="input-group mb-3">
              <!-- <span class="input-group-text"><i class="fas fa-envelope"></i></span> -->
              <input type="email" class="form-control" name="email" placeholder="your@email.com" required>
            </div>
          </div>
          <div class="col-md-4"><label class="form-label">Date of Birth</label>
            <div class="input-group mb-3">
              <!-- <span class="input-group-text"><i class="fas fa-calendar"></i></span> -->
              <input type="date" class="form-control" name="dob" required>
            </div>
          </div>
          <div class="col-md-4"><label class="form-label">Gender</label><select class="form-select" name="gender"
              required>
              <option value="" disabled selected>Select gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select></div>



          <div class="col-md-4"><label class="form-label">Mobile Number</label>
            <div class="input-group"><span class="input-group-text"><i class="fas fa-phone"></i></span><input type="tel"
                class="form-control" name="mobileNumber" placeholder="0712345678" required></div>
          </div>
        </div>
        <div class="section-divider"><span>Additional Information</span></div>
        <!-- Additional Info -->
        <div class="row g-3 mb-4">
          <div class="col-md-6"><label class="form-label">Occupation</label><input type="text" class="form-control"
              name="occupation" placeholder="Your profession" required></div>
          <div class="col-md-6"><label class="form-label">National ID</label><input type="text" class="form-control"
              name="id_number" placeholder="National ID number" required></div><input type="hidden" name="type"
            value="pwd">
        </div>

        <div class="row g-3 mb-4">
          <!-- </div> -->
          <div class="fform-group col-md-6">
            <label>Marital Status</label>
            <select class="form-control selectric form-select" name="maritalStatus" required>
              <option value="">Select status</option>
              <option value="single">Single</option>
              <option value="married">Married</option>
              <option value="divorced">Divorced</option>
              <option value="widowed">Widowed</option>
            </select>
          </div>

          <div class="fform-group col-md-6">
            <label for="educationLevel">Education Level</label>
            <select id="educationLevel" class="form-control selectric form-select" name="educationLevel" required>
              <option value="No Formal">No Formal Education</option>
              <option value="Primary">Primary</option>
              <option value="Secondary">Secondary </option>
              <option value="Diploma">Diploma</option>
              <option value="Bachelor">Bachelor's Degree</option>
              <option value="Master">Master's Degree</option>
              <option value="Doctorate">Doctorate</option>
            </select>
          </div>
        </div>

        <!-- Next of Kin -->
        <div class="section-divider"><span>Next of Kin Details</span></div>
        <div class="row g-3 mb-4">
          <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" class="form-control"
              name="nextOfKinName" placeholder="Kin's full name" required></div>
          <div class="col-md-6"><label class="form-label">Mobile Number</label><input type="tel" class="form-control"
              name="nextOfKinMobile" placeholder="0712345678" required></div>
          <div class="col-md-6"><label class="form-label">Relationship</label><select class="form-select"
              name="nextOfKinRelationship" required>
              <option value="" disabled selected>Select relationship</option>
              <option value="Parent">Parent</option>
              <option value="Spouse">Spouse</option>
              <option value="Sibling">Sibling</option>
              <option value="Relative">Relative</option>
              <option value="Friend">Friend</option>
            </select></div>
        </div>
        <!-- Location -->
        <div class="section-divider"><span>Location Information</span></div>
        <div class="row g-3 mb-4">
          <div class="col-md-6"><label class="form-label">County</label><select class="form-select" name="county_id"
              required>
              <option value="" disabled selected>Select your county</option>
              <?php
              include 'files/db_connect.php';
              $counties = mysqli_query($conn, "SELECT id, county_name FROM counties ORDER BY county_name");
              while ($row = mysqli_fetch_assoc($counties)) {
                echo "<option value='{$row['id']}'>{$row['county_name']}</option>";
              }
              ?>
            </select></div>
          <div class="col-md-6"><label class="form-label">Subcounty</label><input type="text" class="form-control"
              name="subcounty" placeholder="Your subcounty" required></div>
        </div>
        <!-- Account Security -->
        <div class="section-divider"><span>Account Security</span></div>
        <div class="row g-3 mb-4">
          <div class="col-md-6"><label class="form-label">Password</label>
            <div class="password-container"><input id="password" type="password" class="form-control" name="password"
                placeholder="Create password (min 8 chars)" required><i class="password-toggle fas fa-eye"
                onclick="togglePassword('password')"></i></div><small class="text-muted">Use at least 8 characters
              with
              numbers and symbols</small>
          </div>
          <div class="col-md-6"><label class="form-label">Confirm Password</label>
            <div class="password-container"><input id="password2" type="password" class="form-control"
                name="confirm_password" placeholder="Confirm your password" required><i
                class="password-toggle fas fa-eye" onclick="togglePassword('password2')"></i></div>
          </div>
        </div>
        <div class="d-grid mt-5"><button type="submit" class="btn btn-submit"><i
              class="fas fa-user-plus me-2"></i>Complete
            Registration </button></div>
      </form>
      <div class="text-center mt-4 pt-3">
        <p class="text-muted">Already have an account? <a href="login.php" class="text-primary fw-bold">Sign in
            here</a></p>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script> // Password toggle functionality

    function togglePassword(fieldId) {
      const passwordField = document.getElementById(fieldId);
      const toggleIcon = passwordField.nextElementSibling;

      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
      }

      else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
      }
    }

    // Form input enhancements
    document.querySelectorAll('.form-control, .form-select').forEach(input => {

      // Add focus styling
      input.addEventListener('focus', function () {
        this.parentElement.classList.add('input-focused');
      });

      input.addEventListener('blur', function () {
        this.parentElement.classList.remove('input-focused');
      });

      // Add filled class when has value
      if (input.value) {
        input.classList.add('filled');
      }

      input.addEventListener('input', function () {
        if (this.value) {
          this.classList.add('filled');
        }

        else {
          this.classList.remove('filled');
        }
      });
    });

    // Handle form submission
    document.getElementById('registerForm').addEventListener('submit', function (e) {
      // Validate password match
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('password2').value;

      if (password !== confirmPassword) {
        e.preventDefault();

        Swal.fire({
          icon: 'error',
          title: 'Password Mismatch',
          text: 'The passwords you entered do not match. Please try again.',
          confirmButtonColor: '#166534'
        });
      }
    });

    // Handle URL parameters for alerts
    document.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);
      const status = urlParams.get('status');

      if (status === 'success') {
        Swal.fire({
          title: 'Registration Successful!',
          text: 'Your account has been created successfully.',
          icon: 'success',
          confirmButtonText: 'Continue',
          confirmButtonColor: '#166534'

        }).then(() => {
          window.location.href = 'login.php';
        });
      }

      else if (status === 'error') {
        Swal.fire({
          icon: 'error',
          title: 'Registration Failed',
          text: 'There was an error processing your registration. Please try again.',
          confirmButtonColor: '#166534'
        });
      }
    });
  </script>
</body>

</html>