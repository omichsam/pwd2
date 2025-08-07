<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ide_number = $_POST['ide_number'];
    $password = $_POST['password'];

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    // 1. Check if user exists by ide_number
    $query = "SELECT * FROM users WHERE ide_number = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $ide_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['password'];

        // 2. Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['ide_number'] = $user['ide_number'];
            $_SESSION['user_name'] = $user['name'];

            echo "
                <script>
                    Swal.fire({
                        title: 'Login Successful!',
                        icon: 'success',
                        confirmButtonText: 'Continue'
                    }).then(() => {
                        window.location.href = 'user_dashboard.php';
                    });
                </script>";
        } else {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Incorrect Password',
                        text: 'Please check your password and try again.'
                    });
                </script>";
        }
    } else {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'User Not Found',
                    text: 'No user found with the provided ID number.'
                });
            </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
