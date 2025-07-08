<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>

      <?php

      // Dummy session values for testing (ensure real login sets these)
// $pwdUser = 'Samson';
// $_SESSION['id'] = 2;
// $_SESSION['type'] = 'health-officer';
      
      $official_id = $pwdUser['id'];
      $official_type = $pwdUser['type'];


      // Get hospital_id of logged-in official
      $hospital_id = null;
      $official_sql = "SELECT hospital_id FROM officials WHERE id = $official_id";
      $official_result = mysqli_query($conn, $official_sql);
      if ($official_row = mysqli_fetch_assoc($official_result)) {
        $hospital_id = intval($official_row['hospital_id']);
      } else {
        die("Could not find hospital for official ID $official_id.");
      }

      // // Handle validation action
// if (isset($_GET['validate_id'])) {
//   $validate_id = intval($_GET['validate_id']);
//   $update_sql = "UPDATE assessments SET status = 'validated' WHERE id = $validate_id";
//   mysqli_query($conn, $update_sql);
//   header("Location: test.php");
//   exit;
// }
      
      // echo $hospital_id;
      
      // ✅ Now build the query 

      $sql = "SELECT 
            a.id AS assessment_id,
            u.name AS user_name,
            u.gender,
            u.dob,
            a.user_id,
            u.id,
            u.id_number,
            u.mobile_number,
            u.email,
            c.county_name AS county,
            u.subcounty,
            a.disability_type,
            a.assessment_date,
            a.assessment_time,
            a.status
        FROM assessments a
        JOIN users u ON a.user_id = u.id
        LEFT JOIN counties c ON u.county_id = c.id
        WHERE a.hospital_id = $hospital_id
         AND a.health_officer_id IS NULL
        AND a.medical_officer_id IS NOT NULL";

      // WHERE a.status = 'pending' AND a.hospital_id = $hospital_id";
      
      // ✅ Debug check
// echo "<pre>Running SQL:\n$sql</pre>";
      
      // ✅ Finally run the query
      $result = mysqli_query($conn, $sql);
      if (!$result) {
        die("SQL Error: " . mysqli_error($conn));
      }
      ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Assessment</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="#">New </a></div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Assessment(s)</h2>
            <p class="section-lead">
              Completed Assessment by <?= $pwdUser['name'] ?>
            </p>

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <!-- <h4>New Assessments</h4> -->
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>ID No.</th>
                            <th>Gender</th>
                            <th>D.o.B</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <!-- <th>County</th>
                <th>Sub County</th> -->
                            <th>Disability Type</th>
                            <th>Ass Date</th>
                            <!-- <th>Ass Time</th> -->
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['assessment_id']) ?></td>
                              <td><?= htmlspecialchars($row['user_name']) ?></td>
                              <td><?= htmlspecialchars($row['id_number']) ?></td>
                              <td><?= htmlspecialchars($row['gender']) ?></td>
                              <td><?= htmlspecialchars($row['dob']) ?></td>
                              <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                              <td><?= htmlspecialchars($row['email']) ?></td>
                              <!-- <td>< ?= htmlspecialchars($row['county']) ?></td>
                  <td>< ?= htmlspecialchars($row['subcounty']) ?></td> -->
                              <td><?= htmlspecialchars($row['disability_type']) ?></td>
                              <td><?= htmlspecialchars($row['assessment_date']) ?></td>
                              <!-- <td>< ?= htmlspecialchars($row['assessment_time']) ?></td> -->
                              <td>
                                <form method="get" action="view_assessment">
                                  <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                   <input type="hidden" name="from" value="assessment">
                                   <input type="hidden" name="type" value="<?= $row['disability_type']?>">
                                  <button type="submit" class="btn btn-success btn-sm">View</button>
                                </form>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </section>
      </div>




      <?php include 'files/footer.php'; ?>