<?php
// include '../files/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_name = mysqli_real_escape_string($conn, trim($_POST['hospital_name']));
    $county_name = mysqli_real_escape_string($conn, trim($_POST['county_name']));
    $sub_county = mysqli_real_escape_string($conn, trim($_POST['sub_county']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    if (!empty($hospital_name) && !empty($county_name) && !empty($sub_county) && !empty($address)) {
        $sql = "INSERT INTO hospitals (name, county, subcounty, address) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $hospital_name, $county_name, $sub_county, $address);
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
