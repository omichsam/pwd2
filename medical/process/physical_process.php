<?php

function processPhysicalAssessment($conn)
{
    // session_start(); // Uncomment if session not already started
    $status          = 'checked';
    $disability_type = 'Physical';

    $assessment_id = $_POST['assessment_id'] ?? null;
    $user_id       = $_POST['user_id'] ?? null;

    $onset_date             = $_POST['onset_date'] ?? null;
    $last_intervention_date = $_POST['last_intervention_date'] ?? null;
    $cause_of_disability    = $_POST['cause_of_disability'] ?? '';
    $region_assessed        = json_encode([]); // Replace if frontend provides data
    $regions_affected       = $_POST['regions_affected'] ?? '';

    // Impairments
    $impairment_score_muscle_power         = $_POST['impairment_score_muscle_power'] ?? '';
    $impairment_score_joint_motion         = $_POST['impairment_score_joint_motion'] ?? '';
    $impairment_score_structural_deviation = $_POST['impairment_score_structural_deviation'] ?? '';
    $impairment_score_limb_amputation      = $_POST['impairment_score_limb_amputation'] ?? '';
    $impairment_score_limb_length          = $_POST['impairment_score_limb_length'] ?? '';
    $impairment_score_balance_coordination = $_POST['impairment_score_balance_coordination'] ?? '';
    $impairment_score_other_impairments    = $_POST['impairment_score_other_impairments'] ?? '';

    $structural_findings = $_POST['structural_findings'] ?? '';
    $structural_remarks  = $_POST['structural_remarks'] ?? '';

    $score_none     = (int) ($_POST['score_none'] ?? 0);
    $score_mild     = (int) ($_POST['score_mild'] ?? 0);
    $score_moderate = (int) ($_POST['score_moderate'] ?? 0);
    $score_severe   = (int) ($_POST['score_severe'] ?? 0);
    $score_complete = (int) ($_POST['score_complete'] ?? 0);

    $function_mobility      = $_POST['function_mobility'] ?? '';
    $function_hand_use      = $_POST['function_hand_use'] ?? '';
    $function_grip_strength = $_POST['function_grip_strength'] ?? '';
    $function_selfcare      = $_POST['function_selfcare'] ?? '';
    $function_daily_life    = $_POST['function_daily_life'] ?? '';
    $function_work          = $_POST['function_work'] ?? '';

    $count_no_difficulty = (int) ($_POST['count_no_difficulty'] ?? 0);
    $count_mild          = (int) ($_POST['count_mild'] ?? 0);
    $count_moderate      = (int) ($_POST['count_moderate'] ?? 0);
    $count_severe        = (int) ($_POST['count_severe'] ?? 0);
    $count_complete      = (int) ($_POST['count_complete'] ?? 0);

    $remarks_functional  = $_POST['remarks_functional'] ?? '';
    $conclusion_duration = $_POST['conclusion_duration'] ?? '';
    $assistive_products  = $_POST['assistive_products'] ?? '';
    $other_services      = $_POST['other_services'] ?? '';

    // Optional file upload — save separately
    if (! empty($_FILES['supporting_document']['name'])) {
        $ext = strtolower(pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $upload_dir = "../uploads/";
            if (! is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_path = $upload_dir . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['supporting_document']['tmp_name'], $file_path);

            $doc_sql = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                $document_type = "physical_supporting";
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // Insert into DB
    $sql = "INSERT INTO physical_disability_assessments (
        assessment_id, user_id, disability_type,
        onset_date, last_intervention_date, cause_of_disability, region_assessed, regions_affected,
        impairment_score_muscle_power, impairment_score_joint_motion, impairment_score_structural_deviation,
        impairment_score_limb_amputation, impairment_score_limb_length, impairment_score_balance_coordination,
        impairment_score_other_impairments,
        structural_findings, structural_remarks,
        score_none, score_mild, score_moderate, score_severe, score_complete,
        function_mobility, function_hand_use, function_grip_strength,
        function_selfcare, function_daily_life, function_work,
        count_no_difficulty, count_mild, count_moderate, count_severe, count_complete,
        remarks_functional, conclusion_duration, assistive_products, other_services
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // echo "Counted types: " . strlen("iisssssssssssssssiiiissssssiiiiiissss") . "<br>";
    // echo "Counted vars: " . count([
    //     $assessment_id, $user_id, $disability_type,
    //     $onset_date, $last_intervention_date, $cause_of_disability, $region_assessed, $regions_affected,
    //     $impairment_score_muscle_power, $impairment_score_joint_motion, $impairment_score_structural_deviation,
    //     $impairment_score_limb_amputation, $impairment_score_limb_length, $impairment_score_balance_coordination,
    //     $impairment_score_other_impairments,
    //     $structural_findings, $structural_remarks,
    //     $score_none, $score_mild, $score_moderate, $score_severe, $score_complete,
    //     $function_mobility, $function_hand_use, $function_grip_strength,
    //     $function_selfcare, $function_daily_life, $function_work,
    //     $count_no_difficulty, $count_mild, $count_moderate, $count_severe, $count_complete,
    //     $remarks_functional, $conclusion_duration, $assistive_products, $other_services,
    // ]) . "<br>";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "iisssssssssssssssiiiissssssiiiiiissss",
            $assessment_id, $user_id, $disability_type,
            $onset_date, $last_intervention_date, $cause_of_disability, $region_assessed, $regions_affected,
            $impairment_score_muscle_power, $impairment_score_joint_motion, $impairment_score_structural_deviation,
            $impairment_score_limb_amputation, $impairment_score_limb_length, $impairment_score_balance_coordination,
            $impairment_score_other_impairments,
            $structural_findings, $structural_remarks,
            $score_none, $score_mild, $score_moderate, $score_severe, $score_complete,
            $function_mobility, $function_hand_use, $function_grip_strength,
            $function_selfcare, $function_daily_life, $function_work,
            $count_no_difficulty, $count_mild, $count_moderate, $count_severe, $count_complete,
            $remarks_functional, $conclusion_duration, $assistive_products, $other_services
        );

        if (mysqli_stmt_execute($stmt)) {
            $medical_officer_id = $_SESSION['user_id'] ?? 1;
            $update_sql         = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability_type, $medical_officer_id, $status, $assessment_id);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
            }

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Assessment saved.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'complete_assessment';
                    });
                });
            </script>";
        } else {
            echo "Insert error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "SQL error: " . mysqli_error($conn);
    }
}
