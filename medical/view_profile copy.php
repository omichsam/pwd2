<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php';

      $user_id = $pwdUser['id'];

      $query = "SELECT * FROM officials WHERE id = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $user = mysqli_fetch_assoc($result);
      ?>


      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>View Profile</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">View Profile</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row ">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?= htmlspecialchars($pwdUser['name']); ?></h2>
                <p class="section-lead">
                  View information about yourself on this page.
                </p>
              </div>
              <div class="col-md-4 text-right mt-4">
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile </a>
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
                    <div class="profile-widget-name"> <?= htmlspecialchars($pwdUser['name']); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 3px solid rgb(0, 72, 66);">
                  <form method="post" class="needs-validation p-3" novalidate="">
                    <div class="card">
                      <div class="card-header">
                        <h4>View Profile</h4>
                      </div>

                      <div class="card-body">
                        <div class="form-divider mb-3">
                          <u>Personal Info</u>
                        </div>


                       

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="full_name">Full Name</label>
                            <input id="full_name" type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user['name']) ?>"
                              readonly>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="medical_license">Medical License Number (Optional)</label>
                            <input id="medical_license" type="text" class="form-control" name="medical_license"
                              value="<?= htmlspecialchars($user['license_id']) ?>" readonly>
                          </div>
                        </div>

                        <div class="row">
                          <!-- <div class="form-group col-md-4">
                            <label for="national_id">National ID</label>
                            <input id="national_id" type="number" class="form-control" name="national_id"
                              value="< ?= htmlspecialchars($user['license_id']) ?>" readonly>
                          </div> -->
                          <div class="form-group col-md-6">
                            <label for="mobile_number">Mobile Number</label>
                            <input id="mobile_number" type="text" class="form-control" name="mobile_number"
                              value="<?= htmlspecialchars($user['mobile_number']) ?>" readonly>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="email">E-mail</label>
                            <input id="email" type="email" class="form-control" name="email"
                              value="<?= htmlspecialchars($user['email']) ?>" readonly>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="specialty">Specialty</label>
                            <input id="specialty" type="text" class="form-control" name="specialty" value="<?= htmlspecialchars($user['type']) ?>"
                              readonly>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="county">County of Practice</label>
                            <input id="county" type="text" class="form-control" name="county" value="<?= htmlspecialchars($user['license_id']) ?>" readonly>
                          </div>
                        </div>
                      </div>

                      <div class="card-footer text-right">
                        <a href="edit_profile" class="btn btn-primary">Edit Profile</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
       <!-- #region 
        
       -->


       <?php include 'files/footer.php';?>