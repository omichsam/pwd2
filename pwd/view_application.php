<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <!-- top navigation -->
      <?php include 'files/nav.php'; ?>

      <!-- sidebar -->
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <?php
      // Fetch the latest assessment
      $assessment = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM assessments 
           WHERE user_id = {$pwdUser['id']} 
           ORDER BY id DESC LIMIT 1"
      ));

      $status = $assessment['status'] ?? 'pending';

      // Match progress info
      function getProgressInfo($status)
      {
        return match ($status) {
          'pending' => ['step' => 1, 'label' => 'Pending Medical Officer Review', 'badge' => 'warning'],
          'checked' => ['step' => 2, 'label' => 'Checked by Medical Officer', 'badge' => 'info'],
          'approved_by_health_officer' => ['step' => 3, 'label' => 'Approved by Health Officer', 'badge' => 'primary'],
          'approved_by_county_officer' => ['step' => 4, 'label' => 'Approved by County Officer', 'badge' => 'success'],
          'rejected' => ['step' => 0, 'label' => 'Rejected', 'badge' => 'danger'],
          default => ['step' => 0, 'label' => 'Not Started', 'badge' => 'secondary']
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
              <div class="col-12 text-left">
                <h2 class="section-title">Hi, <?= htmlspecialchars($pwdUser['name']); ?>!</h2>
                <p class="section-lead">Track Your Medical Assessment</p>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-animate" style="border-top: 2px solid rgb(0, 72, 66);">
                  <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-file-alt"></i> View Application Status</h4>
                  </div>

                  <div class="card-body">
                    <?php if ($assessment): ?>
                      <h5>Application ID: <span class="font-weight-bold">#<?= $assessment['id'] ?></span></h5>

                      <!-- Application Status -->
                      <div class="mb-4">
                        <label>Current Status</label>
                        <div class="form-control-plaintext">
                          <span class="badge badge-<?= $progress['badge'] ?> badge-pill animate-bounce">
                            <?= $progress['label'] ?>
                          </span>
                        </div>
                      </div>

                      <!-- Animated Roadmap -->
                      <style>
                        @keyframes pulse {
                          0% {
                            transform: scale(1);
                          }

                          50% {
                            transform: scale(1.1);
                          }

                          100% {
                            transform: scale(1);
                          }
                        }

                        @keyframes progressFill {
                          0% {
                            width: 0;
                          }

                          100% {
                            width: var(--progress-width);
                          }
                        }

                        @keyframes bounce {

                          0%,
                          100% {
                            transform: translateY(0);
                          }

                          50% {
                            transform: translateY(-5px);
                          }
                        }

                        .animate-bounce {
                          animation: bounce 1.5s infinite;
                        }

                        .roadmap-container {
                          position: relative;
                          display: flex;
                          justify-content: space-between;
                          margin: 40px 0;
                          padding-bottom: 20px;
                        }

                        .roadmap-track {
                          position: absolute;
                          top: 20px;
                          left: 0;
                          right: 0;
                          height: 6px;
                          background: #e9ecef;
                          border-radius: 3px;
                          z-index: 1;
                        }

                        .roadmap-progress {
                          position: absolute;
                          top: 20px;
                          left: 0;
                          height: 6px;
                          background: linear-gradient(90deg, #28a745, #76d275);
                          border-radius: 3px;
                          z-index: 2;
                          animation: progressFill 1.5s ease-out forwards;
                          --progress-width:
                            <?= ($progress['step'] / 4) * 100 ?>
                            %;
                        }

                        .roadmap-step {
                          position: relative;
                          z-index: 3;
                          text-align: center;
                          width: 20%;
                        }

                        .step-icon {
                          width: 40px;
                          height: 40px;
                          margin: 0 auto 10px;
                          border-radius: 50%;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background: #e9ecef;
                          color: #6c757d;
                          transition: all 0.3s ease;
                        }

                        .step-icon.completed {
                          background: #28a745;
                          color: white;
                          box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2);
                        }

                        .step-icon.current {
                          background: #007bff;
                          color: white;
                          animation: pulse 2s infinite;
                          box-shadow: 0 0 0 6px rgba(0, 123, 255, 0.2);
                        }

                        .step-label {
                          font-size: 0.8rem;
                          color: #6c757d;
                          transition: all 0.3s ease;
                        }

                        .step-label.completed,
                        .step-label.current {
                          color: #343a40;
                          font-weight: 500;
                        }

                        .status-message {
                          opacity: 0;
                          transform: translateY(20px);
                          animation: fadeInUp 0.8s 0.5s forwards;
                        }

                        @keyframes fadeInUp {
                          to {
                            opacity: 1;
                            transform: translateY(0);
                          }
                        }

                        @media (max-width: 768px) {
                          .step-label {
                            font-size: 0.7rem;
                          }
                        }
                      </style>

                      <div class="roadmap-container">
                        <div class="roadmap-track"></div>
                        <div class="roadmap-progress"></div>

                        <?php
                        $steps = [
                          1 => ['icon' => 'fa-file-upload', 'label' => 'Application Submitted'],
                          2 => ['icon' => 'fa-user-md', 'label' => 'Medical Review'],
                          3 => ['icon' => 'fa-clipboard-check', 'label' => 'Health Officer'],
                          4 => ['icon' => 'fa-stamp', 'label' => 'County Approval']
                        ];

                        foreach ($steps as $stepNum => $step):
                          $isCompleted = $stepNum < $progress['step'] || ($stepNum == 4 && $status == 'approved_by_county_officer');
                          $isCurrent = $stepNum == $progress['step'] && $status != 'rejected' && $status != 'approved_by_county_officer';

                          ?>
                          <div class="roadmap-step">
                            <div
                              class="step-icon <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                              <?php if ($isCompleted): ?>
                                <i class="fas fa-check"></i>
                              <?php elseif ($isCurrent): ?>
                                <i class="fas <?= $step['icon'] ?> fa-spin"></i>
                              <?php else: ?>
                                <i class="fas <?= $step['icon'] ?>"></i>
                              <?php endif; ?>
                            </div>
                            <div
                              class="step-label <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                              <?= $step['label'] ?>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>

                      <!-- Status Message with Animation -->
                      <div class="alert alert-<?= $progress['badge'] ?> status-message text-center">
                        <?php
                        $messages = [
                          'pending' => "Your application is under review by medical officers.",
                          'checked' => "Medical review complete. Waiting for health officer approval.",
                          'approved_by_health_officer' => "Health officer approved. Final county approval pending.",
                          'approved_by_county_officer' => "Congratulations! Your application has been fully approved.",
                          'rejected' => "Application requires modifications. Please check the comments.",
                          'default' => "Your application is being processed."
                        ];

                        echo $messages[$status] ?? $messages['default'];

                        if ($status === 'rejected' && !empty($assessment['comment'])) {
                          echo "<div class='mt-2'><strong>Feedback:</strong> " . htmlspecialchars($assessment['comment']) . "</div>";
                        }
                        ?>
                      </div>

                      <!-- Action Buttons -->
                      <div class="text-center mt-4">
                        <?php if ($status === 'approved_by_county_officer'): ?>
                          <a href="print.php" class="btn btn-success btn-lg animate-pop">
                            <i class="fas fa-download mr-2"></i> Download Certificate
                          </a>
                        <?php elseif ($status === 'rejected'): ?>
                          <a href="edit_application.php?id=<?= $assessment['id'] ?>"
                            class="btn btn-warning btn-lg animate-pop">
                            <i class="fas fa-edit mr-2"></i> Resubmit Application
                          </a>
                        <?php else: ?>
                          <button class="btn btn-info btn-lg animate-pop" onclick="checkStatus()">
                            <i class="fas fa-sync-alt mr-2"></i> Refresh Status
                          </button>
                        <?php endif; ?>
                      </div>

                    <?php else: ?>
                      <div class="alert alert-info text-center">
                        <h4><i class="fas fa-info-circle"></i> No Active Applications</h4>
                        <p>You haven't submitted any disability assessment applications yet.</p>
                        <a href="application" class="btn btn-primary mt-2">
                          <i class="fas fa-plus mr-2"></i> Start New Application
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

      <style>
        .animate-pop {
          animation: pop 0.3s ease-out;
        }

        @keyframes pop {
          0% {
            transform: scale(1);
          }

          50% {
            transform: scale(1.05);
          }

          100% {
            transform: scale(1);
          }
        }

        .card-animate {
          transition: all 0.3s ease;
        }

        .card-animate:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
      </style>

      <script>
        function checkStatus() {
          // Show loading animation
          const btn = document.querySelector('.btn-info');
          btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';
          btn.disabled = true;

          // Simulate API call
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        }

        // Add animation to current step icon
        document.addEventListener('DOMContentLoaded', () => {
          const currentStep = document.querySelector('.step-icon.current');
          if (currentStep) {
            setInterval(() => {
              currentStep.style.boxShadow = '0 0 0 ' + (Math.random() * 4 + 4) + 'px rgba(0, 123, 255, 0.2)';
            }, 1000);
          }
        });
      </script>

      <?php include 'files/footer.php'; ?>