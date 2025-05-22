<?php
include 'files/register_officials.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PWD County - Official Portal</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    .bg-overlay {
      position: relative;
      background-size: cover;
      background-position: center;
      height: 100%;
    }

    .bg-overlay::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom right, #0f2921, #1a3a2e);
      opacity: 0.95;
    }

    .auth-container {
      min-height: 100vh;
    }

    .content-wrapper {
      position: relative;
      z-index: 1;
      height: auto;
      padding: 3rem;
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      background-color: rgba(34, 197, 94, 0.2);
    }

    .btn-primary {
      background-color: #166534;
      border-color: #14532d;
    }

    .btn-primary:hover {
      background-color: #14532d;
      border-color: #134827;
    }

    .nav-tabs .nav-link {
      color: #6c757d;
      border: none;
      padding: 0.5rem 1rem;
    }

    .nav-tabs .nav-link.active {
      color: #166534;
      font-weight: 500;
      border-bottom: 2px solid #166534;
    }

    .form-control:focus {
      border-color: #86efac;
      box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.25);
    }

    .badge-icon {
      width: 80px;
      height: 80px;
    }
  </style>
</head>

<body>
  <div class="container-fluid auth-container p-0">
    <div class="row g-0">
      <!-- Left Side - Official Registration Benefits -->
      <div class="col-lg-6 d-none d-lg-block d-none d-lg-flex">
        <div class="bg-overlay h-100"
          style="background-image: url('https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
          <div class="content-wrapper d-flex flex-column h-100 text-white">
            <div class="text-center mb-5">
              <div
                class="badge-icon rounded-circle bg-success d-flex align-items-center justify-content-center mx-auto mb-3">
                <i class="fas fa-id-card-alt text-white fs-3"></i>
              </div>
              <h1 class="display-5 fw-bold mb-3">Official Registration Portal</h1>
              <p class="lead opacity-75">Streamlined access for government officials and service providers</p>
            </div>

            <div class="mb-auto">
              <h3 class="h4 mb-4">Benefits of Official Registration:</h3>

              <div class="row g-4">
                <div class="col-md-6">
                  <div class="feature-icon rounded-circle d-flex align-items-center justify-content-center mb-3">
                    <i class="fas fa-bolt text-success fs-4"></i>
                  </div>
                  <h4 class="h5">Quick Verification</h4>
                  <p class="small opacity-75">Fast-tracked approval process for qualified officials</p>
                </div>

                <div class="col-md-6">
                  <div class="feature-icon rounded-circle d-flex align-items-center justify-content-center mb-3">
                    <i class="fas fa-chart-line text-success fs-4"></i>
                  </div>
                  <h4 class="h5">Performance Analytics</h4>
                  <p class="small opacity-75">Track service delivery metrics and impact</p>
                </div>

                <div class="col-md-6">
                  <div class="feature-icon rounded-circle d-flex align-items-center justify-content-center mb-3">
                    <i class="fas fa-users-cog text-success fs-4"></i>
                  </div>
                  <h4 class="h5">Case Management</h4>
                  <p class="small opacity-75">Efficient tools for managing beneficiary cases</p>
                </div>

                <div class="col-md-6">
                  <div class="feature-icon rounded-circle d-flex align-items-center justify-content-center mb-3">
                    <i class="fas fa-file-alt text-success fs-4"></i>
                  </div>
                  <h4 class="h5">Documentation</h4>
                  <p class="small opacity-75">Secure digital record-keeping system</p>
                </div>
              </div>
            </div>

            <!-- <div class="mt-auto pt-4">
              <div class="d-grid gap-2">
                <button class="btn btn-outline-light btn-lg">
                  <i class="fas fa-file-signature me-2"></i> Register as Official
                </button>
              </div>
              <div class="text-center small mt-3 opacity-75">
                Already registered? <a href="#" class="text-white fw-bold">Verify your status</a>
              </div>
            </div> -->
          </div>
        </div>
      </div>

      <!-- Right Side - Login Form -->
      <div class="col-lg-6 d-flex align-items-center">
        <div class="p-4 p-md-5 w-100">
          <div class="text-center mb-5">
            <h2 class="fw-bold mb-2">Official Login</h2>
            <p class="text-muted">Access the PWD County management system</p>
          </div>

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

            <form id="registerForm" action="" method="POST">
              <!-- Personal Information Section -->
              <div class="form-section">
                <h6 class="section-title">Personal Information</h6>
                <div class="row g-2">
                  <div class="col-md-6">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-user"></i></span>
                      <input id="name" type="text" class="form-control" name="name" required
                        placeholder="Your full name">
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
                <div class="row g-2">
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
                    <select class="form-control" name="type" id="type" required>
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
                    <select class="form-control" name="county_id" required>
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
                <div class="row g-2">
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

              <div class="mt-3 text-center">
                <button type="submit" class="btn btn-primary px-5">
                  <i class="fas fa-user-plus me-2"></i> Complete Registration
                </button>
              </div>


              <div class="text-center small text-muted mt-4 card bg-secondary btn-lg text-light">
                <p class="card-header text-light font-weight-bold"><b>For security reasons, please</b>:</p>
                <ul class="list-inline mb-0 justify-content-around py-3">
                  <li class="list-inline-item text-light"><i class="fas fa-shield-alt  me-1"></i> Use strong passwords
                  </li>
                  <li class="list-inline-item text-light"><i class="fas fa-lock  me-1"></i> Log out after session</li>
                  <li class="list-inline-item text-light"><i class="fas fa-sync-alt me-1"></i> Update credentials
                    regularly</li>
                </ul>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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