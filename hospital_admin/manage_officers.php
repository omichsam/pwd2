<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$admin_id = $pwdUser['id'];
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT hospital_id FROM officials WHERE id = $admin_id"));
$hospital_id = intval($row['hospital_id']);

// Handle deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_id'])) {
    $deactivate_id = intval($_POST['deactivate_id']);
    $stmt = mysqli_prepare($conn, "UPDATE officials SET active = 0 WHERE id = ? AND hospital_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $deactivate_id, $hospital_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire('Done','Officer deactivated.','success').then(()=>location.reload());});</script>";
    }
}

// Handle reactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_id'])) {
    $activate_id = intval($_POST['activate_id']);
    $stmt = mysqli_prepare($conn, "UPDATE officials SET active = 1 WHERE id = ? AND hospital_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $activate_id, $hospital_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire('Done','Officer activated.','success').then(()=>location.reload());});</script>";
    }
}

$officers = mysqli_fetch_all(mysqli_query($conn,
    "SELECT * FROM officials WHERE hospital_id = $hospital_id AND type IN ('medical_officer','health_officer') ORDER BY type, name"
), MYSQLI_ASSOC);
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>Manage Officers</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="index">Dashboard</a></div>
            <div class="breadcrumb-item active">Manage Officers</div>
        </div>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-header"><h4>Officers at This Hospital</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="officersTable">
                        <thead>
                            <tr>
                                <th>License ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($officers as $o): ?>
                            <tr>
                                <td><?= htmlspecialchars($o['license_id']) ?></td>
                                <td><?= htmlspecialchars($o['name']) ?></td>
                                <td>
                                    <?= $o['type'] === 'medical_officer' ? '<span class="badge badge-info">Medical Officer</span>' : '<span class="badge badge-primary">Health Officer</span>' ?>
                                </td>
                                <td><?= htmlspecialchars($o['email']) ?></td>
                                <td><?= htmlspecialchars($o['mobile_number']) ?></td>
                                <td>
                                    <?= $o['active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?>
                                </td>
                                <td>
                                    <?php if ($o['active']): ?>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="deactivate_id" value="<?= $o['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-warning"
                                                onclick="return confirm('Deactivate this officer?')">Deactivate</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="activate_id" value="<?= $o['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Reactivate this officer?')">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
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

<script>$(document).ready(function(){ $('#officersTable').DataTable(); });</script>

<?php include 'files/footer.php'; ?>
