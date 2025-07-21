<?php
function processHearingAssessment($conn)
{
    // session_start();

    $error_message = "";

    $assessment_id                  = $_POST['assessment_id'] ?? null;
    $history_of_hearing_loss        = $_POST['history_of_hearing_loss'] ?? '';
    $history_of_hearing_devices     = $_POST['history_of_hearing_devices'] ?? '';
    $hearing_loss_type_right        = $_POST['hearing_loss_type_right'] ?? '';
    $hearing_loss_type_left         = $_POST['hearing_loss_type_left'] ?? '';
    $hearing_grade_right            = $_POST['hearing_grade_right'] ?? '';
    $hearing_grade_left             = $_POST['hearing_grade_left'] ?? '';
    $hearing_level_dbhl_right       = is_numeric($_POST['hearing_level_dbhl_right']) ? (float) $_POST['hearing_level_dbhl_right'] : null;
    $hearing_level_dbhl_left        = is_numeric($_POST['hearing_level_dbhl_left']) ? (float) $_POST['hearing_level_dbhl_left'] : null;
    $monoaural_percent_right        = is_numeric($_POST['monoaural_percent_right']) ? (float) $_POST['monoaural_percent_right'] : null;
    $monoaural_percent_left         = is_numeric($_POST['monoaural_percent_left']) ? (float) $_POST['monoaural_percent_left'] : null;
    $binaural_percent               = is_numeric($_POST['binaural_percent']) ? (float) $_POST['binaural_percent'] : null;
    $hearing_disability_conclusion  = $_POST['hearing_disability_conclusion'] ?? '';
    $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
    $required_services              = $_POST['required_services'] ?? '';
    $status                         = "checked";

    // File upload
    $file_uploaded = false; 
    $file_path = null;
    if (!empty($_FILES['supporting_file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['supporting_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $file_path = $upload_dir . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['supporting_file']['tmp_name'], $file_path);

            // ✅ Insert into documents table
            $doc_sql = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // Insert
    $sql = "INSERT INTO hearing_disability_assessments (
        assessment_id, history_of_hearing_loss, history_of_hearing_devices,
        hearing_loss_degree_right, hearing_loss_degree_left,
        hearing_test_type_left, hearing_test_type_right,
        hearing_level_dbhl_right, hearing_level_dbhl_left,
        monaural_percentage_right, monaural_percentage_left,
        overall_binaural_percentage, conclusion, recommended_assistive_products,
        required_services
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "isssssssddddsss",
            $assessment_id,
            $history_of_hearing_loss,
            $history_of_hearing_devices,
            $hearing_loss_type_right,
            $hearing_loss_type_left,
            $hearing_grade_right,
            $hearing_grade_left,
            $hearing_level_dbhl_right,
            $hearing_level_dbhl_left,
            $monoaural_percent_right,
            $monoaural_percent_left,
            $binaural_percent,
            $hearing_disability_conclusion,
            $recommended_assistive_products,
            $required_services 
        );

        if (mysqli_stmt_execute($stmt)) {
            // Update the assessment
            $disability         = 'Hearing';
            $medical_officer_id = $_SESSION['user_id'] ?? 1;

            $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);
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
