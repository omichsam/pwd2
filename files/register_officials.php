<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_connect.php';

    // Sanitize input
    $license_id     = trim($_POST['license_id']);
    $name           = trim($_POST['name']);
    $email          = trim($_POST['email']);
    $mobileNumber   = trim($_POST['mobileNumber']);
    $type           = trim($_POST['type']);
    $id_number      = trim($_POST['id_number']);
    $specialisation = trim($_POST['specialisation']);
    $department     = trim($_POST['department']);
    $county_id      = intval($_POST['county_id']);
    // $hospital_id = intval($_POST['hospital_id']); // Commented as per request
    $password       = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // 1. Check for empty fields
    if (
        empty($license_id) || empty($name) || empty($email) || empty($mobileNumber) || empty($type) ||
        empty($password) || empty($confirmPassword) || empty($id_number) || empty($specialisation) ||
        empty($department) || empty($county_id)
    ) {
        header("Location: official_reg.php?status=empty");
        exit();
    }

    // 2. Password match
    if ($password !== $confirmPassword) {
        header("Location: official_reg.php?status=pass_mismatch");
        exit();
    }

    // 3. Check for duplicates
    $checkQuery = "SELECT id FROM officials WHERE license_id = ? OR email = ?";
    $stmtCheck = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, "ss", $license_id, $email);
    mysqli_stmt_execute($stmtCheck);
    mysqli_stmt_store_result($stmtCheck);

    if (mysqli_stmt_num_rows($stmtCheck) > 0) {
        mysqli_stmt_close($stmtCheck);
        header("Location: official_reg.php?status=exists");
        exit();
    }
    mysqli_stmt_close($stmtCheck);

    // 4. Hash password and set active status
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $active = ($type === 'countyofficer') ? 1 : 0;

    // 5. Insert query (hospital_id omitted, created_at is auto-handled)
    $insertQuery = "INSERT INTO officials 
        (license_id, name, email, id_number, mobile_number, type, specialisation, department, password, county_id, active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmtInsert = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param(
        $stmtInsert,
        'ssssssssssi',
        $license_id,
        $name,
        $email,
        $id_number,
        $mobileNumber,
        $type,
        $specialisation,
        $department,
        $hashedPassword,
        $county_id,
        $active
    );

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