<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$admin_id = $pwdUser['id'];

// Get this admin's hospital
$sql = "SELECT o.*, h.name AS hospital_name, h.county_id, c.county_name
        FROM officials o
        JOIN hospitals h ON o.hospital_id = h.id
        JOIN counties c ON h.county_id = c.id
        WHERE o.id = $admin_id";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);
$hospital_id = $admin['hospital_id'];

function getCount($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_array($result)[0] ?? 0;
}

$stats = [
    'total'       => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id"),
    'pending'     => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'pending'"),
    'checked'     => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'checked'"),
    'health_approved' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'approved_by_health_officer'"),
    'county_approved' => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'approved_by_county_officer'"),
    'rejected'    => getCount("SELECT COUNT(*) FROM assessments WHERE hospital_id = $hospital_id AND status = 'rejected'"),
    'medical_officers' => getCount("SELECT COUNT(*) FROM officials WHERE hospital_id = $hospital_id AND type = 'medical_officer' AND active = 1"),
    'health_officers'  => getCount("SELECT COUNT(*) FROM officials WHERE hospital_id = $hospital_id AND type = 'health_officer' AND active = 1"),
];

// Disability type distribution
$dis_result = mysqli_query($conn, "SELECT disability_type, COUNT(*) AS cnt FROM assessments WHERE hospital_id = $hospital_id AND disability_type IS NOT NULL GROUP BY disability_type ORDER BY cnt DESC");
$disability_stats = mysqli_fetch_all($dis_result, MYSQLI_ASSOC);

// Recent assessments
$recent_result = mysqli_query($conn, "SELECT a.id, u.name AS user_name, a.disability_type, a.status, a.assessment_date FROM assessments a JOIN users u ON a.user_id = u.id WHERE a.hospital_id = $hospital_id ORDER BY a.created_at DESC LIMIT 10");
$recent = mysqli_fetch_all($recent_result, MYSQLI_ASSOC);
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>Hospital Admin Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">Welcome, <?= htmlspecialchars($admin['name']); ?></h2>
        <p class="section-lead">
            <i class="fas fa-hospital-alt"></i>
            <strong><?= htmlspecialchars($admin['hospital_name']); ?></strong> &mdash;
            <?= htmlspecialchars($admin['county_name']); ?> County
        </p>

        <!-- Stats Row -->
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-clipboard-list"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Total</h4></div>
                        <div class="card-body"><?= $stats['total'] ?></div></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-hourglass-half"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Pending</h4></div>
                        <div class="card-body"><?= $stats['pending'] ?></div></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info"><i class="fas fa-user-md"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Medical Done</h4></div>
                        <div class="card-body"><?= $stats['checked'] ?></div></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-clipboard-check"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Health Approved</h4></div>
                        <div class="card-body"><?= $stats['health_approved'] ?></div></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-award"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Certified</h4></div>
                        <div class="card-body"><?= $stats['county_approved'] ?></div></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Rejected</h4></div>
                        <div class="card-body"><?= $stats['rejected'] ?></div></div>
                </div>
            </div>
        </div>

        <!-- Officers Row -->
        <div class="row">
            <div class="col-md-3">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-secondary"><i class="fas fa-stethoscope"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Medical Officers</h4></div>
                        <div class="card-body"><?= $stats['medical_officers'] ?></div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-secondary"><i class="fas fa-heartbeat"></i></div>
                    <div class="card-wrap"><div class="card-header"><h4>Health Officers</h4></div>
                        <div class="card-body"><?= $stats['health_officers'] ?></div></div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Disability Distribution -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header"><h4>Disability Type Distribution</h4></div>
                    <div class="card-body">
                        <?php if (empty($disability_stats)): ?>
                            <p class="text-muted">No data yet.</p>
                        <?php else: ?>
                            <?php foreach ($disability_stats as $ds):
                                $pct = $stats['total'] > 0 ? round(($ds['cnt'] / $stats['total']) * 100) : 0;
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Assessments -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><h4>Recent Assessments</h4>
                        <div class="card-header-action">
                            <a href="hospital_records" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead><tr>
                                    <th>#</th><th>Patient</th><th>Disability</th><th>Status</th><th>Date</th>
                                </tr></thead>
                                <tbody>
                                <?php foreach ($recent as $r): ?>
                                <tr>
                                    <td><?= $r['id'] ?></td>
                                    <td><?= htmlspecialchars($r['user_name']) ?></td>
                                    <td><?= htmlspecialchars($r['disability_type'] ?? '—') ?></td>
                                    <td>
                                        <?php
                                        $badge = match($r['status']) {
                                            'pending' => 'warning',
                                            'checked' => 'info',
                                            'approved_by_health_officer' => 'primary',
                                            'approved_by_county_officer' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary'
                                        };
                                        $label = match($r['status']) {
                                            'pending' => 'Pending',
                                            'checked' => 'Medical Done',
                                            'approved_by_health_officer' => 'Health Approved',
                                            'approved_by_county_officer' => 'Certified',
                                            'rejected' => 'Rejected',
                                            default => $r['status']
                                        };
                                        ?>
                                        <span class="badge badge-<?= $badge ?>"><?= $label ?></span>
                                    </td>
                                    <td><?= $r['assessment_date'] ?? '—' ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<?php include 'files/footer.php'; ?>
