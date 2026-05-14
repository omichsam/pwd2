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
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $mobile = trim($_POST['mobile_number']);
    $stmt = mysqli_prepare($conn, "UPDATE officials SET name=?, email=?, mobile_number=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $mobile, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['official_user']['name'] = $name;
        echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire('Success','Profile updated.','success');});</script>";
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM officials WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>

<div class="main-content">
<section class="section">
    <div class="section-header"><h1>Edit Profile</h1></div>
    <div class="section-body">
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><h4>Update Profile</h4></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Mobile Number</label>
                                <input type="text" name="mobile_number" class="form-control" value="<?= htmlspecialchars($user['mobile_number']) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<?php include 'files/footer.php'; ?>
