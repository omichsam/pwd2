<?php include 'files/header.php';

$user_id = $pwdUser['id'];

// $query = "SELECT * FROM users WHERE id = ?";
$query = "SELECT u.*, c.county_name 
          FROM users u 
          LEFT JOIN counties c ON u.county_id = c.id 
          WHERE u.id = ?";
$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

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
                <a href="edit_profile" class="btn btn-primary"> Edit Profile </a>
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
                  <!-- #region -->
                  <form method="post" class="needs-validation p-3" novalidate="">
                    <div class="card-header">
                      <h4>View Profile</h4>
                    </div>

                    <div class="form-divider"><u>Personal Info</u></div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="frist_name">Full Name</label>
                        <input id="full_name" type="text" class="form-control" name="frist_name"
                          value="<?= htmlspecialchars($user['name']) ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="last_name">ID Number</label>
                        <input id="last_name" type="text" class="form-control" name="last_name"
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
                        <select class="form-control" name="gender" disabled>
                          <option <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                          <option <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                          <option <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="occupation">Occupation</label>
                        <input id="occupation" type="text" class="form-control" name="occupation"
                          value="<?= htmlspecialchars($user['occupation']) ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="mobile_number">Mobile Number</label>
                        <input id="mobile_number" type="text" class="form-control" name="mobile_number"
                          value="<?= htmlspecialchars($user['mobile_number']) ?>" readonly>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" class="form-control" name="email"
                          value="<?= htmlspecialchars($user['email']) ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="educationLevel">Education Level</label>
                        <select id="educationLevel" class="form-control" name="educationLevel" disabled>
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

                      <div class="form-group col-md-4">
                        <label for="disabilityType">Type of Disability</label>
                        <select id="disabilityType" class="form-control" name="disabilityType" disabled>
                          <option <?= $user['type'] === 'Physical Disabilities' ? 'selected' : '' ?>>Physical Disabilities
                          </option>
                          <option <?= $user['type'] === 'Multiple Disabilities' ? 'selected' : '' ?>>Multiple Disabilities
                          </option>
                          <option <?= $user['type'] === 'Mental/Intellectual' ? 'selected' : '' ?>>Mental/Intellectual
                            Disabilities</option>
                          <option <?= $user['type'] === 'Visual Impairments' ? 'selected' : '' ?>>Visual Impairments
                          </option>
                          <option <?= $user['type'] === 'Hearing Impairments' ? 'selected' : '' ?>>Hearing Impairments
                          </option>
                          <option <?= $user['type'] === 'Progressive Chronic Disorders' ? 'selected' : '' ?>>Progressive
                            Chronic Disorders</option>
                          <option <?= $user['type'] === 'Speech Disabilities' ? 'selected' : '' ?>>Speech Disabilities
                          </option>
                        </select>
                      </div>
                    </div>

                    <div class="form-divider"><u>Next of Kin Information</u></div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="nextOfKinName">Next of Kin Name</label>
                        <input id="nextOfKinName" type="text" class="form-control" name="nextOfKinName"
                          value="<?= htmlspecialchars($user['next_of_kin_name']) ?>" readonly>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="nextOfKinMobile">Next of Kin Mobile Number</label>
                        <input id="nextOfKinMobile" type="text" class="form-control" name="nextOfKinMobile"
                          value="<?= htmlspecialchars($user['next_of_kin_mobile']) ?>" readonly>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="nextOfKinRelationship">Next of Kin Relationship</label>
                        <select id="nextOfKinRelationship" class="form-control" name="nextOfKinRelationship" disabled>
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
                          value="< ?= htmlspecialchars($user['county']) ?>" readonly> -->
                        <input id="county" type="text" class="form-control" name="county"
                          value="<?= htmlspecialchars($user['county_name']) ?>" readonly>
                      </div>


                      <div class="form-group col-md-4">
                        <label for="subcounty">Subcounty</label>
                        <input id="subcounty" type="text" class="form-control" name="subcounty"
                          value="<?= htmlspecialchars($user['subcounty']) ?>" readonly>
                      </div>
                    </div>

                    <div class="card-footer text-right">

                      <a href="edit_profile" class="btn btn-primary" id="editBtnQ"> Edit Profile </a>

                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- <script>
        document.getElementById('editBtn').addEventListener('click', function (e) {
          e.preventDefault();
          const inputs = document.querySelectorAll('input, select');
          inputs.forEach(el => {
            el.removeAttribute('readonly');
            el.removeAttribute('disabled');
          });
        });
      </script> -->

      <?php include 'files/footer.php'; ?>