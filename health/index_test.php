

<style>
    :root {
      --primary: #3498db;
      --secondary: #2ecc71;
      --accent: #e74c3c;
      --light: #ecf0f1;
      --dark: #2c3e50;
      --text: #34495e;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f5f7fa;
      color: var(--text);
      margin: 0;
      padding: 0;
    }

    .dashboard-container {
      display: grid;
      grid-template-columns: 250px 1fr;
      min-height: 100vh;
    }

    .sidebar {
      background-color: var(--dark);
      color: white;
      padding: 20px;
    }

    .main-content {
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid #ddd;
    }

    .welcome-box {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }

    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      text-align: center;
    }

    .stat-card .value {
      font-size: 28px;
      font-weight: bold;
      margin: 10px 0;
      color: var(--primary);
    }

    .stat-card .label {
      color: #7f8c8d;
      font-size: 14px;
    }

    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
      overflow: hidden;
    }

    .card-header {
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-title {
      font-weight: 500;
      color: var(--dark);
    }

    .card-body {
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #f8f9fa;
      font-weight: 500;
      color: var(--dark);
    }

    .btn {
      padding: 8px 15px;
      border-radius: 5px;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
      cursor: pointer;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
      border: none;
    }

    .chart-container {
      height: 250px;
      margin-top: 15px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 500;
    }

    .badge-warning {
      background-color: #f39c12;
      color: white;
    }
  </style> 


<div class="dashboard-container">
    <!-- < ?php include 'files/sidebar.php'; ?> -->

    <div class="main-content">
      <div class="header">
        <h2>Dashboard Overview</h2>
        <div class="user-info">
          <span><?= date('l, F j, Y') ?></span>
        </div>
      </div>

      <div class="welcome-box">
        <h3>Welcome back, <?= htmlspecialchars($officer['name']) ?></h3>
        <p><?= htmlspecialchars($officer['hospital_name']) ?> • <?= htmlspecialchars($officer['county_name']) ?></p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="label">Total Assessments</div>
          <div class="value"><?= $stats['total'] ?></div>
          <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-card">
          <div class="label">Pending Review</div>
          <div class="value"><?= $stats['pending'] ?></div>
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-card">
          <div class="label">Checked</div>
          <div class="value"><?= $stats['checked'] ?></div>
          <i class="fas fa-check"></i>
        </div>
        <div class="stat-card">
          <div class="label">Completed</div>
          <div class="value"><?= $stats['completed'] ?></div>
          <i class="fas fa-check-double"></i>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid-2">
        <!-- Left Column -->
        <div>
          <!-- Pending Assessments -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Pending Assessments (Checked)</div>
              <a href="assessments.php" class="btn btn-primary">View All</a>
            </div>
            <div class="card-body">
              <?php if (!empty($pending_assessments)): ?>
                <table>
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
                          <a href="review_assessment.php?id=<?= $assessment['id'] ?>" class="btn btn-primary">
                            Review
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p style="text-align: center; color: #7f8c8d; padding: 20px 0;">
                  No pending assessments with checked status
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div>
          <!-- Disability Statistics -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Disability Distribution</div>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="disabilityChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Disability Summary -->
          <div class="card">
            <div class="card-header">
              <div class="card-title">Disability Summary</div>
            </div>
            <div class="card-body">
              <table>
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
    </div>
  </div>

  <script>
    // Disability Pie Chart
    const ctx = document.getElementById('disabilityChart').getContext('2d');
    const disabilityChart = new Chart(ctx, {
      type: 'pie',
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