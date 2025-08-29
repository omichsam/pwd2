<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected county ID
    $county_id = $_POST['county_id'];

    // Prepare the SQL query with placeholders
    $query = "INSERT INTO sub_county (sub_county, county_id) VALUES (?, ?)";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, 'si', $sub_county, $county_id);

        // Iterate through each sub-county from the form
        foreach ($_POST['sub_county'] as $sub_county) {
            // Sanitize the sub-county name
            $sub_county = trim(mysqli_real_escape_string($conn, $sub_county));

            // Execute the prepared statement with the bound parameters
            if (!mysqli_stmt_execute($stmt)) {
                // If execution fails, store the error and stop further execution
                $error_message = "Error: " . mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                // Redirect with error message
                header('Location: List_Counties?status=error&message=' . urlencode($error_message));
                exit();
            }
        }

        // Close the statement
        mysqli_stmt_close($stmt);

        // Redirect with success message
        header('Location: List_Counties?status=success');
        exit();
    } else {
        // Handle errors with preparing the statement
        $error_message = "Error: " . mysqli_error($conn);
        // Redirect with error message
        header('Location: List_COunties?status=error&message=' . urlencode($error_message));
        exit();
    }
}
?>
