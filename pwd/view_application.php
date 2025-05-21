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

      // Fetch assessment status
      $stmt = mysqli_prepare($conn, "SELECT `status` FROM `assessments` WHERE `user_id` = ?");
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $status);
      mysqli_stmt_fetch($stmt);
      mysqli_stmt_close($stmt);

      // Match progress info
      function getProgressInfoo($status)
      {
        return match ($status) {
          'pending' => ['step' => 1, 'label' => 'Pending Medical Officer Review', 'badge' => 'warning'],
          'checked' => ['step' => 2, 'label' => 'Checked by Medical Officer', 'badge' => 'info'],
          'approved_by_health_officer' => ['step' => 3, 'label' => 'Approved by Health Officer', 'badge' => 'primary'],
          'approved_by_county_officer' => ['step' => 4, 'label' => 'Approved by County Officer', 'badge' => 'success'],
          'rejected' => ['step' => 5, 'label' => 'Rejected by Officer', 'badge' => 'danger'],
          default => ['step' => 0, 'label' => 'Not Started', 'badge' => 'secondary']
        };
      }

      $progress1 = getProgressInfoo($status);
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
                <p class="section-lead">Track Your Medical Assessment</p>
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
                        <span class="badge bg-<?= $progress1['badge'] ?>"><?= $progress1['label'] ?></span>
                      </div>
                    </div>



                    <!-- FontAwesome for icons -->
                    <link rel="stylesheet"
                      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

                    <style>
                      .roadmap-wrapper {
                        margin: 40px auto;
                        max-width: 900px;
                        padding: 20px;
                      }

                      .roadmap-container {
                        display: flex;
                        justify-content: space-between;
                        position: relative;
                        flex-wrap: wrap;
                        gap: 10px;
                      }

                      .roadmap-container::before {
                        content: '';
                        position: absolute;
                        top: 28px;
                        left: 0;
                        right: 0;
                        height: 6px;
                        background: #e0e0e0;
                        z-index: 1;
                        border-radius: 5px;
                      }

                      .roadmap-progress {
                        position: absolute;
                        top: 28px;
                        left: 0;
                        height: 6px;
                        background: linear-gradient(to right, #28a745, #76d275);
                        border-radius: 5px;
                        z-index: 2;
                        transition: width 0.5s ease-in-out;
                      }

                      .roadmap-step {
                        flex: 1;
                        min-width: 80px;
                        max-width: 140px;
                        text-align: center;
                        position: relative;
                        z-index: 3;
                        padding-top: 40px;
                      }

                      .roadmap-step .circle {
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        margin: 0 auto;
                        background-color: #e0e0e0;
                        color: white;
                        font-size: 16px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: background 0.4s;
                      }

                      .roadmap-step.active .circle {
                        background: #28a745;
                        box-shadow: 0 0 8px rgba(40, 167, 69, 0.6);
                      }

                      .roadmap-step .label {
                        font-size: 0.75rem;
                        margin-top: 8px;
                        word-wrap: break-word;
                        white-space: normal;
                      }

                      @media (max-width: 600px) {
                        .roadmap-step {
                          flex: 1 0 45%;
                          margin-bottom: 20px;
                        }
                      }
                    </style>

                    <div class="roadmap-wrapper">
                      <div class="roadmap-container">
                        <?php
                        $stages = [
                          'Start Booking',
                          'Pending Medical Officer Review',
                          'Checked by Medical Officer',
                          'Approved by Health Officer',
                          'Approved by County Officer'
                        ];

                        $currentStep = $progress1['step'];
                        $totalStages = count($stages) - 1;
                        $progressPercent = ($currentStep / $totalStages) * 100;
                        ?>

                        <!-- Dynamic Progress Bar -->
                        <div class="roadmap-progress" style="width: <?= $progressPercent ?>%;"></div>

                        <?php foreach ($stages as $index => $label): ?>
                          <div class="roadmap-step <?= $index <= $currentStep ? 'active' : '' ?>"
                            title="<?= htmlspecialchars($label) ?>">
                            <div class="circle">
                              <?php if ($index < $currentStep): ?>
                                <i class="fas fa-check"></i>
                              <?php elseif ($index == $currentStep): ?>
                                <i class="fas fa-spinner fa-spin"></i>
                              <?php else: ?>
                                <?= $index ?>
                              <?php endif; ?>
                            </div>
                            <div class="label"><?= $label ?></div>
                          </div>
                        <?php endforeach; ?>
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
                        <a href="print.php" class="btn btn-success">
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