<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$admin_id = $pwdUser['id'];
$sql = "SELECT hospital_id FROM officials WHERE id = $admin_id";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$hospital_id = intval($row['hospital_id']);

$records_sql = "SELECT a.id, u.name AS patient_name, u.id_number, a.disability_type, a.assessment_date, a.status, a.created_at
                FROM assessments a
                JOIN users u ON a.user_id = u.id
                WHERE a.hospital_id = $hospital_id
                ORDER BY a.created_at DESC";
$records = mysqli_fetch_all(mysqli_query($conn, $records_sql), MYSQLI_ASSOC);
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>Hospital Records</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="index">Dashboard</a></div>
            <div class="breadcrumb-item active">All Records</div>
        </div>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-header"><h4>All Assessment Records</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="recordsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient Name</th>
                                <th>ID Number</th>
                                <th>Disability Type</th>
                                <th>Assessment Date</th>
                                <th>Status</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['patient_name']) ?></td>
                                <td><?= htmlspecialchars($r['id_number']) ?></td>
                                <td><?= htmlspecialchars($r['disability_type'] ?? '—') ?></td>
                                <td><?= $r['assessment_date'] ?? '—' ?></td>
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
                                        default => ucfirst($r['status'])
                                    };
                                    ?>
                                    <span class="badge badge-<?= $badge ?>"><?= $label ?></span>
                                </td>
                                <td><?= date('Y-m-d', strtotime($r['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script>
$(document).ready(function () {
    $('#recordsTable').DataTable({ pageLength: 15, order: [[6, 'desc']] });
});
</script>

<?php include 'files/footer.php'; ?>
