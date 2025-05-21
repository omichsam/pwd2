<?php

// Get logged-in user and medical officer ID from session
$user_id = $pwdUser['id'];               // Logged-in user   

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data safely
    $assessment_id = mysqli_real_escape_string($conn, $_POST['assessment_id']);
    $history_of_hearing_loss = mysqli_real_escape_string($conn, $_POST['history_of_hearing_loss']);
    $history_of_hearing_devices = mysqli_real_escape_string($conn, $_POST['history_of_hearing_devices']);
    $hearing_test_type_right = mysqli_real_escape_string($conn, $_POST['hearing_loss_type_right']);
    $hearing_test_type_left = mysqli_real_escape_string($conn, $_POST['hearing_loss_type_left']);
    $hearing_loss_degree_right = (int) $_POST['hearing_grade_right'];
    $hearing_loss_degree_left = (int) $_POST['hearing_grade_left'];
    $hearing_level_dbhl_right = (int) $_POST['hearing_level_dbhl_right'];
    $hearing_level_dbhl_left = (int) $_POST['hearing_level_dbhl_left'];
    $monaural_percentage_right = (int) $_POST['monoaural_percent_right'];
    $monaural_percentage_left = (int) $_POST['monoaural_percent_left'];
    $overall_binaural_percentage = (float) $_POST['binaural_percent'];
    $conclusion = mysqli_real_escape_string($conn, $_POST['hearing_disability_conclusion']);
    $recommended_assistive_products = mysqli_real_escape_string($conn, $_POST['recommended_assistive_products']);
    $required_services = mysqli_real_escape_string($conn, $_POST['required_services']);

    // INSERT into hearing_disability_assessments
    $insert_sql = "INSERT INTO hearing_disability_assessments (
        assessment_id,
        history_of_hearing_loss,
        history_of_hearing_devices,
        hearing_test_type_right,
        hearing_test_type_left,
        hearing_loss_degree_right,
        hearing_loss_degree_left,
        hearing_level_dbhl_right,
        hearing_level_dbhl_left,
        monaural_percentage_right,
        monaural_percentage_left,
        overall_binaural_percentage,
        conclusion,
        recommended_assistive_products,
        required_services
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param(
        $stmt,
        'issssiiiiiidsss',
        $assessment_id,
        $history_of_hearing_loss,
        $history_of_hearing_devices,
        $hearing_test_type_right,
        $hearing_test_type_left,
        $hearing_loss_degree_right,
        $hearing_loss_degree_left,
        $hearing_level_dbhl_right,
        $hearing_level_dbhl_left,
        $monaural_percentage_right,
        $monaural_percentage_left,
        $overall_binaural_percentage,
        $conclusion,
        $recommended_assistive_products,
        $required_services
    );

    if (mysqli_stmt_execute($stmt)) {
        // Now update the assessments table
        $update_sql = "UPDATE assessments 
                       SET disability_type = 'hearing_Impairments', 
                           medical_officer_id = ?
                       WHERE id = ?";

        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, 'ii', $user_id, $assessment_id);

        if (mysqli_stmt_execute($update_stmt)) {
            echo "<script>alert('Hearing assessment saved and assessment updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating assessment record: " . mysqli_error($conn) . "');</script>";
        }

        mysqli_stmt_close($update_stmt);
    } else {
        echo "<script>alert('Error inserting hearing assessment: " . mysqli_error($conn) . "');</script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>