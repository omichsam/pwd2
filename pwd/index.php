<?php include 'files/header.php';


// Access user ID is stored in session
$user_id = $pwdUser['id']; // Change as needed

$sql = "SELECT status FROM assessments WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $fetched_status);

  if (mysqli_stmt_fetch($stmt)) {
    $status = $fetched_status;
  } else {
    $status = "Application not started";
  }

  mysqli_stmt_close($stmt);
}

//  else {
//   $status = "Error preparing the query.";
// }


$total_hospitals = 0;

$sql = "SELECT COUNT(*) AS total FROM hospitals";
$result = mysqli_query($conn, $sql);

if ($result) {
  $row = mysqli_fetch_assoc($result);
  $total_hospitals = $row['total'];
}


mysqli_close($conn);
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
            <h1>Dashboard</h1>
          </div>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Hospital(s)</h4>
                  </div>
                  <div class="card-body">
                    <?php echo $total_hospitals; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12" hidden>
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>News</h4>
                  </div>
                  <div class="card-body">
                    42
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="far fa-file"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Assessment Progress</h4>
                  </div>
                  <div class="card-body">
                    <?php echo htmlspecialchars($status); ?>
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