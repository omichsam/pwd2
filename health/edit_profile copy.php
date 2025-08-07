<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Profile &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
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
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>

          </ul>

        </form>
        <ul class="navbar-nav navbar-right">

          <li class="dropdown"><a href="#" data-toggle="dropdown"
              class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
              <div class="d-sm-none d-lg-inline-block">Hi, John Doe</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="features-profile.html" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="index.html" class="border border-3 border-info p-2 bg-primary "
              style="width: 18rem; border-radius: 30px; overflow: hidden;"><b
                class="text-light text-bolder rounded-pill "><i class="fa fa-wheelchair" aria-hidden="true"></i> Pwd
                County</b>
            </a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html" class="text-primary text-bolder rounded-pill border border-3 border-primary p-2"
              style="width: 18rem; border-radius: 30px; overflow: hidden;">
              J + C
            </a>
          </div>
          <ul class="sidebar-menu">
            <!-- <li class="menu-header"><a href="index.html">General Dashboard</a></li> -->
            <li class="menu-header">Medical Officer Dashboard</li>
            <li class="dropdown">
              <a href="index.html" class="nav-link "><i class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>

            <li class="menu-header">Assessment</li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="far fa-eye"></i> <span>Medical Assessment</span></a>
              <ul class="dropdown-menu">
                <li><a href="assessment.html">New</a></li>
                <li><a href="report_assessment.html">Approved</a></li>
                <li hidden><a href="officers_reports.html">Officers Report</a></li>
              </ul>
            </li>


            <li class="menu-header">Account</li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Account</span></a>
              <ul class="dropdown-menu">
                <li><a href="view_profile.html">View Profile</a></li>
                <li><a href="edit_profile.html">Edit Profile</a></li>
                <li><a href="change_password.html">Change Password</a></li>
              </ul>
            </li>


          </ul>

          <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="" class="btn btn-danger btn-lg btn-block btn-icon-split">
              <i class="fas fa-lock"></i> Logout
            </a>
          </div>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Edit Profile</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row ">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, John Doe!</h2>
                <p class="section-lead">
                  Change information about yourself on this page.
                </p>
              </div>
              <div class="col-md-3 text-center" hidden>
                <div class="card profile-widget text-center">
                  <div class="profile-widget-header ">
                    <div class="text-center">
                      <img alt="image" src="../assets/img/avatar/avatar-1.png"
                        class="rounded-circle profile-widget-picture">
                    </div>
                  </div>
                  <div class="profile-widget-description text-right">
                    <div class="profile-widget-name">John Doe
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 2px solid rgb(0, 72, 66);">
                  <form method="post" action="/submit-edit-profile" class="needs-validation p-3" novalidate>
                    <div class="card">
                      <div class="card-header">
                        <h4>Edit Profile</h4>
                      </div>

                      <div class="card-body">
                        <div class="form-divider mb-3">
                          <u>Personal Info</u>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="full_name">Full Name</label>
                            <input id="full_name" type="text" class="form-control" name="full_name" placeholder="Dr. Jane Doe"
                              required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="medical_license">Medical License Number (Optional)</label>
                            <input id="medical_license" type="text" class="form-control" name="medical_license"
                            placeholder="MLC123456">
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-4">
                            <label for="national_id">National ID</label>
                            <input id="national_id" type="number" class="form-control" name="national_id"
                            placeholder="12345678" required>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="mobile_number">Mobile Number</label>
                            <input id="mobile_number" type="text" class="form-control" name="mobile_number"
                            placeholder="+254712345678" required>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="email">E-mail</label>
                            <input id="email" type="email" class="form-control" name="email"
                            placeholder="jane.doe@example.com" required>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="specialty">Specialty</label>
                            <input id="specialty" type="text" class="form-control" name="specialty" value="Orthopedics"
                              required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="county">County of Practice</label>
                            <input id="county" type="text" class="form-control" name="county" placeholder="Nairobi" required>
                          </div>
                        </div>
                      </div>

                      <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        <a href="view_profile.html" class="btn btn-danger">Cancel</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-right">
          Copyright &copy; 2025 <div class="bullet"></div><a href="#"><i class="fa fa-wheelchair"
              aria-hidden="true"></i> PWD County</a>
        </div>

      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>

  <!-- JS Libraies -->
  <script src="../assets/modules/summernote/summernote-bs4.js"></script>

  <!-- Page Specific JS File -->

  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>

</html>