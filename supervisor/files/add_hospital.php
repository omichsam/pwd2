<?php
// include '../files/db_connect.php';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_name = mysqli_real_escape_string($conn, trim($_POST['hospital_name']));
    $county_id = mysqli_real_escape_string($conn, trim($_POST['county_id']));
    $sub_county = mysqli_real_escape_string($conn, trim($_POST['subcounty']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    if (!empty($hospital_name) && !empty($county_id) && !empty($sub_county) && !empty($address)) {
        $sql = "INSERT INTO hospitals (name, county_id, subcounty, address) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $hospital_name, $county_id, $sub_county, $address);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                header("Location: Add_Hospital.php?status=success");
                exit();
            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                header("Location: Add_Hospital.php?status=error");
                exit();
            }
        } else {
            header("Location: Add_Hospital.php?status=error");
            exit();
        }
    } else {
        header("Location: Add_Hospital.php?status=error");
        exit();
    }
}
?>
