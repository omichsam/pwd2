<?php
function processPhysicalAssessment($conn)
{
    $assessment_id   = $_POST['assessment_id'] ?? null;
    $user_id         = $_POST['user_id'] ?? null;
    $disability_type = 'physical';
    $status          = 'checked';

    // Summary
    $medical_history        = $_POST['medical_history'] ?? '';
    $onset_date             = $_POST['onset_date'] ?? null;
    $last_intervention_date = $_POST['last_intervention_date'] ?? null;
    $interventions          = $_POST['interventions'] ?? '';
    $cause_of_disability    = $_POST['cause_of_disability'] ?? '';

    // Structural
    $region_assessed     = isset($_POST['region_assessed']) ? json_encode($_POST['region_assessed']) : json_encode([]);
    $regions_affected    = $_POST['regions_affected'] ?? '';
    $structural_findings = $_POST['structural_findings'] ?? '';
    $structural_remarks  = $_POST['structural_remarks'] ?? '';

    $impairments = [
        'muscle_power', 'joint_motion', 'structural_deviation', 'limb_amputation',
        'limb_length', 'balance_coordination', 'other_impairments',
    ];
    foreach ($impairments as $field) {
        ${"impairment_score_$field"} = $_POST["impairment_score_$field"] ?? '';
    }

    $score_none     = $_POST['score_none'] ?? 0;
    $score_mild     = $_POST['score_mild'] ?? 0;
    $score_moderate = $_POST['score_moderate'] ?? 0;
    $score_severe   = $_POST['score_severe'] ?? 0;
    $score_complete = $_POST['score_complete'] ?? 0;

    // Functional
    $functions = [
        'mobility', 'hand_use', 'grip_strength',
        'selfcare', 'daily_life', 'work',
    ];
    foreach ($functions as $key) {
        ${"function_$key"} = $_POST["function_$key"] ?? '';
    }

    $count_no_difficulty = $_POST['count_no_difficulty'] ?? 0;
    $count_mild          = $_POST['count_mild'] ?? 0;
    $count_moderate      = $_POST['count_moderate'] ?? 0;
    $count_severe        = $_POST['count_severe'] ?? 0;
    $count_complete      = $_POST['count_complete'] ?? 0;

    $remarks_functional  = $_POST['remarks_functional'] ?? '';
    $conclusion_duration = $_POST['conclusion_duration'] ?? '';
    $assistive_products  = $_POST['assistive_products'] ?? '';
    $other_services      = $_POST['other_services'] ?? '';

    // File upload
    $file_path = null;
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

    // Insert
    $sql = "INSERT INTO physical_disability_assessments (
        assessment_id, user_id, disability_type,
        medical_history, onset_date, last_intervention_date, interventions, cause_of_disability,
        region_assessed, regions_affected,
        impairment_score_muscle_power, impairment_score_joint_motion, impairment_score_structural_deviation,
        impairment_score_limb_amputation, impairment_score_limb_length, impairment_score_balance_coordination,
        impairment_score_other_impairments,
        structural_findings, structural_remarks,
        score_none, score_mild, score_moderate, score_severe, score_complete,
        function_mobility, function_hand_use, function_grip_strength,
        function_selfcare, function_daily_life, function_work,
        count_no_difficulty, count_mild, count_moderate, count_severe, count_complete,
        remarks_functional, conclusion_duration,
        assistive_products, other_services, supporting_document
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "iissssssssssssssssiiiissssssiiiiiissss",
            $assessment_id, $user_id, $disability_type,
            $medical_history, $onset_date, $last_intervention_date, $interventions, $cause_of_disability,
            $region_assessed, $regions_affected,
            $impairment_score_muscle_power, $impairment_score_joint_motion, $impairment_score_structural_deviation,
            $impairment_score_limb_amputation, $impairment_score_limb_length, $impairment_score_balance_coordination,
            $impairment_score_other_impairments,
            $structural_findings, $structural_remarks,
            $score_none, $score_mild, $score_moderate, $score_severe, $score_complete,
            $function_mobility, $function_hand_use, $function_grip_strength,
            $function_selfcare, $function_daily_life, $function_work,
            $count_no_difficulty, $count_mild, $count_moderate, $count_severe, $count_complete,
            $remarks_functional, $conclusion_duration,
            $assistive_products, $other_services, $file_path
        );

        if (mysqli_stmt_execute($stmt)) {
            $medical_officer_id = $_SESSION['user_id'] ?? 1;
            $update_sql         = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability_type, $medical_officer_id, $status, $assessment_id);
                if (mysqli_stmt_execute($update_stmt)) {
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
                    echo "Update error: " . mysqli_stmt_error($update_stmt);
                }
                mysqli_stmt_close($update_stmt);
            }
        } else {
            echo "Insert error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "SQL error: " . mysqli_error($conn);
    }
}
?>