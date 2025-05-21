<?php include 'files/header.php'; ?>

<style>
  .text-light {
    color: #fff !important;
  }
</style>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php';
      $officer_id = $pwdUser['id'];
      // echo $officer_id;
// Get hospital ID of the officer
      $sql = "SELECT hospital_id FROM officials WHERE id = $officer_id";
      $result = mysqli_query($conn, $sql);
      $row = mysqli_fetch_assoc($result);
      $hospital_id = $row['hospital_id'];

      // Get total assessments
      $sql_total = "SELECT COUNT(*) AS total FROM assessments WHERE hospital_id = $hospital_id";
      $result_total = mysqli_query($conn, $sql_total);
      $total = mysqli_fetch_assoc($result_total)['total'];

      // Get pending assessments
      $sql_pending = "SELECT COUNT(*) AS pending FROM assessments WHERE hospital_id = $hospital_id AND status = 'pending'";
      $result_pending = mysqli_query($conn, $sql_pending);
      $pending = mysqli_fetch_assoc($result_pending)['pending'];

      // Get processed assessments
      $sql_processed = "SELECT COUNT(*) AS processed FROM assessments WHERE hospital_id = $hospital_id AND medical_officer_id IS NOT NULL";
      $result_processed = mysqli_query($conn, $sql_processed);
      $processed = mysqli_fetch_assoc($result_processed)['processed'];
      ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Dashboard</h1>
          </div>
          <!-- #region -->

          <div class="row">
            <!-- All Assessments -->
            <div class="col-lg-4 col-md-6 col-12">
              <div class="card card-statistic-1 border border-primary bg-primary">
                <div class="card-icon text-light">
                  <i class="fas fa-notes-medical"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-light font-weight-bold">All Assessments</h4>
                  </div>
                  <div class="card-body"><b class="text-light font-weight-bold"><?php echo $total; ?></b></div>
                </div>
              </div>
            </div>

            <!-- Pending Assessments -->
            <div class="col-lg-4 col-md-6 col-12">
              <div class="card card-statistic-1 border border-warning bg-warning">
                <div class="card-icon text-dark">
                  <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-dark">Pending</h4>
                  </div>
                  <div class="card-body"><b class="text-dark"><?php echo $pending; ?></b></div>
                </div>
              </div>
            </div>

            <!-- Processed Assessments -->
            <div class="col-lg-4 col-md-6 col-12">
              <div class="card card-statistic-1 border border-danger bg-danger">
                <div class="card-icon text-light">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-light">Processed</h4>
                  </div>
                  <div class="card-body"><b class="text-light"><?php echo $processed; ?></b></div>
                </div>
              </div>
            </div>
          </div>


        </section>
      </div>


      <?php include 'files/footer.php'; ?>