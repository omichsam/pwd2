<?php

function processPhysicalDisabilityAssessment($conn)
{
    // Initialize variables and handle the form data
    $error_message = "";

    // Retrieve form data from the POST request
    $assessment_id = $_POST['assessment_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $onset_date = $_POST['onset_date'] ?? '';
    $last_intervention_date = $_POST['last_intervention_date'] ?? '';
    $cause_of_disability = $_POST['cause_of_disability'] ?? '';
    $regions_affected = $_POST['regions_affected'] ?? '';
    $impairment_score_muscle_power = $_POST['impairment_score_muscle_power'] ?? '';
    $remark_muscle_power = $_POST['remark_muscle_power'] ?? '';
    $impairment_score_joint_motion = $_POST['impairment_score_joint_motion'] ?? '';
    $remark_joint_motion = $_POST['remark_joint_motion'] ?? '';
    $impairment_score_structural_deviation = $_POST['impairment_score_structural_deviation'] ?? '';
    $remark_structural_deviation = $_POST['remark_structural_deviation'] ?? '';
    $impairment_score_limb_amputation = $_POST['impairment_score_limb_amputation'] ?? '';
    $remark_limb_amputation = $_POST['remark_limb_amputation'] ?? '';
    $impairment_score_limb_length = $_POST['impairment_score_limb_length'] ?? '';
    $remark_limb_length = $_POST['remark_limb_length'] ?? '';
    $impairment_score_balance_coordination = $_POST['impairment_score_balance_coordination'] ?? '';
    $remark_balance_coordination = $_POST['remark_balance_coordination'] ?? '';
    $impairment_score_other_impairments = $_POST['impairment_score_other_impairments'] ?? '';
    $remark_other_impairments = $_POST['remark_other_impairments'] ?? '';
    $impairment_rating = $_POST['impairment_rating'] ?? '';
    $function_mobility = $_POST['function_mobility'] ?? '';
    $remark_mobility = $_POST['remark_mobility'] ?? '';
    $function_hand_use = $_POST['function_hand_use'] ?? '';
    $remark_hand_use = $_POST['remark_hand_use'] ?? '';
    $function_grip_strength = $_POST['function_grip_strength'] ?? '';
    $remark_grip_strength = $_POST['remark_grip_strength'] ?? '';
    $function_selfcare = $_POST['function_selfcare'] ?? '';
    $remark_selfcare = $_POST['remark_selfcare'] ?? '';
    $function_daily_life = $_POST['function_daily_life'] ?? '';
    $remark_daily_life = $_POST['remark_daily_life'] ?? '';
    $function_work = $_POST['function_work'] ?? '';
    $remark_work = $_POST['remark_work'] ?? '';
    $disability_rating = $_POST['disability_rating'] ?? '';
    $assistive_products = $_POST['assistive_products'] ?? '';
    $other_services = $_POST['other_services'] ?? '';
    $conclusion_decision = $_POST['conclusion_decision'] ?? '';

    // File upload for supporting document
    $file_path = null;
    if (!empty($_FILES['supporting_document']['name'])) {
        $ext = strtolower(pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_path = $upload_dir . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['supporting_document']['tmp_name'], $file_path)) {
                // Insert into documents table
                $document_type = "physical_disability_supporting";
                $doc_sql = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
                if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                    mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                    mysqli_stmt_execute($doc_stmt);
                    mysqli_stmt_close($doc_stmt);
                }
            }
        }
    }

    // Insert into physical_disability_assessment - MOVE THIS BEFORE THE DEBUG CODE
    $sql = "INSERT INTO physical_disability_assessment (
        assessment_id, user_id, onset_date, last_intervention_date, cause_of_disability, 
        regions_affected, impairment_score_muscle_power, remark_muscle_power, impairment_score_joint_motion,
        remark_joint_motion, impairment_score_structural_deviation, remark_structural_deviation, 
        impairment_score_limb_amputation, remark_limb_amputation, impairment_score_limb_length, 
        remark_limb_length, impairment_score_balance_coordination, remark_balance_coordination, 
        impairment_score_other_impairments, remark_other_impairments, impairment_rating, 
        function_mobility, remark_mobility, function_hand_use, remark_hand_use, function_grip_strength, 
        remark_grip_strength, function_selfcare, remark_selfcare, function_daily_life, remark_daily_life, 
        function_work, remark_work, disability_rating, assistive_products, other_services, 
        conclusion_decision, supporting_document
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Debug: Count the parameters - MOVED AFTER SQL DEFINITION
    $parameters = [
        $assessment_id,                    // i
        $user_id,                         // i
        $onset_date,                      // s
        $last_intervention_date,          // s
        $cause_of_disability,             // s
        $regions_affected,                // s
        $impairment_score_muscle_power,   // s
        $remark_muscle_power,             // s
        $impairment_score_joint_motion,   // s
        $remark_joint_motion,             // s
        $impairment_score_structural_deviation, // s
        $remark_structural_deviation,     // s
        $impairment_score_limb_amputation, // s
        $remark_limb_amputation,          // s
        $impairment_score_limb_length,    // s
        $remark_limb_length,              // s
        $impairment_score_balance_coordination, // s
        $remark_balance_coordination,     // s
        $impairment_score_other_impairments, // s
        $remark_other_impairments,        // s
        $impairment_rating,               // s
        $function_mobility,               // s
        $remark_mobility,                 // s
        $function_hand_use,               // s
        $remark_hand_use,                 // s
        $function_grip_strength,          // s
        $remark_grip_strength,            // s
        $function_selfcare,               // s
        $remark_selfcare,                 // s
        $function_daily_life,             // s
        $remark_daily_life,               // s
        $function_work,                   // s
        $remark_work,                     // s
        $disability_rating,               // s
        $assistive_products,              // s
        $other_services,                  // s
        $conclusion_decision,             // s
        $file_path                        // s
    ];

    // Count SQL placeholders
    $sql_placeholders = substr_count($sql, '?');
    $param_count = count($parameters);
    
    if ($sql_placeholders !== $param_count) {
        error_log("SQL placeholders: $sql_placeholders, Parameters: $param_count");
        echo "<script>alert('Parameter count mismatch: SQL expects $sql_placeholders but got $param_count');</script>";
        return false;
    }

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Correct type string: 2 integers + 36 strings = 38 parameters total
        $type_string = "ii" . str_repeat("s", 36); // "iissssssssssssssssssssssssssssssssss"
        
        $bound = mysqli_stmt_bind_param(
            $stmt,
            $type_string,
            $assessment_id,
            $user_id,
            $onset_date,
            $last_intervention_date,
            $cause_of_disability,
            $regions_affected,
            $impairment_score_muscle_power,
            $remark_muscle_power,
            $impairment_score_joint_motion,
            $remark_joint_motion,
            $impairment_score_structural_deviation,
            $remark_structural_deviation,
            $impairment_score_limb_amputation,
            $remark_limb_amputation,
            $impairment_score_limb_length,
            $remark_limb_length,
            $impairment_score_balance_coordination,
            $remark_balance_coordination,
            $impairment_score_other_impairments,
            $remark_other_impairments,
            $impairment_rating,
            $function_mobility,
            $remark_mobility,
            $function_hand_use,
            $remark_hand_use,
            $function_grip_strength,
            $remark_grip_strength,
            $function_selfcare,
            $remark_selfcare,
            $function_daily_life,
            $remark_daily_life,
            $function_work,
            $remark_work,
            $disability_rating,
            $assistive_products,
            $other_services,
            $conclusion_decision,
            $file_path
        );

        if (!$bound) {
            echo "Bind error: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return false;
        }

        if (mysqli_stmt_execute($stmt)) {
            // Update the assessment record
            $medical_officer_id = $_SESSION['user_id'] ?? 1;
            $status = "checked";
            $disability = "Physical";

            $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Physical disability assessment saved.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'complete_assessment';
                            });
                        });
                    </script>";
                    mysqli_stmt_close($update_stmt);
                    mysqli_stmt_close($stmt);
                    return true;
                } else {
                    echo "Update error: " . mysqli_stmt_error($update_stmt);
                    mysqli_stmt_close($update_stmt);
                }
            }
        } else {
            echo "Insert error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "SQL error: " . mysqli_error($conn);
    }
    
    return false;
}

?>