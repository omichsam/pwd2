<?php
include 'files/register_officials.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Official Signup — PWD</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <!-- Sweetalert -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="text-center mb-4">
              <a href="#" class="login-brand border border-3 border-info p-2 bg-primary"
                style="border-radius: 10px; overflow: hidden;">
                <b class="text-light rounded-pill">
                  <i class="fas fa-wheelchair" style="font-size: 35px;"></i> PWD County
                </b>
              </a>
            </div>

            <div class="card card-primary">
              <div class="card-header">
                <h6>Official Signup</h6>
              </div>
              <!-- <button onclick="showSuccessAlert()">Show Success Alert</button> -->
              <div class="card-body">
                <form id="registerForm" action="" method="POST">
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="license_id">License ID</label>
                      <input id="license_id" type="text" class="form-control" name="license_id" required>
                      <div class="invalid-feedback">Please fill in License ID!</div>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="name">Name</label>
                      <input id="name" type="text" class="form-control" name="name" required>
                      <div class="invalid-feedback">Please fill in your name!</div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="email">Email</label>
                      <input id="email" type="email" class="form-control" name="email" required>
                      <div class="invalid-feedback">Please fill in your email!</div>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="mobileNumber">Mobile Number</label>
                      <input id="mobileNumber" type="text" class="form-control" name="mobileNumber" required>
                      <div class="invalid-feedback">Please fill in your mobile number!</div>
                    </div>
                  </div>

                  <div class="form-group col-md-12">
                    <label>Type</label>
                    <select class="form-control" name="type" id="type" required>
                      <option value="">Select Type</option>
                      <!-- <option value="pwd">PWD</option> -->
                      <option value="health-officer">Health Officer</option>
                      <option value="medical-officer">Medical Officer</option>
                      <option value="county-officer">County Officer</option>
                    </select>
                    <div class="invalid-feedback">Please select a type!</div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="password">Password</label>
                      <input id="password" type="password" class="form-control" name="password" required>
                      <div class="invalid-feedback">Please fill in your password!</div>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="confirmPassword">Confirm Password</label>
                      <input id="confirmPassword" type="password" class="form-control" name="confirmPassword" required>
                      <div class="invalid-feedback">Please confirm your password!</div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                  </div>
                </form>
              </div>
            </div>

            <div class="mt-5 text-muted text-center">
              Already have an account? <a href="login.php">Login</a>
            </div>

            <div class="simple-footer text-center">
              &copy; PWD <span id="year"></span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script>
    // Set current year
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>
  <!-- JS Files -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>


</body>

</html>