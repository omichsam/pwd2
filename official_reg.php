<?php
include 'files/register_officials.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Official Registration — PWD County</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Custom CSS -->
  <style>
    :root {
      /* --primary: #4361ee; */
       --primary: rgb(25, 140, 83);
      --primary-light: #e6f0ff;
      --secondary: #3f37c9;
      --accent: #4cc9f0;
      --dark: #1a1a2e;
      --light: #f8f9fa;
      --success: rgb(67, 181, 82);
    }

    body {
      background-color: #f5f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
    }

    .auth-container {
      height: 100%;
    }

    .auth-image {
      background: linear-gradient(rgba(0, 181, 115, 0.7), rgba(0, 255, 132, 0.7)),
        url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=880&q=80');
      background-size: cover;
      background-position: center;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
    }

    .auth-image-content {
      max-width: 500px;
    }

    .auth-form {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      background-color: white;
      height: 100%;
      overflow-y: auto;
    }

    .auth-logo {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      text-decoration: none;
      margin-bottom: 2rem;
    }

    .auth-logo i {
      font-size: 2.2rem;
    }

    .form-section {
      margin-bottom: 2rem;
    }

    .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid var(--primary-light);
    }

    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.25rem rgba(9, 161, 95, 0.15);
    }

    .form-label {
      font-weight: 500;
      color: #555;
      margin-bottom: 0.5rem;
    }

    .btn-primary {
      background-color: var(--success);
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      background-color: var(--secondary);
      transform: translateY(-2px);
    }

    .input-group-text {
      background-color: var(--success-light);
      border: 1px solid #e0e0e0;
      color: var(--primary);
    }

    .footer-link {
      color: var(--success);
      text-decoration: none;
      font-weight: 500;
    }

    .password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #999;
    }

    .password-toggle:hover {
      color: var(--primary);
    }

    .password-container {
      position: relative;
    }

    @media (max-width: 992px) {
      .auth-image {
        display: none;
      }

      .auth-form {
        padding: 2rem;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid auth-container">
    <div class="row g-0 h-100 ">
      <!-- Image Section -->
      <div class="col-lg-6 d-none d-lg-block auth-image ">
        <div class="auth-image-content p-4">
          <h2 class="mb-4">Join Our Team of Professionals</h2>
          <p class="mb-4">As an official of PWD County, you'll be part of a dedicated team working to improve services
            for persons with disabilities across our communities.</p>
          <ul class="list-unstyled">
            <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Streamlined registration process</li>
            <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Secure access to all resources</li>
            <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Direct communication channels</li>
          </ul>
        </div>
      </div>

      <!-- Form Section -->
      <div class="col-lg-6 auth-form p-4">
        <div class="p-5"></div>
        <div class="p-5"></div>
        <div class="p-5"></div>
        <div class="p-5"></div>

        <a href="#" class="auth-logo pt-4">
          <i class="fas fa-wheelchair"></i>
          <span>PWD County</span>
        </a>

        <h4 class="mb-4">Official Registration</h4>

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
                  <option value="medical_officer">Medical Officer</option>
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

          <div class="mt-4">
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
              <i class="fas fa-user-plus me-2"></i> Complete Registration
            </button>
          </div>
        </form>

        <div class="text-center mt-4">
          <p class="mb-0">Already registered? <a href="login.php" class="footer-link">Sign in here</a></p>
        </div>

        <div class="text-center mt-4 text-muted small">
          &copy; PWD County <span id="year"></span>. All rights reserved.
        </div>
      </div>
    </div>
  </div>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  <script>
    // Set current year
    document.getElementById('year').textContent = new Date().getFullYear();

    // Password toggle function
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const icon = field.nextElementSibling || field.parentElement.nextElementSibling;

      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }

    // Handle URL parameters for alerts
    document.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);
      const status = urlParams.get('status');

      if (status === 'empty') {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Information',
          text: 'Please fill in all required fields.',
          confirmButtonColor: '#4361ee'
        });
      } else if (status === 'pass_mismatch') {
        Swal.fire({
          icon: 'error',
          title: 'Password Mismatch',
          text: 'The passwords you entered do not match.',
          confirmButtonColor: '#4361ee'
        });
      } else if (status === 'exists') {
        Swal.fire({
          icon: 'info',
          title: 'Account Exists',
          text: 'A user with this license ID or email already exists.',
          confirmButtonColor: '#4361ee'
        });
      } else if (status === 'success') {
        Swal.fire({
          title: 'Registration Successful!',
          text: 'Your official account has been created.',
          icon: 'success',
          confirmButtonText: 'Proceed to Login',
          confirmButtonColor: '#4361ee'
        }).then(() => {
          window.location.href = 'login.php';
        });
      } else if (status === 'fail') {
        Swal.fire({
          icon: 'error',
          title: 'Registration Failed',
          text: 'Could not save your information. Please try again.',
          confirmButtonColor: '#4361ee'
        });
      }
    });
  </script>
</body>

</html>