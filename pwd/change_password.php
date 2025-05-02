<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php'; ?>


      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Profile</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row ">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?php echo htmlspecialchars($pwdUser['name']); ?>! </h2>
                <p class="section-lead">
                  Update your password on this page.
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

            <div class="container mt-5">
              <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                  <div class="card p-4 text-center" style="border-top: 3px solid rgb(0, 72, 66);">
                    <form method="POST" action="change_password.php">
                      <div class="form-divider mb-3">
                        <u>Change Credentials</u>
                      </div>

                      <div class="form-group">
                        <label for="current_password" class="d-block">Current Password</label>
                        <input id="current_password" type="password" class="form-control pwstrength"
                          data-indicator="pwindicator" name="current_password" required>
                        <div id="pwindicator" class="pwindicator">
                          <div class="bar"></div>
                          <div class="label"></div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="password" class="d-block">New Password</label>
                        <input id="password" type="password" class="form-control pwstrength"
                          data-indicator="pwindicator" name="password" required>
                        <div id="pwindicator" class="pwindicator">
                          <div class="bar"></div>
                          <div class="label"></div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="password2" class="d-block">Confirm New Password</label>
                        <input id="password2" type="password" class="form-control" name="password_confirm" required>
                      </div>

                      <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Make Changes</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>



          </div>
        </section>
      </div>



      <footer class="main-footer">
        <div class="footer-right ">
          Copyright &copy; <?php echo date('Y'); ?>
          <div class="bullet"></div><i class="fa fa-wheelchair" aria-hidden="true"></i> Pwd County
        </div>
        <div class="footer-right">

        </div>
      </footer>
    </div>
  </div>

  <?php include 'files/footer.php'; ?>