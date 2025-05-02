<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_connect.php';

    $license_id = trim($_POST['license_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobileNumber = trim($_POST['mobileNumber']);
    $type = trim($_POST['type']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // 1. Check for empty fields
    if (
        empty($license_id) || empty($name) || empty($email) ||
        empty($mobileNumber) || empty($type) || empty($password) || empty($confirmPassword)
    ) {
        header("Location: official_reg.php?status=empty");
        exit();
    }

    // 2. Check if passwords match
    if ($password !== $confirmPassword) {
        header("Location: official_reg.php?status=pass_mismatch");
        exit();
    }

    // 3. Check if user exists
    $checkQuery = 'SELECT id FROM officials WHERE license_id = ? OR email = ?';
    $stmtCheck = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, 'ss', $license_id, $email);
    mysqli_stmt_execute($stmtCheck);
    mysqli_stmt_store_result($stmtCheck);

    if (mysqli_stmt_num_rows($stmtCheck) > 0) {
        mysqli_stmt_close($stmtCheck);
        header("Location: official_reg.php?status=exists");
        exit();
    }
    mysqli_stmt_close($stmtCheck);

    // 4. Register new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertQuery = "INSERT INTO officials (license_id, name, email, mobile_number, type, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmtInsert, 'ssssss', $license_id, $name, $email, $mobileNumber, $type, $hashedPassword);

    if (mysqli_stmt_execute($stmtInsert)) {
        header("Location: official_reg.php?status=success");
    } else {
        header("Location: official_reg.php?status=fail");
    }

    mysqli_stmt_close($stmtInsert);
    mysqli_close($conn);
    exit();
}
?>