<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <?php
      // Assuming you've fetched user data and assessment status already
      $assessment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM assessments WHERE user_id = {$pwdUser['id']} ORDER BY id DESC LIMIT 1"));

      $status = $assessment['status'] ?? 'pending';

      function getProgressInfo($status)
      {
        return match ($status) {
          'pending' => ['percent' => 0, 'label' => 'Pending Medical Officer Review', 'badge' => 'warning'],
          'checked' => ['percent' => 33, 'label' => 'Checked by Medical Officer', 'badge' => 'info'],
          'approved_by_health_officer' => ['percent' => 66, 'label' => 'Approved by Health Officer', 'badge' => 'primary'],
          'approved_by_county_officer' => ['percent' => 100, 'label' => 'Approved by County Officer', 'badge' => 'success'],
          'rejected' => ['percent' => 100, 'label' => 'Rejected by Officer', 'badge' => 'danger'],
          default => ['percent' => 0, 'label' => 'Unknown', 'badge' => 'secondary']
        };
      }

      $progress = getProgressInfo($status);
      ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Status</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Status Application</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?= htmlspecialchars($pwdUser['name']); ?>!</h2>
                <p class="section-lead">Book Medical Assessment</p>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card" style="border-top: 2px solid rgb(0, 72, 66);">
                  <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-file-alt"></i> View Application Status</h4>
                  </div>

                  <div class="card-body">

                    <h5>Application ID: <span>#<?= $assessment['id'] ?></span></h5>

                    <!-- Application Status -->
                    <div class="mb-4">
                      <label>Application Status</label>
                      <div class="form-control-plaintext">
                        <span class="badge bg-<?= $progress['badge'] ?>"><?= $progress['label'] ?></span>
                      </div>
                    </div>

                    <!-- Circular Progress -->
                    <div class="text-center mb-4">
                      <div
                        style="width:120px;height:120px;border-radius:50%;margin:auto;background:conic-gradient(#28a745 <?= $progress['percent'] ?>%, #e0e0e0 <?= $progress['percent'] ?>%);display:flex;align-items:center;justify-content:center;">
                        <span style="font-weight:bold;"><?= $progress['percent'] ?>%</span>
                      </div>
                    </div>

                    <!-- Status Message -->
                    <div class="alert alert-info mt-3 text-center">
                      <?php
                      if ($status === 'approved_by_county_officer') {
                        echo "Your application has been fully approved. You may now download your certificate.";
                      } elseif ($status === 'rejected') {
                        echo "Unfortunately, your application has been rejected. Please contact support for more information.";
                      } else {
                        echo "Your application is currently in progress. Please check back for updates.";
                      }
                      ?>
                    </div>

                    <!-- Download Button -->
                    <?php if ($status === 'approved_by_county_officer'): ?>
                      <div class="text-center">
                        <a href="download_certificate.php?id=<?= $assessment['id'] ?>" class="btn btn-success" download>
                          <i class="fas fa-download"></i> Download Certificate
                        </a>
                      </div>
                    <?php endif; ?>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>
      </div>



      <?php include 'files/footer.php'; ?>