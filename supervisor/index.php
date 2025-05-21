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

      // Get total assessments
      $sql_total = "SELECT COUNT(*) AS total FROM assessments";
      $result_total = mysqli_query($conn, $sql_total);
      $total_assessment = mysqli_fetch_assoc($result_total)['total'];

      // Get total hopitals
      $sql_total = "SELECT COUNT(*) AS total FROM hospitals";
      $result_total = mysqli_query($conn, $sql_total);
      $total_hospital = mysqli_fetch_assoc($result_total)['total'];
      

      // $user_id = $pwdUser['id'];

      // $user_sql = "SELECT county_id FROM users WHERE id = $user_id";
      // $user_result = mysqli_query($conn, $user_sql);

      // if ($user_result && mysqli_num_rows($user_result) > 0) {
      //   $user_data = mysqli_fetch_assoc($user_result);
      //   $county_id = $user_data['county_id'];
      // } else {
      //   die("User not found or county not set.");
      // }

      // // Step 2: Get total hospitals in that county
      // $total_hospitals = 0;

      // if ($county_id) {
      //   $hospital_sql = "SELECT COUNT(*) AS total FROM hospitals WHERE county_id = $county_id";
      //   $hospital_result = mysqli_query($conn, $hospital_sql);

      //   if ($hospital_result) {
      //     $hospital_data = mysqli_fetch_assoc($hospital_result);
      //     $total_hospitals = $hospital_data['total'];
      //   }
      // }

      // Get total officers
      $sql_total = "SELECT COUNT(*) AS total FROM officials WHERE `type` != 'county_officer'";
      $result_total = mysqli_query($conn, $sql_total);
      $total_officers = mysqli_fetch_assoc($result_total)['total'];

      // // Get total assessments
//       $sql_total = "SELECT COUNT(*) AS total FROM hospital WHERE hospital_id = $hospital_id";
//       $result_total = mysqli_query($conn, $sql_total);
//       $total = mysqli_fetch_assoc($result_total)['total'];
      
      ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Dashboard</h1>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1 border border-dark">
                <div class="card-icon bg-primary">
                  <i class="fas fa-check-circle p-4 text-center"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-dark"><b>Total Assessment</b></h4>
                  </div>
                  <div class="card-body">
                    <b><?= $total_assessment ?></b>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1 border border-danger">
                <div class="card-icon bg-danger text-light">
                  <!-- <i class="fas fa-ban"></i> -->
                  <i class="fas fa-stethoscope p-4"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-dark"><b>Hospitals</b></h4>
                  </div>
                  <div class="card-body">
                    <b class="text-dark font-weight-600"><?= $total_hospital ?></b>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12 ">
              <div class="card card-statistic-1 border border-warning">
                <div class="card-icon bg-warning">
                  <i class="fas fa-users p-4"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-dark"><b>Medical Practitioners</b></h4>
                  </div>
                  <div class="card-body">
                    <b><?= $total_officers ?></b>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12" hidden>
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Online Users</h4>
                  </div>
                  <div class="card-body">
                    47
                  </div>
                </div>
              </div>
            </div>
          </div>

        </section>
      </div>

      <?php include 'files/footer.php'; ?>