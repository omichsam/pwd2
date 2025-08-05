<?php

function processVisualAssessment($conn)
{
    $assessment_id = $_POST['assessment_id'] ?? null;
    $user_id       = $_POST['user_id'] ?? null;

    // Background
    $assistive_device = $_POST['assistive_device'] ?? '';
    $medical_history  = $_POST['medical_history'] ?? '';
    $ocular_history   = $_POST['ocular_history'] ?? '';

    // Visual acuity
    $right_eye_with_correction      = $_POST['right_eye_with_correction'] ?? '';
    $right_eye_without_correction   = $_POST['right_eye_without_correction'] ?? '';
    $left_eye_with_correction       = $_POST['left_eye_with_correction'] ?? '';
    $left_eye_without_correction    = $_POST['left_eye_without_correction'] ?? '';
    $near_vision_with_correction    = $_POST['near_vision_with_correction'] ?? '';
    $near_vision_without_correction = $_POST['near_vision_without_correction'] ?? '';

    // Right Eye
    $present_eyeball_right  = $_POST['present_eyeball_right'] ?? '';
    $squint_right           = $_POST['squint_right'] ?? '';
    $nystagmus_right        = $_POST['nystagmus_right'] ?? '';
    $tearing_right          = $_POST['tearing_right'] ?? '';
    $lids_right             = $_POST['lids_right'] ?? '';
    $conjunctiva_right      = $_POST['conjunctiva_right'] ?? '';
    $cornea_right           = $_POST['cornea_right'] ?? '';
    $anterior_chamber_right = $_POST['anterior_chamber_right'] ?? '';
    $iris_right             = $_POST['iris_right'] ?? '';
    $pupil_right            = $_POST['pupil_right'] ?? '';
    $lens_right             = $_POST['lens_right'] ?? '';
    $fundus_right           = $_POST['fundus_right'] ?? '';

    // Left Eye
    $present_eyeball_left  = $_POST['present_eyeball_left'] ?? '';
    $squint_left           = $_POST['squint_left'] ?? '';
    $nystagmus_left        = $_POST['nystagmus_left'] ?? '';
    $tearing_left          = $_POST['tearing_left'] ?? '';
    $lids_left             = $_POST['lids_left'] ?? '';
    $conjunctiva_left      = $_POST['conjunctiva_left'] ?? '';
    $cornea_left           = $_POST['cornea_left'] ?? '';
    $anterior_chamber_left = $_POST['anterior_chamber_left'] ?? '';
    $iris_left             = $_POST['iris_left'] ?? '';
    $pupil_left            = $_POST['pupil_left'] ?? '';
    $lens_left             = $_POST['lens_left'] ?? '';
    $fundus_left           = $_POST['fundus_left'] ?? '';

    // Specialized tests
    $hvf           = $_POST['hvf'] ?? '';
    $colour_vision = $_POST['colour_vision'] ?? '';
    $stereopsis    = $_POST['stereopsis'] ?? '';

    // Conclusion
    $category              = $_POST['category'] ?? '';
    $cause_of_vision       = $_POST['cause_of_vision'] ?? '';
    $percentage_disability = $_POST['percentage_disability'] ?? '';
    $possible_intervention = $_POST['possible_intervention'] ?? '';
    $recommendation        = $_POST['recommendation'] ?? '';
    $conclusion_duration   = $_POST['conclusion_duration'] ?? '';

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

            // Optional: store in documents table
            $document_type = "visual_supporting";
            $doc_sql       = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // Insert into visual_assessment_details
    $sql = "INSERT INTO visual_assessment_details (
        assessment_id, user_id, assistive_device, medical_history, ocular_history,
        right_eye_with_correction, right_eye_without_correction,
        left_eye_with_correction, left_eye_without_correction,
        near_vision_with_correction, near_vision_without_correction,
        present_eyeball_right, squint_right, nystagmus_right, tearing_right,
        lids_right, conjunctiva_right, cornea_right, anterior_chamber_right,
        iris_right, pupil_right, lens_right, fundus_right,
        present_eyeball_left, squint_left, nystagmus_left, tearing_left,
        lids_left, conjunctiva_left, cornea_left, anterior_chamber_left,
        iris_left, pupil_left, lens_left, fundus_left,
        hvf, colour_vision, stereopsis,
        category, cause_of_vision, percentage_disability,
        possible_intervention, recommendation, conclusion_duration
    ) VALUES (
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?,
        ?, ?, ?, ?, ?, ?
    )";

    if ($stmt = mysqli_prepare($conn, $sql)) {

            // echo "Counted types: " . strlen("iissssssssssssssssssssssssssssssssssssssss") . "<br>";
            // echo "Counted vars: " . count([
            //     $assessment_id, $user_id, $assistive_device, $medical_history, $ocular_history,
            //             $right_eye_with_correction, $right_eye_without_correction,
            //             $left_eye_with_correction, $left_eye_without_correction,
            //             $near_vision_with_correction, $near_vision_without_correction,
            //             $present_eyeball_right, $squint_right, $nystagmus_right, $tearing_right,
            //             $lids_right, $conjunctiva_right, $cornea_right, $anterior_chamber_right,
            //             $iris_right, $pupil_right, $lens_right, $fundus_right,
            //             $present_eyeball_left, $squint_left, $nystagmus_left, $tearing_left,
            //             $lids_left, $conjunctiva_left, $cornea_left, $anterior_chamber_left,
            //             $iris_left, $pupil_left, $lens_left, $fundus_left,
            //             $hvf, $colour_vision, $stereopsis,
            //             $category, $cause_of_vision, $percentage_disability,
            //             $possible_intervention, $recommendation, $conclusion_duration
            // ]) . "<br>";
        $types = "ii" . str_repeat("s", 42);
        mysqli_stmt_bind_param(
            $stmt,
            // "iissssssssssssssssssssssssssssssssssssssss",
            $types,
            // $types = "ii" . str_repeat("s", 42),
            $assessment_id, $user_id, $assistive_device, $medical_history, $ocular_history,
            $right_eye_with_correction, $right_eye_without_correction,
            $left_eye_with_correction, $left_eye_without_correction,
            $near_vision_with_correction, $near_vision_without_correction,
            $present_eyeball_right, $squint_right, $nystagmus_right, $tearing_right,
            $lids_right, $conjunctiva_right, $cornea_right, $anterior_chamber_right,
            $iris_right, $pupil_right, $lens_right, $fundus_right,
            $present_eyeball_left, $squint_left, $nystagmus_left, $tearing_left,
            $lids_left, $conjunctiva_left, $cornea_left, $anterior_chamber_left,
            $iris_left, $pupil_left, $lens_left, $fundus_left,
            $hvf, $colour_vision, $stereopsis,
            $category, $cause_of_vision, $percentage_disability,
            $possible_intervention, $recommendation, $conclusion_duration
        );

        if (mysqli_stmt_execute($stmt)) {
            $medical_officer_id = $_SESSION['user_id'] ?? 1;
            $status             = "checked";
            $disability         = "Visual";

            $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Visual assessment saved.',
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
