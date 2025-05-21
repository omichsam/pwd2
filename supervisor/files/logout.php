<!-- < ?php
session_start();
session_destroy();
header("Location: ../../login.php");

exit(); -->



<?php include 'header.php';?>

<body>
    <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <?php
    // session_start();
    $_SESSION = [];
    session_destroy();

    // Optionally clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Display logout confirmation with redirect
    echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
Swal.fire({
  icon: 'success',
  title: 'Logged Out',
  text: 'You have been successfully logged out.',
  showConfirmButton: false,
  timer: 2000
}).then(() => {
  window.location.href = '../../login.php';
});
</script>
";
    ?>

    <script src="" async defer></script>
</body>
</html>