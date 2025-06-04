<?php include 'files/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hospital_id'], $_POST['assessment_date'], $_POST['assessment_time'])) {
  $hospital_id = intval($_POST['hospital_id']);
  $assessment_date = trim($_POST['assessment_date']);
  $assessment_time = trim($_POST['assessment_time']);
  $user_id = intval($pwdUser['id']);
  $status = "Pending";

  // Validate hospital exists
  $hospital_check = mysqli_prepare($conn, "SELECT id FROM hospitals WHERE id = ?");
  mysqli_stmt_bind_param($hospital_check, "i", $hospital_id);
  mysqli_stmt_execute($hospital_check);
  mysqli_stmt_store_result($hospital_check);

  if (mysqli_stmt_num_rows($hospital_check) == 0) {
    $error = "Selected hospital does not exist.";
  } else {
    // Check for existing pending assessment
    $pending_check = mysqli_prepare($conn, "SELECT id FROM assessments WHERE user_id = ? AND status = 'Pending'");
    mysqli_stmt_bind_param($pending_check, "i", $user_id);
    mysqli_stmt_execute($pending_check);
    mysqli_stmt_store_result($pending_check);

    if (mysqli_stmt_num_rows($pending_check) > 0) {
      $error = "You already have a pending assessment.";
    } else {
      // Final insert
      $insert_stmt = mysqli_prepare($conn, "INSERT INTO assessments (hospital_id, assessment_date, assessment_time, status, user_id) VALUES (?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($insert_stmt, "isssi", $hospital_id, $assessment_date, $assessment_time, $status, $user_id);

      if (mysqli_stmt_execute($insert_stmt)) {
        $assessment_id = mysqli_insert_id($conn);
        $success = "Appointment booked successfully! Your assessment ID is #$assessment_id";
      } else {
        $error = "Database insert error: " . mysqli_error($conn);
      }
    }
    mysqli_stmt_close($pending_check);
  }
  mysqli_stmt_close($hospital_check);
}

?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($success) || isset($error)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        icon: '<?= isset($success) ? 'success' : 'error' ?>',
        title: '<?= isset($success) ? 'Success' : 'Error' ?>',
        text: '<?= isset($success) ? addslashes($success) : addslashes($error) ?>',
        confirmButtonColor: '#3085d6',
        timer: 5000,
        timerProgressBar: true
      });
    });
  </script>
<?php endif; ?>


<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php include 'files/nav.php'; ?>
      <?php include 'files/sidebar.php'; ?>

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
                <h2 class="section-title">Hi, <?php echo htmlspecialchars($pwdUser['name']); ?>!</h2>
                <p class="section-lead">Book Medical Assessment</p>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 2px solid rgb(0, 72, 66);">
                  <form method="post" class="p-4" novalidate>
                    <div class="card">
                      <div class="card-body">
                        <div class="form-divider mb-3"><u>Book Assessment</u></div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="county">County</label>
                            <select id="county" name="county_id" class="form-control" required>
                              <option value="">-- Select County --</option>
                              <?php
                              $query = "SELECT DISTINCT c.id, c.county_name 
                                        FROM counties c
                                        JOIN hospitals h ON c.id = h.county_id
                                        ORDER BY c.county_name";
                              $result = mysqli_query($conn, $query);
                              while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['county_name']) . "</option>";
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
                            <input type="date" name="assessment_date" class="form-control" required
                              min="<?php echo date('Y-m-d'); ?>">
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

                  <!-- jQuery for AJAX -->
                  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                  <script>
                    $('#county').change(function () {
                      const county_id = $(this).val();
                      if (county_id) {
                        $.post("", { action: "fetch_hospitals", county_id: county_id }, function (data) {
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
        <!-- done -->
      </div>

      <?php include 'files/footer.php'; ?>