<?php include 'files/header.php';

// Access user data from session
$user_id = $pwdUser['id'] ?? null;
$user_county_id = $pwdUser['county_id'] ?? null;

// Function to safely fetch data with proper error handling
function fetchData($conn, $sql, $params = [], $single = false)
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt)
    return null;

  if (!empty($params)) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
  }

  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($single) {
    $data = mysqli_fetch_assoc($result);
  } else {
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
  }

  mysqli_stmt_close($stmt);
  return $data;
}

// Fetch user-specific data
$user_profile = fetchData(
  $conn,
  "SELECT u.*, c.county_name 
     FROM users u 
     LEFT JOIN counties c ON u.county_id = c.id 
     WHERE u.id = ?",
  [$user_id],
  true
);

// Fetch user's assessments with detailed status
$user_assessments = fetchData(
  $conn,
  "SELECT a.*, h.name as hospital_name, h.address as hospital_address,
            CASE 
                WHEN a.status = 'pending' THEN 'Pending Medical Review'
                WHEN a.status = 'checked' THEN 'Medical Evaluation Complete'
                WHEN a.status = 'approved_by_health_officer' THEN 'Health Officer Approved'
                WHEN a.status = 'approved_by_county_officer' THEN 'Fully Certified'
                WHEN a.status = 'rejected' THEN 'Needs Resubmission'
                ELSE a.status
            END as status_display,
            a.assessment_date,
            DATEDIFF(NOW(), a.created_at) as days_since_submission
     FROM assessments a
     LEFT JOIN hospitals h ON a.hospital_id = h.id
     WHERE a.user_id = ?
     ORDER BY a.assessment_date DESC",
  [$user_id]
);

// Calculate assessment stats
$assessment_stats = [
  'total' => count($user_assessments),
  'completed' => 0,
  'in_progress' => 0,
  'pending' => 0,
  'rejected' => 0
];

foreach ($user_assessments as $assessment) {
  switch ($assessment['status']) {
    case 'approved_by_county_officer':
      $assessment_stats['completed']++;
      break;
    case 'rejected':
      $assessment_stats['rejected']++;
      break;
    case 'pending':
      $assessment_stats['pending']++;
      break;
    default:
      $assessment_stats['in_progress']++;
  }
}

// Fetch available hospitals in user's county
$county_hospitals = [];
if ($user_county_id) {
  $county_hospitals = fetchData(
    $conn,
    "SELECT h.*, 
                (SELECT COUNT(*) FROM assessments 
                 WHERE hospital_id = h.id AND status = 'approved_by_county_officer') as successful_assessments
         FROM hospitals h
         WHERE h.county_id = ?
         ORDER BY successful_assessments DESC",
    [$user_county_id]
  );
}

// Gamification elements
$user_level = 1;
$user_points = 0;
$achievements = [];

// Calculate points and level
if ($assessment_stats['total'] > 0) {
  $user_points = $assessment_stats['completed'] * 100 + $assessment_stats['in_progress'] * 30;
  $user_level = min(floor($user_points / 200) + 1, 5);

  // Achievements
  if ($assessment_stats['completed'] >= 1) {
    $achievements[] = ['name' => 'First Certification', 'icon' => 'fa-medal', 'points' => 50];
  }
  if ($assessment_stats['completed'] >= 3) {
    $achievements[] = ['name' => 'Frequent Applicant', 'icon' => 'fa-trophy', 'points' => 100];
  }
  if (!empty($user_assessments) && $user_assessments[0]['status'] == 'approved_by_county_officer') {
    $achievements[] = ['name' => 'Current Certification', 'icon' => 'fa-certificate', 'points' => 75];
  }
}

// Current assessment progress
$current_assessment = !empty($user_assessments) ? $user_assessments[0] : null;
$progress_data = [
  'pending' => ['icon' => 'fa-file-upload', 'label' => 'Application Submitted', 'status' => false],
  'checked' => ['icon' => 'fa-user-md', 'label' => 'Medical Review', 'status' => false],
  'approved_by_health_officer' => ['icon' => 'fa-clipboard-check', 'label' => 'Health Officer Approval', 'status' => false],
  'approved_by_county_officer' => ['icon' => 'fa-stamp', 'label' => 'County Certification', 'status' => false]
];

if ($current_assessment) {
  foreach ($progress_data as $status => $data) {
    $progress_data[$status]['status'] =
      array_search($current_assessment['status'], array_keys($progress_data)) >=
      array_search($status, array_keys($progress_data));
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Disability Assessment Dashboard</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <!-- SweetAlert -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">

  <style>
    .progress-tracker {
      position: relative;
      height: 10px;
      background: #f0f0f0;
      border-radius: 5px;
      margin: 20px 0;
    }

    .progress-fill {
      height: 100%;
      border-radius: 5px;
      background: linear-gradient(90deg, #6777ef, #5a67d8);
      width: 0;
      transition: width 0.5s ease;
    }

    .progress-steps {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
    }

    .step {
      text-align: center;
      position: relative;
      flex: 1;
    }

    .step-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
      background: #f0f0f0;
      color: #6c757d;
    }

    .step.active .step-icon {
      background: #6777ef;
      color: white;
    }

    .step.completed .step-icon {
      background: #28a745;
      color: white;
    }

    .hospital-card {
      border: 1px solid #e4e6fc;
      border-radius: 8px;
      transition: all 0.3s;
      margin-bottom: 20px;
    }

    .hospital-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .hospital-badge {
      position: absolute;
      top: 10px;
      right: 10px;
    }

    .level-badge {
      font-size: 1rem;
      padding: 5px 10px;
      border-radius: 20px;
    }

    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s, box-shadow 0.2s;
      height: 100%;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php include 'files/nav.php'; ?>
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <!-- Dashboard Header -->
          <div class="section-header">
            <h1>My Assessment Dashboard</h1>
            <div class="section-header-breadcrumb">
              <div class="badge level-badge bg-primary text-light">
                <i class="fas fa-level-up-alt"></i> Level <?= $user_level ?>
              </div>
              <div class="badge level-badge bg-success ml-2">
                <i class="fas fa-star"></i> <?= $user_points ?> Points
              </div>
            </div>
          </div>

          <!-- User Summary -->
          <div class="row g-4 mb-4">
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-indigo-100 text-indigo-600 p-3 rounded-lg">
                    <i class="fas fa-file-medical fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Total Assessments</div>
                    <div class="h4 mb-0"><?= $assessment_stats['total'] ?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-green-100 text-green-600 p-3 rounded-lg">
                    <i class="fas fa-check-circle fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Approved</div>
                    <div class="h4 mb-0"><?= $assessment_stats['completed'] ?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-blue-100 text-blue-600 p-3 rounded-lg">
                    <i class="fas fa-spinner fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">In Progress</div>
                    <div class="h4 mb-0"><?= $assessment_stats['in_progress'] ?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-amber-100 text-amber-600 p-3 rounded-lg">
                    <i class="fas fa-map-marker-alt fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">County Assessment Centers</div>
                    <div class="h4 mb-0"><?= count($county_hospitals) ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="row">
            <!-- Current Assessment Progress -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  <h4>My Assessment Journey</h4>
                </div>
                <div class="card-body">
                  <?php if ($current_assessment): ?>
                    <h6>Current Status: <strong><?= $current_assessment['status_display'] ?></strong></h6>
                    <p class="text-muted">
                      Submitted <?= $current_assessment['days_since_submission'] ?> days ago at
                      <?= htmlspecialchars($current_assessment['hospital_name']) ?>
                    </p>

                    <div class="progress-tracker" hidden>
                      <div class="progress-fill" style="width: <?=
                        ($progress_data['pending']['status'] ? 25 : 0) +
                        ($progress_data['checked']['status'] ? 25 : 0) +
                        ($progress_data['approved_by_health_officer']['status'] ? 25 : 0) +
                        ($progress_data['approved_by_county_officer']['status'] ? 25 : 0)
                        ?>%"></div>
                    </div>

                    <div class="progress-steps" hidden>
                      <?php foreach ($progress_data as $status => $step): ?>
                        <div class="step <?=
                          $progress_data[$status]['status'] ? 'completed' : (
                            $current_assessment['status'] == $status ? 'active' : ''
                          )
                          ?>">
                          <div class="step-icon">
                            <i class="fas <?= $step['icon'] ?>"></i>
                          </div>
                          <div class="step-label"><?= $step['label'] ?></div>
                        </div>
                      <?php endforeach; ?>
                    </div>

                    <div class="mt-4">
                      <h6>Assessment Details</h6>
                      <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          Assessment Center
                          <span><?= htmlspecialchars($current_assessment['hospital_name']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          Address
                          <span class="text-right"><?= htmlspecialchars($current_assessment['hospital_address']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          Submission Date
                          <span><?= date('M d, Y', strtotime($current_assessment['created_at'])) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          Last Updated
                          <span><?= date('M d, Y', strtotime($current_assessment['assessment_date'])) ?></span>
                        </li>
                      </ul>
                    </div>
                  <?php else: ?>
                    <div class="empty-state" data-height="300">
                      <div class="empty-state-icon">
                        <i class="fas fa-file-medical"></i>
                      </div>
                      <h2>No Active Assessments</h2>
                      <p class="lead">
                        You haven't started any disability assessments yet.
                      </p>
                      <a href="new_assessment.php" class="btn btn-primary mt-4">
                        Start New Assessment
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- County Hospitals and Achievements -->
            <div class="col-lg-6">
              <!-- Available Hospitals -->
              <div class="card">
                <div class="card-header">
                  <h4>Assessment Centers in your County
                    (<?= htmlspecialchars($user_profile['county_name'] ?? 'Your County') ?> )</h4>
                </div>
                <div class="card-body">
                  <?php if (!empty($county_hospitals)): ?>
                    <div class="row">
                      <?php foreach ($county_hospitals as $hospital): ?>
                        <div class="col-md-6">
                          <div class="hospital-card">
                            <div class="card-body">
                              <?php if ($hospital['successful_assessments'] > 10): ?>
                                <span class="hospital-badge badge badge-success">
                                  <i class="fas fa-check-circle"></i> Recommended
                                </span>
                              <?php endif; ?>
                              <h6><?= htmlspecialchars($hospital['name']) ?></h6>
                              <p class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($hospital['address']) ?>
                              </p>
                              <!-- <div class="d-flex justify-content-between" >
                                <span class="text-primary">
                                  <i class="fas fa-users"></i> < ?= $hospital['successful_assessments'] ?> successful
                                  assessments
                                </span>
                              </div> -->
                              <!-- <a href="new_assessment.php?hospital=<?= $hospital['id'] ?>"
                                class="btn btn-sm btn-outline-primary btn-block mt-3">
                                Choose This Center
                              </a> -->
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <a href="List_Hospitals" class="btn btn-outline-secondary btn-block">
                      View All Assessment Centers
                    </a>
                  <?php else: ?>
                    <div class="alert alert-warning">
                      No assessment centers found in your county. Please check back later or contact support.
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Achievements -->

            </div>
          </div>

          <!-- Assessment History -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4>My Assessment History</h4>
                </div>
                <div class="card-body">
                  <?php if (!empty($user_assessments)): ?>
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Assessment Center</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Last Update</th>
                            <th>Days Taken</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($user_assessments as $assessment): ?>
                            <tr>
                              <td>#<?= $assessment['id'] ?></td>
                              <td><?= htmlspecialchars($assessment['hospital_name']) ?></td>
                              <td>
                                <?php switch ($assessment['status']) {
                                  case 'approved_by_county_officer':
                                    echo '<span class="badge badge-success">' . $assessment['status_display'] . '</span>';
                                    break;
                                  case 'rejected':
                                    echo '<span class="badge badge-danger">' . $assessment['status_display'] . '</span>';
                                    break;
                                  case 'pending':
                                    echo '<span class="badge badge-warning">' . $assessment['status_display'] . '</span>';
                                    break;
                                  default:
                                    echo '<span class="badge badge-info">' . $assessment['status_display'] . '</span>';
                                } ?>
                              </td>
                              <td><?= date('M d, Y', strtotime($assessment['created_at'])) ?></td>
                              <td><?= date('M d, Y', strtotime($assessment['assessment_date'])) ?></td>
                              <td><?= $assessment['days_since_submission'] ?></td>
                              <td>
                                <a href="view_application"
                                  class="btn btn-sm btn-outline-primary">Details</a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-info">
                      You haven't completed any assessments yet. Get started today!
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php include 'files/footer.php'; ?>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>

  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>

  <script>
    // Animate progress bars on page load
    $(document).ready(function () {
      $('.progress-bar').each(function () {
        let width = $(this).attr('data-width');
        $(this).css('width', '0').animate({
          width: width + '%'
        }, 1000);
      });
    });
  </script>
</body>

</html>