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
                  'pending' => ['step' => 1, 'label' => 'Pending Medical Officer Review', 'badge' => 'warning', 'icon' => 'fa-hourglass-half'],
                  'checked' => ['step' => 2, 'label' => 'Checked by Medical Officer', 'badge' => 'info', 'icon' => 'fa-user-md'],
                  'approved_by_health_officer' => ['step' => 3, 'label' => 'Approved by Health Officer', 'badge' => 'primary', 'icon' => 'fa-clipboard-check'],
                  'approved_by_county_officer' => ['step' => 4, 'label' => 'Approved by County Officer', 'badge' => 'success', 'icon' => 'fa-award'],
                  'rejected' => ['step' => 0, 'label' => 'Rejected', 'badge' => 'danger', 'icon' => 'fa-exclamation-circle'],
                  default => ['step' => 0, 'label' => 'Not Started', 'badge' => 'secondary', 'icon' => 'fa-file']
              };
          }

          $progress = getProgressInfo($status);
      ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Application Status</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Status Tracking</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="hero text-center bg-primary text-white py-4 rounded-lg mb-4">
                  <h2 class="mb-1">Hello,                                          <?php echo htmlspecialchars($pwdUser['name']); ?>!</h2>
                  <p class="lead mb-0">Track Your Disability Assessment Application</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-<?php echo $progress['badge'] ?>">
                    <i class="fas                                  <?php echo $progress['icon'] ?>"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Current Status</h4>
                    </div>
                    <div class="card-body">
                      <span class="badge badge-<?php echo $progress['badge'] ?> badge-pill p-2">
                        <?php echo $progress['label'] ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-animate">
                  <div class="card-header">
                    <h4><i class="fas fa-road mr-2"></i> Application Journey</h4>
                  </div>

                  <div class="card-body">
                    <?php if ($assessment): ?>
                      <div class="application-meta mb-4">
                        <div class="d-flex justify-content-between flex-wrap">
                          <div class="mb-2">
                            <span class="text-muted">Application ID:</span>
                            <span class="font-weight-bold text-primary ml-1">#<?php echo $assessment['id'] ?></span>
                          </div>
                          <div class="mb-2">
                            <span class="text-muted">Submitted:</span>
                            <span class="font-weight-bold ml-1">
                              <?php echo date('M d, Y', strtotime($assessment['created_at'])) ?>
                            </span>
                          </div>
                          <?php if ($assessment['assessment_date']): ?>
                            <div class="mb-2">
                              <span class="text-muted">Last Update:</span>
                              <span class="font-weight-bold ml-1">
                                <?php echo date('M d, Y', strtotime($assessment['update_time'])) ?>
                              </span>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Enhanced Roadmap Visualization -->
                      <style>
                        .roadmap-container {
                          position: relative;
                          margin: 40px 0;
                          padding: 20px 0;
                        }

                        .roadmap-track {
                          position: absolute;
                          top: 50px;
                          left: 5%;
                          right: 5%;
                          height: 8px;
                          background: #f0f0f0;
                          border-radius: 10px;
                          z-index: 1;
                          box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
                        }

                        .roadmap-progress {
                          position: absolute;
                          top: 50px;
                          left: 5%;
                          height: 8px;
                          background: linear-gradient(90deg, #6777ef, #3abaf4);
                          border-radius: 10px;
                          z-index: 2;
                          transition: width 1.5s ease;
                          width: <?php echo $progress['step'] / 4 * 90?>%;
                          box-shadow: 0 2px 4px rgba(54, 162, 235, 0.3);
                        }

                        .roadmap-steps {
                          display: flex;
                          justify-content: space-between;
                          position: relative;
                          z-index: 3;
                          margin: 0 5%;
                        }

                        .roadmap-step {
                          text-align: center;
                          width: 20%;
                        }

                        .step-marker {
                          width: 60px;
                          height: 60px;
                          margin: 0 auto 15px;
                          border-radius: 50%;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          background: #fff;
                          border: 4px solid #e9ecef;
                          color: #adb5bd;
                          font-size: 24px;
                          transition: all 0.4s ease;
                          box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        }

                        .step-marker.completed {
                          background: #28a745;
                          border-color: #28a745;
                          color: white;
                          transform: scale(1.1);
                        }

                        .step-marker.current {
                          background: #6777ef;
                          border-color: #6777ef;
                          color: white;
                          animation: pulse 2s infinite;
                        }

                        .step-marker.pending {
                          background: #fff;
                          border-color: #e9ecef;
                        }

                        .step-label {
                          font-size: 14px;
                          font-weight: 600;
                          color: #adb5bd;
                          margin-bottom: 5px;
                          transition: all 0.3s ease;
                        }

                        .step-label.completed,
                        .step-label.current {
                          color: #343a40;
                        }

                        .step-date {
                          font-size: 12px;
                          color: #adb5bd;
                          min-height: 18px;
                        }

                        @keyframes pulse {
                          0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(103, 119, 239, 0.7); }
                          70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(103, 119, 239, 0); }
                          100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(103, 119, 239, 0); }
                        }

                        .status-card {
                          border-left: 4px solid #6777ef;
                          animation: fadeIn 0.8s ease;
                          transition: all 0.3s ease;
                        }

                        .status-card:hover {
                          transform: translateY(-3px);
                          box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                        }

                        @keyframes fadeIn {
                          from { opacity: 0; transform: translateY(20px); }
                          to { opacity: 1; transform: translateY(0); }
                        }

                        .action-btn {
                          transition: all 0.3s ease;
                          letter-spacing: 0.5px;
                          font-weight: 600;
                        }

                        .action-btn:hover {
                          transform: translateY(-2px);
                          box-shadow: 0 7px 14px rgba(0,0,0,0.1);
                        }
                      </style>

                      <div class="roadmap-container">
                        <div class="roadmap-track"></div>
                        <div class="roadmap-progress"></div>

                        <div class="roadmap-steps">
                          <?php
                              $steps = [
                                  1 => ['icon' => 'fa-file-upload', 'label' => 'Submitted', 'date' => $assessment['created_at']],
                                  2 => ['icon' => 'fa-user-md', 'label' => 'Medical Review', 'date' => $status == 'checked' ? $assessment['assessment_date'] : ''],
                                  3 => ['icon' => 'fa-clipboard-check', 'label' => 'Health Officer', 'date' => $status == 'approved_by_health_officer' ? $assessment['assessment_date'] : ''],
                                  4 => ['icon' => 'fa-stamp', 'label' => 'County Approval', 'date' => $status == 'approved_by_county_officer' ? $assessment['update_time'] : ''],
                              ];

                              foreach ($steps as $stepNum => $step):
                                  $isCompleted = $stepNum < $progress['step'] || ($stepNum == 4 && $status == 'approved_by_county_officer');
                                  $isCurrent   = $stepNum == $progress['step'] && $status != 'rejected' && $status != 'approved_by_county_officer';
                              ?>
		                            <div class="roadmap-step">
		                              <div class="step-marker		                                                      <?php echo $isCompleted ? 'completed' : '' ?><?php echo $isCurrent ? 'current' : 'pending' ?>">
		                                <i class="fas		                                              <?php echo $isCompleted ? 'fa-check' : $step['icon'] ?>"></i>
		                              </div>
		                              <div class="step-label		                                                     <?php echo $isCompleted ? 'completed' : '' ?><?php echo $isCurrent ? 'current' : '' ?>">
		                                <?php echo $step['label'] ?>
		                              </div>
		                              <div class="step-date">
		                                <?php echo ! empty($step['date']) ? date('M d, Y', strtotime($step['date'])) : '&nbsp;' ?>
		                              </div>
		                            </div>
		                          <?php endforeach; ?>
                        </div>
                      </div>

                      <!-- Status Details Card -->
                      <div class="card status-card mt-4 mb-5">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="mr-3">
                              <i class="fas                                            <?php echo $progress['icon'] ?> fa-3x text-<?php echo $progress['badge'] ?>"></i>
                            </div>
                            <div>
                              <h5 class="card-title mb-1">Status Update</h5>
                              <p class="card-text mb-0">
                                <?php
                                    $messages = [
                                        'pending'                    => "Your application has been received and is currently being reviewed by our medical officers. This process typically takes 3-5 business days.",
                                        'checked'                    => "The medical review is complete. Your application is now awaiting approval from the health officer.",
                                        'approved_by_health_officer' => "Your application has been approved by the health officer and is now in the final stage of county approval.",
                                        'approved_by_county_officer' => "Congratulations! Your disability assessment has been fully approved. You may now download your official certificate.",
                                        'rejected'                   => "Your application requires modifications before it can be processed further. Please review the feedback below and resubmit.",
                                        'default'                    => "Your application is being processed through our system.",
                                    ];

                                    echo $messages[$status] ?? $messages['default'];
                                ?>
                              </p>

                              <?php if ($status === 'rejected' && ! empty($assessment['comment'])): ?>
                                <div class="alert alert-danger mt-3">
                                  <h6><i class="fas fa-comment-dots mr-2"></i>Officer Feedback</h6>
                                  <p class="mb-0"><?php echo htmlspecialchars($assessment['comment']) ?></p>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Action Buttons -->
                      <div class="text-center mt-5">
                        <?php if ($status === 'approved_by_county_officer'): ?>

                        <?php
                            // Get the disability type from the assessment
                            $disability_type = $assessment['disability_type'];

                            // Map disability types to their corresponding print form
                            switch ($disability_type) {
                                case 'Hearing':
                                    $url = 'hearing_print.php';
                                    break;
                                case 'Mental':
                                    $url = 'mental_print.php';
                                    break;
                                case 'Maxillofacial':
                                    $url = 'maxillofacial_print.php';
                                    break;
                                case 'others':
                                    $url = 'others_print.php';
                                    break;
                                default:
                                    $url = 'default_print.php'; // Handle a default case if needed
                                    break;
                            }
                        ?>


                          <!-- <a href="checker.php" class="btn btn-success btn-lg action-btn px-4 py-3">
                            <i class="fas fa-download mr-2"></i> Download Certificate                                                                                      <?php echo $assessment['disability_type']; ?>
                          </a> -->

                        <a href="#" class="btn btn-success btn-lg action-btn px-4 py-3" onclick="window.location.href='<?php echo $url; ?>';">
                        <i class="fas fa-download mr-2"></i> Download Certificate <?php echo $disability_type; ?></a>



                          <a href="#" class="btn btn-outline-primary btn-lg ml-3 px-4 py-3">
                            <i class="fas fa-info-circle mr-2"></i> Next Steps
                          </a>
                        <?php elseif ($status === 'rejected'): ?>
                          <a href="edit_application.php?id=<?php echo $assessment['id'] ?>" class="btn btn-warning btn-lg action-btn px-4 py-3">
                            <i class="fas fa-edit mr-2"></i> Resubmit Application
                          </a>
                          <a href="#" class="btn btn-outline-info btn-lg ml-3 px-4 py-3">
                            <i class="fas fa-question-circle mr-2"></i> Need Help?
                          </a>
                        <?php else: ?>
                          <button class="btn btn-primary btn-lg action-btn px-4 py-3" onclick="checkStatus()">
                            <i class="fas fa-sync-alt mr-2"></i> Refresh Status
                          </button>
                          <a href="#" class="btn btn-outline-secondary btn-lg ml-3 px-4 py-3">
                            <i class="fas fa-envelope mr-2"></i> Contact Support
                          </a>
                        <?php endif; ?>
                      </div>

                    <?php else: ?>
                      <div class="empty-state" data-height="400">
                        <div class="empty-state-icon">
                          <i class="fas fa-file-alt"></i>
                        </div>
                        <h2>No Active Applications Found</h2>
                        <p class="lead">
                          You haven't submitted any disability assessment applications yet.
                        </p>
                        <a href="application" class="btn btn-primary mt-4">
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

      <script>
        function checkStatus() {
          const btn = document.querySelector('.btn-primary');
          const originalHtml = btn.innerHTML;

          // Show loading state
          btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Checking...';
          btn.disabled = true;

          // Simulate API call
          setTimeout(() => {
            // Restore button state
            btn.innerHTML = originalHtml;
            btn.disabled = false;

            // Show toast notification
            Toastify({
              text: "Status is up to date",
              duration: 3000,
              close: true,
              gravity: "top",
              position: "right",
              backgroundColor: "#6777ef",
              stopOnFocus: true
            }).showToast();
          }, 1500);
        }

        // Animate progress bar on load
        document.addEventListener('DOMContentLoaded', () => {
          const progressBar = document.querySelector('.roadmap-progress');
          if (progressBar) {
            // Force reflow to trigger animation
            progressBar.style.width = '0';
            setTimeout(() => {
              progressBar.style.width = '<?php echo($progress['step'] / 4) * 90 ?>%';
            }, 100);
          }
        });
      </script>

      <?php include 'files/footer.php'; ?>