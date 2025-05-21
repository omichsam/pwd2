<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    // Get POST data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $occupation = $_POST['occupation'];
    $mobile_number = $_POST['mobileNumber'];
    $type = $_POST['type'];
    $id_number = $_POST['id_number'];
    $marital_status = $_POST['maritalStatus'];
    $education_level = $_POST['educationLevel'];
    $next_of_kin_name = $_POST['nextOfKinName'];
    $next_of_kin_mobile = $_POST['nextOfKinMobile'];
    $next_of_kin_relationship = $_POST['nextOfKinRelationship'];
    $county = $_POST['county'];
    $subcounty = $_POST['subcounty'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Passwords do not match.'
                });
            </script>";
        exit();
    }

    // 2. Check if user already exists (by email or id_number)
    $check_query = "SELECT * FROM users WHERE email = ? OR id_number = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $email, $id_number);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'User Exists',
                    text: 'An account with this email or ID number already exists.'
                });
            </script>";
        exit();
    }

    // 3. Proceed with registration
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (
        name, email, dob, gender, occupation, mobile_number, type, id_number,
        marital_status, education_level, next_of_kin_name, next_of_kin_mobile,
        next_of_kin_relationship, county, subcounty, password
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssssssssssss",
            $name, $email, $dob, $gender, $occupation, $mobile_number, $type, $id_number,
            $marital_status, $education_level, $next_of_kin_name, $next_of_kin_mobile,
            $next_of_kin_relationship, $county, $subcounty, $hashed_password
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "
                <script>
                    Swal.fire({
                        title: 'Success!',
                        text: 'Registration successful.',
                        icon: 'success',
                        confirmButtonText: 'Proceed to Login'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                </script>";
        } else {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error',
                        text: 'Something went wrong while saving data.'
                    });
                </script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to prepare SQL statement.'
                });
            </script>";
    }

    mysqli_stmt_close($check_stmt);
    mysqli_close($conn);
}
?>
