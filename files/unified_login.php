<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ide_number = trim($_POST['ide_number']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; // Get selected user type

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    if (empty($ide_number) || empty($password) || empty($user_type)) {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Input',
                    text: 'Please enter all required fields.'
                });
            </script>";
        exit;
    }

    // Check if the user is logging in as "user" or "official"
    if ($user_type === 'user') {
        // Query for User login
        $sql = "SELECT * FROM users WHERE id_number = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ide_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['ide_number'] = $user['ide_number'];
                $_SESSION['name'] = $user['name'];

                echo "
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: 'Welcome back!',
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
                        text: 'Invalid License/ID Number.'
                    });
                </script>";
        }
    } elseif ($user_type === 'official') {
        // Query for Official login
        $sql = "SELECT * FROM officials WHERE ide_number = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ide_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($official = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $official['password'])) {
                // Set session
                $_SESSION['official_id'] = $official['id'];
                $_SESSION['ide_number'] = $official['ide_number'];
                $_SESSION['official_name'] = $official['name'];
                $_SESSION['official_type'] = $official['official_type'];

                // Role-based redirect
                $dashboard = '';
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
                            text: 'Welcome, " . $official['official_type'] . "!',
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
                        text: 'Invalid ID or License Number.'
                    });
                </script>";
        }
    } else {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid User Type',
                    text: 'Please select a valid login type.'
                });
            </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
