<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$user_id = $pwdUser['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    $stmt = mysqli_prepare($conn, "SELECT password FROM officials WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!password_verify($current, $row['password'])) {
        echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire('Error','Current password is incorrect.','error');});</script>";
    } elseif ($new_pass !== $confirm) {
        echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire('Error','New passwords do not match.','error');});</script>";
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $upd = mysqli_prepare($conn, "UPDATE officials SET password=? WHERE id=?");
        mysqli_stmt_bind_param($upd, 'si', $hashed, $user_id);
        if (mysqli_stmt_execute($upd)) {
            echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire('Success','Password changed successfully.','success');});</script>";
        }
    }
}
?>

<div class="main-content">
<section class="section">
    <div class="section-header"><h1>Change Password</h1></div>
    <div class="section-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h4>Update Password</h4></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<?php include 'files/footer.php'; ?>
