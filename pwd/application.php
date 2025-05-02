<?php include 'files/header.php';

// Handle AJAX hospital fetching
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_hospitals') {
  $county = mysqli_real_escape_string($conn, $_POST['county']);
  $sql = "SELECT id, name, subcounty FROM hospitals WHERE county = '$county'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    echo '<option value="">-- Select Hospital --</option>';
    while ($row = mysqli_fetch_assoc($result)) {
      $id = $row['id'];
      $name = htmlspecialchars($row['name']);
      $subcounty = htmlspecialchars($row['subcounty']);
      echo "<option value='$id'>$name ($subcounty)</option>";
    }
  } else {
    echo '<option value="">No hospitals found</option>';
  }
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hospital_id'], $_POST['assessment_date'], $_POST['assessment_time'])) {
  $hospital_id = isset($_POST['hospital_id']) ? intval($_POST['hospital_id']) : 0;
  $assessment_date = $_POST['assessment_date'];
  $assessment_time = $_POST['assessment_time'];
  $user_id = $pwdUser['id'];
  $status = "Pending";

  $hospital_check = mysqli_query($conn, "SELECT id FROM hospitals WHERE id = '$hospital_id'");
  if (mysqli_num_rows($hospital_check) == 0) {
    $error = "Selected hospital does not exist.";
  } else {


    $check_sql = "SELECT * FROM assessments WHERE user_id = '$user_id' AND status = 'Pending'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
      $error = "You already have a pending assessment";
    } else {

      $sql = "INSERT INTO assessments (hospital_id, assessment_date, assessment_time, status, user_id)
            VALUES ('$hospital_id', '$assessment_date', '$assessment_time', '$status', '$user_id')";

      if (mysqli_query($conn, $sql)) {
        $success = "Appointment booked successfully!";
      } else {
        $error = "Error: " . mysqli_error($conn);
      }
    }
  }
}
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


      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Book Assessment</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row ">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?php echo htmlspecialchars($pwdUser['name']); ?> !</h2>
                <p class="section-lead">
                  Book Medical Assessment
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
                    <div class="profile-widget-name"> <?php echo htmlspecialchars($pwdUser['name']); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 2px solid rgb(0, 72, 66);">

                  <?php if (isset($success) || isset($error)): ?>
                    <div
                      class="alert <?= isset($success) ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show"
                      role="alert" id="flash-message">
                      <?= isset($success) ? $success : $error ?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeFlash()">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <script>
                      // Auto close after 5 seconds
                      setTimeout(() => {
                        const alert = document.getElementById("flash-message");
                        if (alert) {
                          alert.classList.remove("show");
                          alert.classList.add("hide");
                          alert.remove(); // remove from DOM
                        }
                      }, 5000);

                      // Optional manual close fallback
                      function closeFlash() {
                        const alert = document.getElementById("flash-message");
                        if (alert) alert.remove();
                      }
                    </script>
                  <?php endif; ?>


                  <form method="post" class="p-4" novalidate>
                    <div class="card">
                      <div class="card-body">
                        <div class="form-divider mb-3"><u>Book Assessment</u></div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="county">County</label>
                            <select id="county" name="county" class="form-control" required>
                              <option value="">-- Select County --</option>
                              <?php
                              $query = "SELECT DISTINCT county FROM hospitals ORDER BY county";
                              $result = mysqli_query($conn, $query);
                              while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['county'] . "'>" . $row['county'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="hospital_id">Health Facility</label>
                            <select id="hospital_id" name="hospital_id" class="form-control" required>
                              <option value="">-- Select Hospital --</option>
                            </select>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="assessment_date">Assessment Date</label>
                            <input type="date" name="assessment_date" class="form-control" required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="assessment_time">Assessment Time</label>
                            <input type="time" name="assessment_time" class="form-control" required>
                          </div>
                        </div>
                      </div>

                      <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                      </div>
                    </div>
                  </form>



                  <!-- jQuery & AJAX Script -->
                  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                  <script>
                    $('#county').change(function () {
                      let county = $(this).val();
                      if (county) {
                        $.post("", { action: "fetch_hospitals", county: county }, function (data) {
                          $('#hospital_id').html(data);
                        });
                      } else {
                        $('#hospital_id').html('<option value="">-- Select Hospital --</option>');
                      }
                    });
                  </script>

                </div>
              </div>
            </div>
          </div>
        </section>
      </div>




      <?php include 'files/footer.php'; ?>