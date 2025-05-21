<?php include 'files/header.php';
$user_id = $pwdUser['id'];
?>
<style>
  .toggle-password {
    position: absolute;
    top: 38px;
    right: 15px;
    cursor: pointer;
    color: #666;
  }
</style>

<body>


  <!-- #region -->
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>


      <!-- navigation -->
      <?php include 'files/sidebar.php'; ?>


      <?php

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['password']);
        $confirm_password = trim($_POST['password_confirm']);

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
          echo "<script>Swal.fire('Error', 'All fields are required.', 'error');</script>";
        } else {
          // Fetch current password hash
          $query = "SELECT password FROM officials WHERE id = $user_id";
          $result = mysqli_query($conn, $query);

          if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $stored_hash = $row['password'];

            if (!password_verify($current_password, $stored_hash)) {
              echo "<script>Swal.fire('Error', 'Current password is incorrect.', 'error');</script>";
            } elseif ($new_password !== $confirm_password) {
              echo "<script>Swal.fire('Error', 'New password and confirmation do not match.', 'error');</script>";
            } elseif (password_verify($new_password, $stored_hash)) {
              echo "<script>Swal.fire('Error', 'New password cannot be the same as the old one.', 'error');</script>";
            } else {
              $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
              $update = "UPDATE users SET password = '$new_hash' WHERE id = $user_id";

              if (mysqli_query($conn, $update)) {
                echo "<script>Swal.fire('Success', 'Password updated successfully.', 'success');</script>";
              } else {
                echo "<script>Swal.fire('Error', 'Failed to update password.', 'error');</script>";
              }
            }
          } else {
            echo "<script>Swal.fire('Error', 'User not found.', 'error');</script>";
          }
        }
      }
      ?>


      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Profile</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row ">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?= htmlspecialchars($pwdUser['name']); ?>! </h2>
                <p class="section-lead">
                  Update your password on this page.
                </p>
              </div>
              <div class="col-md-3 text-center" hidden>
                <div class="card profile-widget text-center">
                  <div class="profile-widget-header ">
                    <div class="text-center">
                      <img alt="image" src="../assets/img/avatar/avatar-1.png"
                        class="rounded-circle profile-widget-picture">
                    </div>
                  </div>
                  <div class="profile-widget-description text-right">
                    <div class="profile-widget-name"><?= htmlspecialchars($pwdUser['name']) ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="container mt-5">
              <div class="row justify-content-center">
                <div class="col-12 col-sm-8 col-md-8 col-lg-10">
                  <div class="card p-4 text-center" style="border-top: 3px solid rgb(0, 72, 66);">

                    <form method="POST" autocomplete="off">
                      <div class="form-group position-relative">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control"
                          required>
                        <i class="fas fa-eye toggle-password" data-toggle="#current_password"></i>
                      </div>

                      <div class="form-group position-relative">
                        <label for="password">New Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <i class="fas fa-eye toggle-password" data-toggle="#password"></i>
                      </div>

                      <div class="form-group position-relative">
                        <label for="password_confirm">Confirm New Password</label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control"
                          required>
                        <i class="fas fa-eye toggle-password" data-toggle="#password_confirm"></i>
                      </div>

                      <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>
      </div>


      <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
          icon.addEventListener('click', function () {
            const input = document.querySelector(this.getAttribute('data-toggle'));
            if (input.type === 'password') {
              input.type = 'text';
              this.classList.remove('fa-eye');
              this.classList.add('fa-eye-slash');
            } else {
              input.type = 'password';
              this.classList.remove('fa-eye-slash');
              this.classList.add('fa-eye');
            }
          });
        });
      </script>

      <?php include 'files/footer.php'; ?>