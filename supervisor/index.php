<?php include 'files/header.php'; ?>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <!-- Top Navigation -->
      <?php include 'files/nav.php'; ?>

      <!-- Sidebar -->
      <?php include 'files/sidebar.php'; ?>

      <?php
      // Get county officer's county
      $officer_id = $pwdUser['id'];
      $county_sql = "SELECT county_id FROM officials WHERE id = $officer_id";
      $county_result = mysqli_query($conn, $county_sql);
      $county_data = mysqli_fetch_assoc($county_result);
      $county_id = $county_data['county_id'];

      // Get county name
      $county_name_sql = "SELECT county_name FROM counties WHERE id = $county_id";
      $county_name_result = mysqli_query($conn, $county_name_sql);
      $county_name = mysqli_fetch_assoc($county_name_result)['county_name'];

      // County-wide statistics
      $stats = [
        'total_assessments' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id"),
        'pending' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id AND a.status = 'pending'"),
        'medical_review' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id AND a.status = 'checked'"),
        'health_approved' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id AND a.status = 'approved_by_health_officer'"),
        'fully_approved' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id AND a.status = 'approved_by_county_officer'"),
        'rejected' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id AND a.status = 'rejected'"),
        'hospitals' => getCount("SELECT COUNT(*) FROM hospitals WHERE county_id = $county_id"),
        'medical_officers' => getCount("SELECT COUNT(*) FROM officials WHERE county_id = $county_id AND type = 'medical_officer'"),
        'health_officers' => getCount("SELECT COUNT(*) FROM officials WHERE county_id = $county_id AND type = 'health_officer'"),
        'today_assessments' => getCount("SELECT COUNT(*) FROM assessments a JOIN hospitals h ON a.hospital_id = h.id WHERE h.county_id = $county_id AND DATE(a.created_at) = CURDATE()"),
        'avg_processing_time' => getAvgProcessingTime($county_id)
      ];

      // Disability type distribution
      $disability_stats = getDisabilityStats($county_id);

      // Hospital performance
      $hospital_performance = getHospitalPerformance($county_id);

      // Recent assessments needing county approval
      $pending_approvals = getPendingApprovals($county_id);

      // Helper functions
      function getCount($sql)
      {
        global $conn;
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_array($result)[0];
      }

      function getAvgProcessingTime($county_id)
      {
        global $conn;
        $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, a.created_at, a.assessment_date)) 
                  FROM assessments a 
                  JOIN hospitals h ON a.hospital_id = h.id 
                  WHERE h.county_id = $county_id AND a.status = 'approved_by_county_officer'";
        $result = mysqli_query($conn, $sql);
        return round(mysqli_fetch_array($result)[0], 1);
      }

      function getDisabilityStats($county_id)
      {
        global $conn;
        $sql = "SELECT a.disability_type, COUNT(*) as count 
                  FROM assessments a 
                  JOIN hospitals h ON a.hospital_id = h.id 
                  WHERE h.county_id = $county_id 
                  GROUP BY a.disability_type 
                  ORDER BY count DESC";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
      }

      function getHospitalPerformance($county_id)
      {
        global $conn;
        $sql = "SELECT h.id, h.name, 
                         COUNT(a.id) as total_assessments,
                         SUM(CASE WHEN a.status = 'approved_by_county_officer' THEN 1 ELSE 0 END) as approved,
                         AVG(TIMESTAMPDIFF(HOUR, a.created_at, a.assessment_date)) as avg_time
                  FROM hospitals h
                  LEFT JOIN assessments a ON h.id = a.hospital_id
                  WHERE h.county_id = $county_id
                  GROUP BY h.id, h.name
                  ORDER BY total_assessments DESC
                  LIMIT 5";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
      }

      function getPendingApprovals($county_id)
      {
        global $conn;
        $sql = "SELECT a.id, a.created_at, u.name as user_name, h.name as hospital_name,
                         TIMESTAMPDIFF(DAY, a.created_at, NOW()) as days_pending
                  FROM assessments a
                  JOIN users u ON a.user_id = u.id
                  JOIN hospitals h ON a.hospital_id = h.id
                  WHERE h.county_id = $county_id AND a.status = 'approved_by_health_officer'
                  ORDER BY a.created_at ASC
                  LIMIT 5";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
      }
      ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>County Dashboard</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($county_name) ?></div>
            </div>
          </div>

          <!-- County Overview Cards -->
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-file-medical"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Assessments</h4>
                  </div>
                  <div class="card-body">
                    <?= $stats['total_assessments'] ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-hospital"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Hospitals</h4>
                  </div>
                  <div class="card-body">
                    <?= $stats['hospitals'] ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                  <i class="fas fa-user-md"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Medical Officers</h4>
                  </div>
                  <div class="card-body">
                    <?= $stats['medical_officers'] ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Pending Approvals</h4>
                  </div>
                  <div class="card-body">
                    <?= $stats['health_approved'] ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Assessment Status Breakdown -->
          <div class="row">
            <div class="col-lg-8">
              <div class="card">
                <div class="card-header">
                  <h4>Assessment Status Overview</h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                          Pending Medical Review
                          <span class="badge badge-primary badge-pill"><?= $stats['pending'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                          Medical Review Complete
                          <span class="badge badge-info badge-pill"><?= $stats['medical_review'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                          Health Officer Approved
                          <span class="badge badge-warning badge-pill"><?= $stats['health_approved'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                          Fully Approved
                          <span class="badge badge-success badge-pill"><?= $stats['fully_approved'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                          Rejected Applications
                          <span class="badge badge-danger badge-pill"><?= $stats['rejected'] ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <canvas id="statusChart" height="200"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card">
                <div class="card-header">
                  <h4>Processing Efficiency</h4>
                </div>
                <div class="card-body">
                  <div class="mb-4">
                    <h6>Average Processing Time</h6>
                    <div class="progress" data-height="10">
                      <div class="progress-bar bg-info"
                        data-width="<?= min(100 - ($stats['avg_processing_time'] * 4), 100) ?>%"></div>
                    </div>
                    <small><?= $stats['avg_processing_time'] ?> hours (Lower is better)</small>
                  </div>

                  <div class="mb-4">
                    <h6>Today's Assessments</h6>
                    <h2><?= $stats['today_assessments'] ?></h2>
                  </div>

                  <div>
                    <h6>Approval Rate</h6>
                    <?php
                    $approval_rate = $stats['total_assessments'] > 0 ?
                      round(($stats['fully_approved'] / $stats['total_assessments']) * 100) : 0;
                    ?>
                    <div class="progress" data-height="10">
                      <div class="progress-bar bg-success" data-width="<?= $approval_rate ?>%"></div>
                    </div>
                    <small><?= $approval_rate ?>% of applications fully approved</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Disability Type Distribution -->
          <div class="row">
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  <h4>Disability Type Distribution</h4>
                </div>
                <div class="card-body">
                  <canvas id="disabilityChart" height="250"></canvas>
                </div>
              </div>
            </div>

            <!-- Top Hospitals -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  <h4>Hospital Performance</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Hospital</th>
                          <th>Assessments</th>
                          <th>Approved</th>
                          <th>Avg Time</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($hospital_performance as $hospital): ?>
                          <tr>
                            <td><?= htmlspecialchars($hospital['name']) ?></td>
                            <td><?= $hospital['total_assessments'] ?></td>
                            <td><?= $hospital['approved'] ?></td>
                            <td><?= round($hospital['avg_time'], 1) ?>h</td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Approvals -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4>Pending Your Approval</h4>
                </div>
                <div class="card-body">
                  <?php if (!empty($pending_approvals)): ?>
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Hospital</th>
                            <th>Submitted</th>
                            <th>Days Pending</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($pending_approvals as $assessment): ?>
                            <tr class="<?= $assessment['days_pending'] > 7 ? 'table-danger' : '' ?>">
                              <td>#<?= $assessment['id'] ?></td>
                              <td><?= htmlspecialchars($assessment['user_name']) ?></td>
                              <td><?= htmlspecialchars($assessment['hospital_name']) ?></td>
                              <td><?= date('M d, Y', strtotime($assessment['created_at'])) ?></td>
                              <td><?= $assessment['days_pending'] ?></td>
                              <td>
                                <a href="review_assessment.php?id=<?= $assessment['id'] ?>" class="btn btn-sm btn-primary">
                                  <i class="fas fa-eye"></i> Review
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-success">
                      <i class="fas fa-check-circle"></i> No assessments currently pending your approval!
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php include 'files/footer.php'; ?>

      <!-- Chart.js -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      <script>
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
          type: 'doughnut',
          data: {
            labels: ['Pending', 'Medical Review', 'Health Approved', 'Fully Approved', 'Rejected'],
            datasets: [{
              data: [
                <?= $stats['pending'] ?>,
                <?= $stats['medical_review'] ?>,
                <?= $stats['health_approved'] ?>,
                <?= $stats['fully_approved'] ?>,
                <?= $stats['rejected'] ?>
              ],
              backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#f6c23e',
                '#36b9cc',
                '#e74a3b'
              ],
              hoverBackgroundColor: [
                '#2e59d9',
                '#17a673',
                '#dda20a',
                '#2c9faf',
                '#be2617'
              ],
              hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
          },
          options: {
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'right',
              },
              tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: true,
                caretPadding: 10,
              },
            },
            cutout: '70%',
          },
        });

        // Disability Chart
        const disabilityCtx = document.getElementById('disabilityChart').getContext('2d');
        const disabilityChart = new Chart(disabilityCtx, {
          type: 'bar',
          data: {
            labels: [<?= implode(',', array_map(function ($item) {
              return "'" . htmlspecialchars($item['disability_type']) . "'"; }, $disability_stats)) ?>],
            datasets: [{
              label: "Number of Cases",
              backgroundColor: "#4e73df",
              hoverBackgroundColor: "#2e59d9",
              borderColor: "#4e73df",
              data: [<?= implode(',', array_column($disability_stats, 'count')) ?>],
            }],
          },
          options: {
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
              },
            },
            scales: {
              x: {
                grid: {
                  display: false,
                  drawBorder: false
                },
                ticks: {
                  maxRotation: 45,
                  minRotation: 45
                }
              },
              y: {
                beginAtZero: true,
                grid: {
                  color: "rgb(234, 236, 244)",
                  zeroLineColor: "rgb(234, 236, 244)",
                  drawBorder: false,
                  borderDash: [2],
                  zeroLineBorderDash: [2]
                },
                ticks: {
                  precision: 0
                }
              },
            },
          }
        });
      </script>
</body>

</html>