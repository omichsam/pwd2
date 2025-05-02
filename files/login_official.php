<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_input = trim($_POST['id_or_license']);
    $password = $_POST['password'];
    $official_type = trim($_POST['official_type']);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    if (empty($user_input) || empty($password) || empty($official_type)) {
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Missing Input',
                text: 'Please fill in all fields.'
            });
        </script>";
        exit;
    }

    // Check if input matches either ide_number or license_number and type
    $sql = "SELECT * FROM officials WHERE (ide_number = ? OR license_number = ?) AND official_type = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $user_input, $user_input, $official_type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($official = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $official['password'])) {
            $_SESSION['official_id'] = $official['id'];
            $_SESSION['official_name'] = $official['name'];
            $_SESSION['official_type'] = $official['official_type'];

            // Redirect dashboard by type
            switch (strtolower($official['official_type'])) {
                case 'medical officer':
                    $dashboard = 'medical_dashboard.php';
                    break;
                case 'health officer':
                    $dashboard = 'health_dashboard.php';
                    break;
                case 'county officer':
                    $dashboard = 'county_dashboard.php';
                    break;
                default:
                    $dashboard = 'general_dashboard.php';
            }

            echo "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Welcome, {$official['official_type']}!',
                    confirmButtonText: 'Proceed'
                }).then(() => {
                    window.location.href = '$dashboard';
                });
            </script>";
        } else {
            echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Password',
                    text: 'Please try again.'
                });
            </script>";
        }
    } else {
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'User Not Found',
                text: 'No official found with that ID/License and type.'
            });
        </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
