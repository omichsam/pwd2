<?php
include 'files/header.php'; 
include '../files/db_connect.php'; // Ensure you have a valid connection

// Check if the request is to view sub-counties for a particular county
if (isset($_POST['county_id'])) {
    $county_id = (int)$_POST['county_id']; // Sanitize input to avoid SQL injection

    // Query to fetch sub-counties for the selected county
    $subcounty_query = "SELECT * FROM sub_county WHERE county_id = ?";

    // Prepare the statement and bind parameters
    if ($stmt = mysqli_prepare($conn, $subcounty_query)) {
        mysqli_stmt_bind_param($stmt, "i", $county_id); // Bind county_id as integer
        mysqli_stmt_execute($stmt);
        $subcounty_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($subcounty_result) > 0) {
            $sub_counties = mysqli_fetch_all($subcounty_result, MYSQLI_ASSOC);
            echo json_encode(['status' => 'success', 'sub_counties' => $sub_counties]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No sub-counties found for this county.']);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
    }
    exit;
}
?>
