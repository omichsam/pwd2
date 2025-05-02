<?php 
include 'files/login.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; PWD</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <!-- <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4"> -->
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">

            <div class="text-center mb-4">
              <a href="index.html" class="login-brand border border-3 border-info p-2 bg-primary"
                style="border-radius: 10px; overflow: hidden;">
                <b class="text-light text-bolder rounded-pill">
                  <i class="fas fa-wheelchair" style="font-size: 35px;"></i> PWD County
                </b>
              </a>
            </div>

            <div class="card card-primary">
              <div class="card-header">
                <h4>Login</h4>
              </div>

              <div class="card-body">
                <form method="" action="" id="loginForm" class="needs-validation" novalidate>


                 <!-- User type selection -->
                   <div class="form-group">
                    <label>Type</label>
                    <select id="type" class="form-control selectric" name="type" onchange="toggleFields()" required>
                      <option value="">-- Select Type --</option>
                      <option value="PWD">PWD</option>
                      <option value="Health Officer">Health Officer</option>
                      <option value="Medical Officer">Medical Officer</option>
                      <option value="County Officer">County Officer</option>
                    </select>
                    <div class="invalid-feedback">
                      Please select your user type!
                    </div>
                  </div>

                  <!-- ID Number field (only for PWD) -->
                  <div class="form-group" id="idNumberGroup">
                    <label for="idNo">ID Number</label>
                    <input id="idNo" type="text" class="form-control" name="id_number" tabindex="1">
                    <div class="invalid-feedback">
                      Please fill in ID Number!
                    </div>
                  </div> 

                  <!-- License Number field (for all others) -->
                  <div class="form-group" id="licenseNumberGroup" style="display: none;">
                    <label for="licenseNo">License Number</label>
                    <input id="licenseNo" type="text" class="form-control" name="license_id" tabindex="1">
                    <div class="invalid-feedback">
                      Please fill in License Number!
                    </div>
                  </div>   

                  <!-- Password -->
                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                      Please fill in your password!
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>

                <!-- JavaScript to toggle fields -->
                <script>
                  function toggleFields() {
                    const userType = document.getElementById("type").value;
                    const idGroup = document.getElementById("idNumberGroup");
                    const licenseGroup = document.getElementById("licenseNumberGroup");

                    if (userType === "PWD") {
                      idGroup.style.display = "block";
                      licenseGroup.style.display = "none";
                      document.getElementById("idNo").required = true;
                      document.getElementById("licenseNo").required = false;
                    } else {
                      idGroup.style.display = "none";
                      licenseGroup.style.display = "block";
                      document.getElementById("idNo").required = false;
                      document.getElementById("licenseNo").required = true;
                    }
                  }

                  // Trigger the toggle once on page load if needed (e.g., after back navigation)
                  window.addEventListener('DOMContentLoaded', toggleFields);
                </script>

              </div>
            </div>

            <!-- <div class="mt-5 text-muted text-center">
              Don't have an account? <a href="register.php">PWD Applicant</a> | <a href="official_reg.php">Officials</a>
            </div> -->

            <div class="mt-5 text-muted text-center">
              Create account for <a href="register.php" class="btn btn-info">PWD Applicant</a>
              <nbsp>|</nbsp> <a href="official_reg.php" class="btn btn-dark">Officials</a>
            </div>

            <div class="simple-footer">
              Copyright &copy;
              <b>PWD</b>
              <span id="currentYear"></span>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>

  <script>
    document.getElementById("currentYear").textContent = new Date().getFullYear();

  </script>

  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>

</html>