<?php include 'files/header.php';

$officer_id = $pwdUser['id'];

// Get officer details and hospital info
$sql = "SELECT o.*, h.name AS hospital_name, h.county_id, c.county_name 
        FROM officials o 
        JOIN hospitals h ON o.hospital_id = h.id
        JOIN counties c ON h.county_id = c.id
        WHERE o.id = $officer_id";
$result = mysqli_query($conn, $sql);
$officer = mysqli_fetch_assoc($result);
$hospital_id = $officer['hospital_id'];

// Assessment statistics
$stats = [
  'total' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND medical_officer_id IS NOT NULL"),
  'pending' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'pending'"),
  'checked' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'checked'"),
  'rejected' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'rejected'"),
  'completed' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'approved_by_county_officer'")
];

// Pending assessments (status = 'checked')
$pending_assessments = getPendingAssessments($hospital_id);

// Disability type distribution
$disability_stats = getDisabilityStats($hospital_id);

function getCount($sql)
{
  global $conn;
  $result = mysqli_query($conn, $sql);
  return mysqli_fetch_array($result)[0];
}

function getPendingAssessments($hospital_id)
{
  global $conn;
  $sql = "SELECT a.id, a.user_id, u.name AS user_name, a.disability_type
            FROM assessments a
            JOIN users u ON a.user_id = u.id
            WHERE a.hospital_id = $hospital_id AND a.status = 'checked'
            ORDER BY a.created_at DESC 
            LIMIT 5";
  $result = mysqli_query($conn, $sql);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getDisabilityStats($hospital_id)
{
  global $conn;
  $sql = "SELECT disability_type, COUNT(*) AS count 
            FROM assessments 
            WHERE hospital_id = $hospital_id
            GROUP BY disability_type
            ORDER BY count DESC";
  $result = mysqli_query($conn, $sql);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Health Officer Dashboard</title>

  <!-- Modern CSS Framework -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.0.0-beta.73/dist/themes/light.css">

  <!-- Custom Styling -->
  <style>
    :root {
      --primary: #6366f1;
      --primary-light: #818cf8;
      --secondary: #f43f5e;
      --dark: #1e293b;
      --light: #f8fafc;
      --success: #10b981;
      --warning: #f59e0b;
      --info: #0ea5e9;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f1f5f9;
      color: var(--dark);
    }

    .dashboard-header {
      background: linear-gradient(135deg, var(--dark) 0%, var(--primary-light) 100%);
      color: white;
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

    .chart-container {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .badge-pill {
      border-radius: 50rem;
    }

    .progress-thin {
      height: 6px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .urgent {
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
      }
    }
  </style>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h2 class="mb-1">Welcome, <?= htmlspecialchars($officer['name']) ?></h2>
                <p class="mb-0 opacity-75">
                  <?= htmlspecialchars($officer['hospital_name']) ?> •
                  <?= htmlspecialchars($officer['county_name']) ?>
                </p>
              </div>
              <div class="d-flex align-items-center">
                <div class="me-3 text-end">
                  <!-- <div class="text-sm opacity-75">Level</div> -->
                  <!-- <div class="h4 mb-0">< ?= $gamification['level'] ?></div> -->
                </div>
                <div class="text-end">
                  <!-- <div class="text-sm opacity-75">Points</div> -->
                  <!-- <div class="h4 mb-0">< ?= $gamification['points'] ?></div> -->
                  <div class="user-info">
                    <span><?= date('l, F j, Y') ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Overview -->
          <div class="row g-4 mb-4">
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-indigo-100 text-indigo-600 p-3 rounded-lg">
                    <i class="fas fa-file-medical fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Total Assessments</div>
                    <div class="h4 mb-0"><?= $stats['total'] ?></div>

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-amber-100 text-amber-600 p-3 rounded-lg">
                    <i class="fas fa-hourglass-half fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Pending Review</div>
                    <div class="h4 mb-0"><?= $stats['checked'] ?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-green-100 text-green-600 p-3 rounded-lg">
                    <i class="fas fa-ban fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Rejected</div>
                    <div class="h4 mb-0"><?= $stats['rejected'] ?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stat-card">
                <div class="d-flex align-items-center">
                  <div class="me-3 bg-blue-100 text-blue-600 p-3 rounded-lg">
                    <i class="fas fa-check-double fa-lg"></i>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="h4 mb-0"><?= $stats['completed'] ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-4">
            <!-- Recent Assessments -->
            <div class="col-lg-8">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>Pending Assessments</h4>
                  <a href="assessment" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                  <?php if (!empty($pending_assessments)): ?>
                    <div class="table-responsive">
                      <table class="table  table-hover table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Disability</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($pending_assessments as $assessment): ?>
                            <tr>
                              <td>#<?= $assessment['id'] ?></td>
                              <td><?= htmlspecialchars($assessment['user_name']) ?></td>
                              <td>
                                <span class="badge badge-warning">
                                  <?= htmlspecialchars($assessment['disability_type']) ?>
                                </span>
                              </td>
                              <td>
                                <a href="view_assessment?user_id=<?= $assessment['user_id'] ?>&from=assessment" class="btn btn-primary">
                                  Review
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="text-center py-4">
                      <div class="mb-3 text-gray-400">
                        <i class="fas fa-check-circle fa-3x"></i>
                      </div>
                      <h4>No Pending Assessments</h4>
                      <p class="text-gray-500">All assessments have been processed</p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Performance & Gamification -->
            <div class="col-lg-4">
              <div class="card mb-4">
                <div class="card-header">
                  <h4>Disability Distribution</h4>
                </div>
                <div class="card-body">
                  <div class="chart-container">
                    <canvas id="disabilityChart"></canvas>
                  </div>
                </div>
              </div>

              <!-- Gamification -->
              <div class="card">
                <div class="card-header">
                  <h4>Disability Summary</h4>
                </div>
                 <div class="card-body">
              <table class="table ">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Cases</th>
                    <th>%</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $total_cases = array_sum(array_column($disability_stats, 'count'));
                  foreach ($disability_stats as $disability):
                    $percentage = $total_cases > 0 ? round(($disability['count'] / $total_cases) * 100, 1) : 0;
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($disability['disability_type']) ?></td>
                      <td><?= $disability['count'] ?></td>
                      <td><?= $percentage ?>%</td>
                    </tr>
                  <?php endforeach; ?>
                  <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td>Total</td>
                    <td><?= $total_cases ?></td>
                    <td>100%</td>
                  </tr>
                </tbody>
              </table>
            </div>
              </div>
            </div>
          </div> 
        </section>
      </div>

      <?php include 'files/footer.php'; ?>
    </div>
  </div>

  <!-- Modern JavaScript Components -->
  <script src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.0.0-beta.73/dist/shoelace.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Chart.js Implementation -->
  
  <script>
    // Disability Pie Chart
    const ctx = document.getElementById('disabilityChart').getContext('2d');
    const disabilityChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: [<?= implode(',', array_map(function ($item) {
          return "'" . htmlspecialchars($item['disability_type']) . "'"; }, $disability_stats)) ?>],
        datasets: [{
          data: [<?= implode(',', array_column($disability_stats, 'count')) ?>],
          backgroundColor: [
            '#3498db', '#2ecc71', '#e74c3c', '#f39c12',
            '#9b59b6', '#1abc9c', '#d35400', '#34495e'
          ],
          borderWidth: 0,
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.label || '';
                const value = context.raw || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((value / total) * 100);
                return `${label}: ${value} (${percentage}%)`;
              }
            }
          }
        },
        cutout: '60%'
      }
    });
  </script>
</body>

</html>