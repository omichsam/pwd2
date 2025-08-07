<?php
session_start(); // Start session at top

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_connect.php';

    // echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    $type = trim($_POST['type']);
    $password = $_POST['password'];

    if ($type === "PWD") {
        $id_number = trim($_POST['id_number']);

        if (empty($id_number) || empty($password)) {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Fields',
                    text: 'Please enter your ID Number and Password.'
                });
            </script>";
            exit();
        }

        $query = "SELECT * FROM users WHERE id_number = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_number);
    } else {
        $license_id = trim($_POST['license_id']);

        if (empty($license_id) || empty($password)) {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Fields',
                    text: 'Please enter your License Number and Password.'
                });
            </script>";
            exit();
        }

        $query = "SELECT * FROM officials WHERE license_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $license_id);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'User Not Found',
                text: 'No user matches the credentials.'
            });
        </script>";
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    $hashedPassword = $row['password'];

    if (!password_verify($password, $hashedPassword)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Incorrect Password',
                text: 'The password entered is incorrect.'
            });
        </script>";
        exit();
    }

    // Login successful, store session
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['type'] = $type;

    // Redirect based on type
    $redirectPage = '';

    if ($type === 'PWD') {
        $redirectPage = 'pwd/index.html';
    } elseif ($type === 'Health Officer') {
        $redirectPage = 'health_dashboard.php';
    } elseif ($type === 'Medical Officer') {
        $redirectPage = 'medical_dashboard.php';
    } elseif ($type === 'County Officer') {
        $redirectPage = 'county_dashboard.php';
    } else {
        $redirectPage = 'dashboard.php'; // fallback
    }

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Welcome back!',
            confirmButtonText: 'Continue'
        }).then(() => {
            window.location.href = '$redirectPage';
        });
    </script>";

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>