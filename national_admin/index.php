<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
function natCount($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_array($result)[0] ?? 0;
}

$stats = [
    'total_pwds'       => natCount("SELECT COUNT(*) FROM users WHERE type='PWD'"),
    'total_assessments'=> natCount("SELECT COUNT(*) FROM assessments"),
    'pending'          => natCount("SELECT COUNT(*) FROM assessments WHERE status='pending'"),
    'certified'        => natCount("SELECT COUNT(*) FROM assessments WHERE status='approved_by_county_officer'"),
    'rejected'         => natCount("SELECT COUNT(*) FROM assessments WHERE status='rejected'"),
    'total_hospitals'  => natCount("SELECT COUNT(*) FROM hospitals"),
    'total_counties'   => natCount("SELECT COUNT(*) FROM counties"),
    'medical_officers' => natCount("SELECT COUNT(*) FROM officials WHERE type='medical_officer'"),
    'health_officers'  => natCount("SELECT COUNT(*) FROM officials WHERE type='health_officer'"),
    'county_officers'  => natCount("SELECT COUNT(*) FROM officials WHERE type='county_officer'"),
];

// County-by-county assessment counts
$county_stats = mysqli_fetch_all(mysqli_query($conn,
    "SELECT c.county_name,
        COUNT(a.id) AS total,
        SUM(a.status='pending') AS pending,
        SUM(a.status='approved_by_county_officer') AS certified,
        SUM(a.status='rejected') AS rejected,
        COUNT(DISTINCT h.id) AS hospitals
     FROM counties c
     LEFT JOIN hospitals h ON h.county_id = c.id
     LEFT JOIN assessments a ON a.hospital_id = h.id
     GROUP BY c.id, c.county_name
     ORDER BY total DESC"
), MYSQLI_ASSOC);

// Disability type distribution
$disability_stats = mysqli_fetch_all(mysqli_query($conn,
    "SELECT disability_type, COUNT(*) AS cnt FROM assessments WHERE disability_type IS NOT NULL GROUP BY disability_type ORDER BY cnt DESC"
), MYSQLI_ASSOC);

// Monthly trend (last 6 months)
$monthly = mysqli_fetch_all(mysqli_query($conn,
    "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS cnt
     FROM assessments
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY month ORDER BY month"
), MYSQLI_ASSOC);
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>National Overview</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">National Admin Dashboard</div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">Welcome, <?= htmlspecialchars($pwdUser['name']); ?></h2>
        <p class="section-lead">System-wide analytics across all counties and hospitals.</p>

        <!-- Top Stats -->
        <div class="row">
            <?php
            $cards = [
                ['label'=>'Registered PWDs',   'val'=>$stats['total_pwds'],        'icon'=>'fa-wheelchair',    'color'=>'bg-primary'],
                ['label'=>'Total Assessments',  'val'=>$stats['total_assessments'], 'icon'=>'fa-clipboard-list','color'=>'bg-info'],
                ['label'=>'Pending',            'val'=>$stats['pending'],           'icon'=>'fa-hourglass-half','color'=>'bg-warning'],
                ['label'=>'Certified',          'val'=>$stats['certified'],         'icon'=>'fa-award',         'color'=>'bg-success'],
                ['label'=>'Rejected',           'val'=>$stats['rejected'],          'icon'=>'fa-times-circle',  'color'=>'bg-danger'],
                ['label'=>'Hospitals',          'val'=>$stats['total_hospitals'],   'icon'=>'fa-hospital',      'color'=>'bg-secondary'],
                ['label'=>'Counties',           'val'=>$stats['total_counties'],    'icon'=>'fa-map-marked-alt','color'=>'bg-dark'],
                ['label'=>'Medical Officers',   'val'=>$stats['medical_officers'],  'icon'=>'fa-stethoscope',   'color'=>'bg-info'],
            ];
            foreach ($cards as $c): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon <?= $c['color'] ?>"><i class="fas <?= $c['icon'] ?>"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4><?= $c['label'] ?></h4></div>
                        <div class="card-body"><?= $c['val'] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <!-- County Breakdown Table -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4>County-by-County Breakdown</h4>
                        <div class="card-header-action">
                            <a href="view_counties" class="btn btn-sm btn-primary">Full Report</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0" id="countyTable">
                                <thead><tr>
                                    <th>County</th><th>Hospitals</th><th>Total</th><th>Pending</th><th>Certified</th><th>Rejected</th>
                                </tr></thead>
                                <tbody>
                                <?php foreach ($county_stats as $cs): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cs['county_name']) ?></td>
                                    <td><?= $cs['hospitals'] ?></td>
                                    <td><?= $cs['total'] ?></td>
                                    <td><span class="badge badge-warning"><?= $cs['pending'] ?></span></td>
                                    <td><span class="badge badge-success"><?= $cs['certified'] ?></span></td>
                                    <td><span class="badge badge-danger"><?= $cs['rejected'] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disability Distribution -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h4>Disability Distribution</h4></div>
                    <div class="card-body">
                        <?php
                        $total_a = $stats['total_assessments'] ?: 1;
                        foreach ($disability_stats as $ds):
                            $pct = round(($ds['cnt'] / $total_a) * 100);
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= htmlspecialchars($ds['disability_type'] ?? 'Unknown') ?></span>
                                <span><?= $ds['cnt'] ?> (<?= $pct ?>%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Monthly Trend -->
                <div class="card">
                    <div class="card-header"><h4>Monthly Assessments (6 months)</h4></div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script src="../assets/modules/chart.min.js"></script>
<script>
const monthlyData = <?= json_encode($monthly) ?>;
const labels = monthlyData.map(d => d.month);
const counts = monthlyData.map(d => parseInt(d.cnt));

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Assessments',
            data: counts,
            backgroundColor: 'rgba(0,128,128,0.7)'
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

$(document).ready(function(){ $('#countyTable').DataTable({ pageLength: 10 }); });
</script>

<?php include 'files/footer.php'; ?>
