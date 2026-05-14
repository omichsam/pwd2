<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$user_id = $pwdUser['id'];
$stmt = mysqli_prepare($conn, "SELECT o.*, h.name AS hospital_name FROM officials o LEFT JOIN hospitals h ON o.hospital_id = h.id WHERE o.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>View Profile</h1>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4>Profile Details</h4>
                        <div class="card-header-action">
                            <a href="edit_profile" class="btn btn-primary btn-sm">Edit Profile</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr><th>Name</th><td><?= htmlspecialchars($user['name']) ?></td></tr>
                            <tr><th>License ID</th><td><?= htmlspecialchars($user['license_id']) ?></td></tr>
                            <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                            <tr><th>Mobile</th><td><?= htmlspecialchars($user['mobile_number']) ?></td></tr>
                            <tr><th>Role</th><td><span class="badge badge-success">Hospital Admin</span></td></tr>
                            <tr><th>Hospital</th><td><?= htmlspecialchars($user['hospital_name'] ?? '—') ?></td></tr>
                            <tr><th>Joined</th><td><?= date('d M Y', strtotime($user['created_at'])) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<?php include 'files/footer.php'; ?>
