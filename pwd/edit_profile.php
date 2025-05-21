<?php include 'files/header.php';

$user_id = $pwdUser['id'];
$update_success = false; //  prevents "undefined variable" error


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $occupation = mysqli_real_escape_string($conn, $_POST['occupation']);
  $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $educationLevel = mysqli_real_escape_string($conn, $_POST['educationLevel']);
  $nextOfKinName = mysqli_real_escape_string($conn, $_POST['nextOfKinName']);
  $nextOfKinMobile = mysqli_real_escape_string($conn, $_POST['nextOfKinMobile']);
  $nextOfKinRelationship = mysqli_real_escape_string($conn, $_POST['nextOfKinRelationship']);
  // $county = mysqli_real_escape_string($conn, $_POST['county']);
  $county_id = intval($_POST['county_id']);
  $subcounty = mysqli_real_escape_string($conn, $_POST['subcounty']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']); // You missed this in original

  $update_sql = "UPDATE users SET 
    occupation='$occupation',
    mobile_number='$mobile_number',
    email='$email',
    education_level='$educationLevel',
    next_of_kin_name='$nextOfKinName',
    next_of_kin_mobile='$nextOfKinMobile',
    next_of_kin_relationship='$nextOfKinRelationship',
    county_id='$county_id',
    subcounty='$subcounty',
    gender='$gender'
    WHERE id='$user_id'";

  if (mysqli_query($conn, $update_sql)) {
    $update_success = true;
  } else {
    $update_error = mysqli_error($conn);
  }
}
?>


<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php
      // Fetch user data
      // $sql = "SELECT * FROM users WHERE id = '$user_id'";
      $sql = "SELECT u.*, c.county_name FROM users u
        LEFT JOIN counties c ON u.county_id = c.id
        WHERE u.id = '$user_id'";
      $result = mysqli_query($conn, $sql);
      $user = mysqli_fetch_assoc($result);

      if (!$user) {
        echo "<script>
  Swal.fire('Error!', 'User not found.', 'error');
</script>";
        exit;
      }
      ?>

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
              <div class="breadcrumb-item">Edit Profile</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row ">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?= htmlspecialchars($pwdUser['name']) ?></h2>
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
                    <div class="profile-widget-name"><?= htmlspecialchars($pwdUser['name']) ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 2px solid rgb(0, 72, 66);">
                  <form method="POST" class="needs-validation p-3" novalidate="" id="profileForm">
                    <div class="card-header">
                      <h4>View Profile</h4>
                    </div>

                    <div class="form-divider"><u>Personal Info</u></div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="full_name">Full Name</label>
                        <input id="full_name" type="text" class="form-control" name="full_name"
                          value="<?= htmlspecialchars($user['name']) ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="last_name">ID Number</label>
                        <input id="last_name" type="text" class="form-control" name="id_number"
                          value="<?= htmlspecialchars($user['id_number']) ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label>Date Of Birth</label>
                        <input type="text" class="form-control" name="dob" value="<?= htmlspecialchars($user['dob']) ?>"
                          readonly>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-4">
                        <label>Gender</label>
                        <select class="form-control" name="gender">
                          <option <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                          <option <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                          <option <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="occupation">Occupation</label>
                        <input id="occupation" type="text" class="form-control" name="occupation"
                          value="<?= htmlspecialchars($user['occupation']) ?>">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="mobile_number">Mobile Number</label>
                        <input id="mobile_number" type="text" class="form-control" name="mobile_number"
                          value="<?= htmlspecialchars($user['mobile_number']) ?>">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" class="form-control" name="email"
                          value="<?= htmlspecialchars($user['email']) ?>">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="educationLevel">Education Level</label>
                        <select id="educationLevel" class="form-control" name="educationLevel">
                          <option value="No Formal" <?= $user['education_level'] === 'No Formal' ? 'selected' : '' ?>>No
                            Formal</option>
                          <option value="Primary" <?= $user['education_level'] === 'Primary' ? 'selected' : '' ?>>Primary
                          </option>
                          <option value="Secondary" <?= $user['education_level'] === 'Secondary' ? 'selected' : '' ?>>
                            Secondary</option>
                          <option value="Diploma" <?= $user['education_level'] === 'Diploma' ? 'selected' : '' ?>>Diploma
                          </option>
                          <option value="Bachelor" <?= $user['education_level'] === 'Bachelor' ? 'selected' : '' ?>>
                            Bachelor's Degree</option>
                          <option value="Master" <?= $user['education_level'] === 'Master' ? 'selected' : '' ?>>Master's
                            Degree</option>
                          <option value="Doctorate" <?= $user['education_level'] === 'Doctorate' ? 'selected' : '' ?>>
                            Doctorate</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-divider"><u>Next of Kin Information</u></div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="nextOfKinName">Next of Kin Name</label>
                        <input id="nextOfKinName" type="text" class="form-control" name="nextOfKinName"
                          value="<?= htmlspecialchars($user['next_of_kin_name']) ?>">
                      </div>
                      <div class="form-group col-md-4">
                        <label for="nextOfKinMobile">Next of Kin Mobile Number</label>
                        <input id="nextOfKinMobile" type="text" class="form-control" name="nextOfKinMobile"
                          value="<?= htmlspecialchars($user['next_of_kin_mobile']) ?>">
                      </div>
                      <div class="form-group col-md-4">
                        <label for="nextOfKinRelationship">Next of Kin Relationship</label>
                        <select id="nextOfKinRelationship" class="form-control" name="nextOfKinRelationship">
                          <option <?= $user['next_of_kin_relationship'] === 'Parent' ? 'selected' : '' ?>>Parent</option>
                          <option <?= $user['next_of_kin_relationship'] === 'Relative' ? 'selected' : '' ?>>Relative
                          </option>
                          <option <?= $user['next_of_kin_relationship'] === 'Friend' ? 'selected' : '' ?>>Friend</option>
                          <option <?= $user['next_of_kin_relationship'] === 'Son' ? 'selected' : '' ?>>Son</option>
                          <option <?= $user['next_of_kin_relationship'] === 'Daughter' ? 'selected' : '' ?>>Daughter
                          </option>
                          <option <?= $user['next_of_kin_relationship'] === 'Brother' ? 'selected' : '' ?>>Brother</option>
                          <option <?= $user['next_of_kin_relationship'] === 'Sister' ? 'selected' : '' ?>>Sister</option>
                          <option <?= $user['next_of_kin_relationship'] === 'Caretaker' ? 'selected' : '' ?>>Caretaker
                          </option>
                        </select>
                      </div>
                    </div>

                    <div class="form-divider"><u>Location</u></div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="county">County</label>
                        <!-- <input id="county" type="text" class="form-control" name="county"
                        value="< ?=htmlspecialchars($user['county']) ?>"> -->
                          <select id="county_id" name="county_id" class="form-control" required>
                            <option value="">-- Select County --</option>
                            <?php
                            $counties = mysqli_query($conn, "SELECT id, county_name FROM counties ORDER BY county_name");
                            while ($row = mysqli_fetch_assoc($counties)) {
                              $selected = ($user['county_id'] == $row['id']) ? 'selected' : '';
                              echo "<option value='{$row['id']}' $selected>" . htmlspecialchars($row['county_name']) . "</option>";
                            }
                            ?>
                          </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="subcounty">Subcounty</label>
                        <input id="subcounty" type="text" class="form-control" name="subcounty"
                          value="<?= htmlspecialchars($user['subcounty']) ?>">
                      </div>
                    </div>

                    <div class="card-footer text-right">

                      <div class="card-footer text-right">
                        <!-- //onclick="confirmUpdate()" -->
                        <button class="btn btn-primary" type="submit">Save Changes</button>

                      </div>

                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php if (isset($update_success) && $update_success): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
          Swal.fire({
            icon: 'success',
            title: 'Profile Updated!',
            text: 'Your profile information has been successfully updated.',
          });
        </script>
      <?php elseif (isset($update_error)): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            html: '<?= addslashes($update_error) ?>',
          });
        </script>
      <?php endif; ?>


      <?php include 'files/footer.php'; ?>