<?php
// session_start();
require_once '../files/db_connect.php'; // database connection file

echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
// $pwdUser = $_SESSION['user']
// Ensure user is logged in

$user_id = $pwdUser['id'];

if (!isset($_SESSION['user'])) {
    echo "
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Not Logged In',
                text: 'Please log in to change your password.'
            }).then(() => {
                window.location.href = '../login.php';
            });
        </script>";
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['password_confirm'];

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Mismatch',
                    text: 'New passwords do not match.'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        exit();
    }

    // Fetch current password hash from DB
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (!password_verify($current_password, $row['password'])) {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Incorrect Password',
                        text: 'Your current password is incorrect.'
                    }).then(() => {
                        window.history.back();
                    });
                </script>";
            exit();
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);

        if (mysqli_stmt_execute($update_stmt)) {
            echo "
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Password changed successfully.'
                    }).then(() => {
                        window.location.href = 'dashboard.php';
                    });
                </script>";
        } else {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error',
                        text: 'Failed to update password.'
                    });
                </script>";
        }

        mysqli_stmt_close($update_stmt);
    } else {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'User Not Found',
                    text: 'Unable to verify user.'
                });
            </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>