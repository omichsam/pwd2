<?php
include('files/register_user.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Register &mdash; PWD</title>


  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/modules/bootstrap-daterangepicker/daterangepicker.css">

  <!-- Sweetalert -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">


  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/jquery-selectric/selectric.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-94034622-3');
  </script>
  <!-- /END GA -->
</head>

<body>


  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <!-- <div class="login-brand">
              <img src="assets/img/logo.png" alt="logo" width="200" class="shadow-light rounded-circle">
            </div> -->
            <div class="text-center py-5">
              <a href="#" class="border border-3 border-info p-2 bg-primary "
                style="width: 25rem; border-radius: 10px; overflow: hidden;"><b
                  class="text-light text-bolder rounded-pill "><i class="fas fa-4x fa-wheelchair"></i>
                  PWD County</b>
              </a>
            </div>

            <div class="card card-primary">
              <div class="card-header">
                <h6 class="fw-500">Register</h6>
              </div>

              <div class="card-body">
                <form method="POST" action="" id="registerForm">
                  <div class="form-divider">
                    <u>Personal Info</u>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="full_name">Full Name</label>
                      <input id="full_name" type="text" class="form-control" name="name" autofocus>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="email">E-mail</label>
                      <input id="" type="email" class="form-control" name="email">
                      <div class="invalid-feedback">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <div class="form-group">
                        <label>Date Of Birth</label>
                        <input type="text" name="dob" class="form-control datepicker">
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label>Gender</label>
                      <select class="form-control selectric" name="gender">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                      </select>
                    </div>

                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="last_name">Occupation</label>
                      <input id="occupation" name="occupation" type="text" class="form-control" name="occupation">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="last_name">Mobile Number</label>
                      <input id="mobile_number" name="mobileNumber" type="text" class="form-control phone-number"
                        placeholder="+25470000000" name="mobile_number">
                    </div>
                  </div>

                  <div class="row" hidde>
                    <div class="form-group col-md-6">
                      <label for="type">Type</label>
                      <input id="type" type="text" class="form-control" name="type" value="pwd" readonly>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="type">ID Number</label>
                      <input id="id_number" type="text" max="8" class="form-control" name="id_number">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Marital Status</label>
                      <select class="form-control selectric" name="maritalStatus" required>
                        <option value="">Select status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                      </select>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="educationLevel">Education Level</label>
                      <select id="educationLevel" class="form-control selectric" name="educationLevel" required>
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
                  <!-- <div class="form-group" hidden>
                    <label for="disabilityType">Type of Disability</label>
                    <select id="disabilityType" class="form-control" name="disabilityType" required>
                      <option value="Physical Disabilities">Physical Disabilities</option>
                      <option value="Multiple Disabilities">Multiple Disabilities</option>
                      <option value="Mental/Intellectual">Mental/Intellectual Disabilities</option>
                      <option value="Visual Impairments">Visual Impairments</option>
                      <option value="Hearing Impairments">Hearing Impairments</option>
                      <option value="Progressive Chronic Disorders">Progressive Chronic Disorders</option>
                      <option value="Speech Disabilities">Speech Disabilities</option>
                    </select>
                  </div> -->

                  <!-- next of kin info -->
                  <div class="form-divider">
                    <u>Next of Kin Information</u>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="nextOfKinName">Next of Kin Name</label>
                      <input id="nextOfKinName" type="text" class="form-control" name="nextOfKinName"
                        placeholder="Jane Doe">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="nextOfKinMobile">Next of Kin Mobile Number</label>
                      <input id="nextOfKinMobile" type="text" class="form-control" name="nextOfKinMobile"
                        placeholder="+25470000000">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="nextOfKinRelationship">Next of Kin Relationship</label>
                      <select id="nextOfKinRelationship" class="form-control" name="nextOfKinRelationship" required>
                        <option value="Parent">👨‍👩‍👧‍👦 Parent</option>
                        <option value="Relative">👪 Relative</option>
                        <option value="Friend">🤝 Friend</option>
                        <option value="Son">👦 Son</option>
                        <option value="Daughter">👧 Daughter</option>
                        <option value="Brother">👨 Brother</option>
                        <option value="Sister">👩 Sister</option>
                        <option value="Caretaker">👩‍⚕️ Caretaker</option>
                      </select>
                      <small class="form-text text-muted">Select your next of kin's relationship to you.</small>
                    </div>
                  </div>
                  <!-- end of next of kin info  -->

                  <!-- Location  -->
                  <div class="form-divider">
                    <u>Location</u>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="county">County</label>
                      <input id="county" type="text" class="form-control" name="county" placeholder="Enter your county"
                        required>
                      <small class="form-text text-muted">Type the name of your county.</small>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="subcounty">Subcounty</label>
                      <input id="subcounty" type="text" class="form-control" name="subcounty"
                        placeholder="Enter your subcounty" required>
                      <small class="form-text text-muted">Type the name of your subcounty within the selected
                        county.</small>
                    </div>
                  </div>
                  <!-- End of location  -->
                  <div class="form-divider">
                    <u>Set Credentials</u>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label for="password" class="d-block">Password</label>
                      <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator"
                        name="password">
                      <div id="pwindicator" class="pwindicator">
                        <div class="bar"></div>
                        <div class="label"></div>
                      </div>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="password2" class="d-block">Password Confirmation</label>
                      <input id="password2" type="password" class="form-control" name="confirm_password">
                    </div>
                  </div>



                  <div class="form-group" hidden>
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="agree" class="custom-control-input" id="agree">
                      <label class="custom-control-label" for="agree">I agree with the terms and conditions</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                      Register
                    </button>
                  </div>
                </form>

                <div class="mt-5 text-muted text-center">
                  Already have an account? <a href="login.php">Login</a>
                </div>

              </div>
            </div>

            <div class="simple-footer">
              Copyright &copy; <b>PWD</b>
              <x id="demo"></x>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <script>
    const d = new Date();
    let year = d.getFullYear();
    document.getElementById("demo").innerHTML = year;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>


  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>

  <!-- JS Libraies -->
  <script src="assets/modules/jquery-pwstrength/jquery.pwstrength.min.js"></script>
  <script src="assets/modules/jquery-selectric/jquery.selectric.min.js"></script>
  <script src="assets/modules/bootstrap-daterangepicker/daterangepicker.js"></script>

  <!-- Page Specific JS File -->
  <script src="assets/js/page/auth-register.js"></script>

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

</body>

</html>