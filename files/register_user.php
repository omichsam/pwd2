<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Start output buffering to prevent header issues
    ob_start();

    // Load SweetAlert2 library
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    // Get and sanitize POST data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $dob = htmlspecialchars($_POST['dob']);
    $gender = htmlspecialchars($_POST['gender']);
    $occupation = htmlspecialchars($_POST['occupation']);
    $mobile_number = htmlspecialchars($_POST['mobileNumber']);
    $type = htmlspecialchars($_POST['type']);
    $id_number = htmlspecialchars($_POST['id_number']);
    $marital_status = htmlspecialchars($_POST['maritalStatus']);
    $education_level = htmlspecialchars($_POST['educationLevel']);
    $next_of_kin_name = htmlspecialchars($_POST['nextOfKinName']);
    $next_of_kin_mobile = htmlspecialchars($_POST['nextOfKinMobile']);
    $next_of_kin_relationship = htmlspecialchars($_POST['nextOfKinRelationship']);
    $county_id = htmlspecialchars($_POST['county_id']);
    $subcounty = htmlspecialchars($_POST['subcounty']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Passwords do not match.',
                        confirmButtonText: 'Try Again'
                    }).then(() => {
                        window.history.back();
                    });
                });
            </script>";
        exit();
    }

    // // 2. Check password strength (minimum 8 chars, at least one number and one letter)
    // if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
    //     echo "
    //         <script>
    //             document.addEventListener('DOMContentLoaded', function() {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Weak Password',
    //                     html: 'Password must:<br>- Be at least 8 characters<br>- Contain at least one letter<br>- Contain at least one number',
    //                     confirmButtonText: 'Try Again'
    //                 }).then(() => {
    //                     window.history.back();
    //                 });
    //             });
    //         </script>";
    //     exit();
    // }

    // 3. Check if user already exists (by email or id_number)
    $check_query = "SELECT * FROM users WHERE email = ? OR id_number = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $email, $id_number);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Account Exists',
                        html: 'An account with this:<br><b>" . (mysqli_fetch_assoc($result)['email'] === $email ? 'Email' : 'ID Number') . "</b> already exists.',
                        confirmButtonText: 'Try Again'
                    }).then(() => {
                        window.history.back();
                    });
                });
            </script>";
        exit();
    }

    // 4. Proceed with registration
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (
        name, email, dob, gender, occupation, mobile_number, type, id_number,
        marital_status, education_level, next_of_kin_name, next_of_kin_mobile,
        next_of_kin_relationship, county_id, subcounty, password
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssssss",
            $name,
            $email,
            $dob,
            $gender,
            $occupation,
            $mobile_number,
            $type,
            $id_number,
            $marital_status,
            $education_level,
            $next_of_kin_name,
            $next_of_kin_mobile,
            $next_of_kin_relationship,
            $county_id,
            $subcounty,
            $hashed_password
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Registration Successful!',
                            icon: 'success',
                            html: 'Account created successfully!<br><br>You can now login with your credentials.',
                            confirmButtonText: 'Go to Login'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login.php';
                            }
                        });
                    });
                </script>";
        } else {
            echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: 'Error: " . addslashes(mysqli_error($conn)) . "',
                            confirmButtonText: 'Try Again'
                        }).then(() => {
                            window.history.back();
                        });
                    });
                </script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'System Error',
                        text: 'Failed to prepare registration statement.',
                        confirmButtonText: 'Try Again'
                    }).then(() => {
                        window.history.back();
                    });
                });
            </script>";
    }

    mysqli_stmt_close($check_stmt);
    mysqli_close($conn);

    // Flush output buffer
    ob_end_flush();
}
?>