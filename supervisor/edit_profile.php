<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php'; ?>

      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $official_id = intval($_POST['official_id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $mobile = trim($_POST['mobile_number']);
        $type = $_POST['type'];
        $license_id = trim($_POST['license_id']);
        $id_number = trim($_POST['id_number']);
        $specialisation = trim($_POST['specialisation']);
        $department = trim($_POST['department']);
        $county_id = intval($_POST['county_id']);
        $hospital_id = !empty($_POST['hospital_id']) ? intval($_POST['hospital_id']) : NULL;

        $sql = "UPDATE officials 
            SET name = ?, email = ?, mobile_number = ?, type = ?, license_id = ?, id_number = ?, 
                specialisation = ?, department = ?, county_id = ?, hospital_id = ?
            WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
          "ssssssssiii",
          $name,
          $email,
          $mobile,
          $type,
          $license_id,
          $id_number,
          $specialisation,
          $department,
          $county_id,
          $hospital_id,
          $official_id
        );

        if ($stmt->execute()) {
          echo "<div class='alert alert-success'>Profile updated successfully.</div>";
        } else {
          echo "<div class='alert alert-danger'>Update failed: " . $stmt->error . "</div>";
        }
      }
      ?>


      <?php
      $official_id = $pwdUser['id']; // Or from session if logged-in official
      
      $sql = "SELECT 
          o.id, o.name, o.email, o.mobile_number, o.type, o.license_id,
          o.id_number, o.specialisation, o.department, o.county_id, o.hospital_id,
          c.county_name, h.name AS hospital_name
          FROM officials o
          LEFT JOIN counties c ON o.county_id = c.id
          LEFT JOIN hospitals h ON o.hospital_id = h.id
          WHERE o.id = ?";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $official_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $user = $result->fetch_assoc();
      ?>


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
                <h2 class="section-title">Hi, <?= htmlspecialchars($pwdUser['name']) ?>!</h2>
                <p class="section-lead">
                  Change information about your profile on this page.
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
                    <div class="profile-widget-name"><?= htmlspecialchars($pwdUser['name']) ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 2px solid rgb(0, 72, 66);">
                  <form method="post" action="" class="needs-validation p-3" novalidate>
                    <div class="card">
                      <div class="card-header">
                        <h4>Edit Profile</h4>
                      </div>

                      <div class="card-body">
                        <div class="form-divider mb-3">
                          <u>Personal Info</u>
                        </div>

                        <form method="POST">
                          <input type="hidden" name="official_id" value="<?= $user['id'] ?>">

                          <div class="row">
                            <div class="form-group col-md-6">
                              <label for="full_name">Full Name</label>
                              <input id="full_name" type="text" class="form-control" name="name"
                                placeholder="Dr. Jane Doe" value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                required>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="medical_license">Medical License Number (Optional)</label>
                              <input id="medical_license" type="text" class="form-control" name="license_id"
                                placeholder="MLC123456" value="<?= htmlspecialchars($user['license_id'] ?? '') ?>">
                            </div>
                          </div>

                          <div class="row">
                            <div class="form-group col-md-4">
                              <label for="national_id">National ID</label>
                              <input id="national_id" type="number" class="form-control" name="id_number"
                                placeholder="12345678" value="<?= htmlspecialchars($user['id_number'] ?? '') ?>"
                                required>
                            </div>
                            <div class="form-group col-md-4">
                              <label for="mobile_number">Mobile Number</label>
                              <input id="mobile_number" type="text" class="form-control" name="mobile_number"
                                placeholder="+254712345678"
                                value="<?= htmlspecialchars($user['mobile_number'] ?? '') ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                              <label for="email">E-mail</label>
                              <input id="email" type="email" class="form-control" name="email"
                                placeholder="jane.doe@example.com" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                required>
                            </div>
                          </div>

                          <div class="row">
                            <div class="form-group col-md-4">
                              <label for="type">System Role</label>
                              <select id="type" name="type" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                <option value="medical_officer" <?= $user['type'] == 'medical_officer' ? 'selected' : '' ?>>Medical Officer</option>
                                <option value="health_officer" <?= $user['type'] == 'health_officer' ? 'selected' : '' ?>>
                                  Health Officer</option>
                                <option value="county_officer" <?= $user['type'] == 'county_officer' ? 'selected' : '' ?>>
                                  County Officer</option>
                              </select>
                            </div>

                            <div class="form-group col-md-4">
                              <label for="speciality">Specialisation</label>
                              <input id="speciality" type="text" class="form-control" name="specialisation"
                                placeholder="Orthopedics" value="<?= htmlspecialchars($user['specialisation'] ?? '') ?>"
                                required>
                            </div>

                            <div class="form-group col-md-4">
                              <label for="department">Department</label>
                              <input id="department" type="text" class="form-control" name="department"
                                placeholder="ENT, Disability Services, etc."
                                value="<?= htmlspecialchars($user['department'] ?? '') ?>">
                            </div>
                          </div>

                          <div class="row">
                            <div class="form-group col-md-6">
                              <label for="county">County of Practice</label>
                              <select id="county" name="county_id" class="form-control" required>
                                <option value="">-- Select County --</option>
                                <?php
                                $counties = mysqli_query($conn, "SELECT id, county_name FROM counties ORDER BY county_name");
                                while ($county = mysqli_fetch_assoc($counties)) {
                                  $selected = ($county['id'] == ($user['county_id'] ?? '')) ? 'selected' : '';
                                  echo "<option value='{$county['id']}' $selected>" . htmlspecialchars($county['county_name']) . "</option>";
                                }
                                ?>
                              </select>
                            </div>

                            <div class="form-group col-md-6">
                              <label for="hospital">Attached Hospital (Optional)</label>
                              <select id="hospital" name="hospital_id" class="form-control">
                                <option value="">-- Select Hospital --</option>
                                <?php
                                $hospitals = mysqli_query($conn, "SELECT id, name FROM hospitals ORDER BY name");
                                while ($hospital = mysqli_fetch_assoc($hospitals)) {
                                  $selected = ($hospital['id'] == ($user['hospital_id'] ?? '')) ? 'selected' : '';
                                  echo "<option value='{$hospital['id']}' $selected>" . htmlspecialchars($hospital['name']) . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>


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