<?php
include('files/register_user.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — PWD County Portal</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Flatpickr -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <style>
    :root {
      --primary: #2e7d32;
      --primary-light: #e8f5e9;
      --primary-dark: #1b5e20;
      --accent: #69f0ae;
      --dark: #263238;
      --light: #f5f5f5;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      color: var(--dark);
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
      /* Prevent body scrolling */
    }

    .auth-container {
      display: flex;
      height: 100vh;
    }

    .auth-hero {
      flex: 1;
      background: linear-gradient(rgba(46, 125, 50, 0.9), rgba(27, 94, 32, 0.9)),
        url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=880&q=80');
      background-size: cover;
      background-position: center;
      color: white;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow: hidden;
    }

    .auth-form-container {
      flex: 1;
      max-width: 750px;
      padding: 3rem;
      overflow-y: auto;
      /* Enable scrolling for form */
      height: 100vh;
    }

    .auth-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 2rem;
    }

    .auth-logo-icon {
      font-size: 2.5rem;
      color: var(--primary);
    }

    .auth-logo-text {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--primary);
    }

    .form-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      padding: 2.5rem;
    }

    .form-header {
      margin-bottom: 2rem;
    }

    .form-header h2 {
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }

    .form-header p {
      color: #6c757d;
    }

    .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--dark);
    }

    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
    }

    .input-group-text {
      background-color: var(--primary-light);
      color: var(--primary);
      border: 1px solid #e0e0e0;
    }

    .btn-primary {
      background-color: var(--primary);
      border: none;
      padding: 0.75rem;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }

    .section-divider {
      position: relative;
      margin: 2rem 0;
      text-align: center;
      color: #6c757d;
    }

    .section-divider::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background-color: #e0e0e0;
      z-index: 1;
    }

    .section-divider span {
      position: relative;
      z-index: 2;
      background: white;
      padding: 0 1rem;
    }

    @media (max-width: 992px) {
      .auth-container {
        flex-direction: column;
      }

      .auth-hero {
        display: none;
      }

      .auth-form-container {
        max-width: 100%;
        padding: 2rem;
        height: auto;
        overflow-y: visible;
      }
    }
  </style>
</head>

<body>
  <div class="auth-container">
    <!-- Static Hero Section -->
    <div class="auth-hero d-none d-lg-flex">
      <div class="px-4">
        <h1 class="display-5 fw-bold mb-4">Welcome to PWD County</h1>
        <p class="lead mb-5">Join our community to access disability services and support programs designed for you.</p>

        <div class="d-flex align-items-center mb-4">
          <i class="fas fa-check-circle fa-lg me-3" style="color: var(--accent);"></i>
          <span>Easy registration process</span>
        </div>
        <div class="d-flex align-items-center mb-4">
          <i class="fas fa-check-circle fa-lg me-3" style="color: var(--accent);"></i>
          <span>Personalized support services</span>
        </div>
        <div class="d-flex align-items-center mb-4">
          <i class="fas fa-check-circle fa-lg me-3" style="color: var(--accent);"></i>
          <span>Secure and confidential</span>
        </div>
      </div>
    </div>

    <!-- Scrollable Form Section -->
    <div class="auth-form-container">
      <div class="form-card">
        <div class="auth-logo">
          <i class="fas fa-wheelchair auth-logo-icon"></i>
          <span class="auth-logo-text">PWD County</span>
        </div>

        <div class="form-header">
          <h2>Create your account</h2>
          <p>Fill in your details to register with PWD County</p>
        </div>

        <form method="POST" action="" id="registerForm">
          <!-- Personal Information -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="name" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email Address</label>
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" name="email" required>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Date of Birth</label>
              <input type="text" class="form-control datepicker" name="dob" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select class="form-select" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Mobile</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="text" class="form-control" name="mobileNumber" required>
              </div>
            </div>
          </div>

          <div class="section-divider">
            <span>Additional Information</span>
          </div>

          <!-- Additional Info -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Occupation</label>
              <input type="text" class="form-control" name="occupation" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">National ID</label>
              <input type="text" class="form-control" name="id_number" required>
            </div>

            <div class="col-md-6" hidden>
              <label class="form-label">Type</label>
              <input type="text" class="form-control" name="type" value="pwd" readonly>
            </div>
          </div>

          <!-- Next of Kin -->
          <div class="section-divider">
            <span>Next of Kin</span>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="nextOfKinName" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Mobile</label>
              <input type="text" class="form-control" name="nextOfKinMobile" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Relationship</label>
              <select class="form-select" name="nextOfKinRelationship" required>
                <option value="Parent">Parent</option>
                <option value="Relative">Relative</option>
                <option value="Friend">Friend</option>
              </select>
            </div>
          </div>

          <!-- Location -->
          <div class="section-divider">
            <span>Location</span>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">County</label>
              <select class="form-select" name="county_id" required>
                <option value="">Select county</option>
                <?php
                include 'files/db_connect.php';
                $counties = mysqli_query($conn, "SELECT id, county_name FROM counties ORDER BY county_name");
                while ($row = mysqli_fetch_assoc($counties)) {
                  echo "<option value='{$row['id']}'>{$row['county_name']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Subcounty</label>
              <input type="text" class="form-control" name="subcounty" required>
            </div>
          </div>

          <!-- Account Security -->
          <div class="section-divider">
            <span>Account Security</span>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <div class="position-relative">
                <input id="password" type="password" class="form-control" name="password" required>
                <i class="password-toggle fas fa-eye" onclick="togglePassword('password')"></i>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <div class="position-relative">
                <input id="password2" type="password" class="form-control" name="confirm_password" required>
                <i class="password-toggle fas fa-eye" onclick="togglePassword('password2')"></i>
              </div>
            </div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary py-3">
              <i class="fas fa-user-plus me-2"></i> Complete Registration
            </button>
          </div>
        </form>

        <div class="text-center mt-4">
          <p class="text-muted">Already have an account? <a href="login.php" class="text-primary fw-bold">Sign in</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Flatpickr -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <script>
    // Initialize datepicker
    flatpickr(".datepicker", {
      dateFormat: "Y-m-d",
      maxDate: "today"
    });

    // Password toggle
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const icon = field.nextElementSibling;

      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }

    // Form validation
    document.getElementById('registerForm').addEventListener('submit', function (e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('password2').value;

      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match');
      }
    });
  </script>
</body>

</html>