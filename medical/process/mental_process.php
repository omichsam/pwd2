<?php

function processMentalAssessment($conn)
{
    // session_start();

    $assessment_id                  = $_POST['assessment_id'] ?? null;
    $clinical_history               = $_POST['clinical_history'] ?? '';
    $mental_status_evaluation       = $_POST['mental_status_evaluation'] ?? '';
    $feeding_score                  = $_POST['feeding_score'] ?? 0;
    $toileting_score                = $_POST['toileting_score'] ?? 0;
    $grooming_score                 = $_POST['grooming_score'] ?? 0;
    $physical_disability_score      = $_POST['physical_disability_score'] ?? 0;
    $employability_score            = $_POST['employability_score'] ?? 0;
    $duration_of_illness            = $_POST['duration_of_illness'] ?? '';
    $major_cause_disability         = $_POST['major_cause_disability'] ?? '';
    $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
    $other_services_required        = $_POST['other_services_required'] ?? '';
    $status                         = "checked";
    $disability                     = "Mental/Intellectual/Autism Spectrum";

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

            // Insert into documents table if you want
            $document_type = "mental_supporting";
            $doc_sql       = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // Insert into assessment table
    $sql = "INSERT INTO mental_assessment_details (
        assessment_id, clinical_history, mental_status_evaluation,
        feeding_score, toileting_score, grooming_score,
        physical_disability_score, employability_score,
        duration_of_illness, major_cause_disability,
        recommended_assistive_products, other_services_required,
        document_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "issiiiissssss",
            $assessment_id,
            $clinical_history,
            $mental_status_evaluation,
            $feeding_score,
            $toileting_score,
            $grooming_score,
            $physical_disability_score,
            $employability_score,
            $duration_of_illness,
            $major_cause_disability,
            $recommended_assistive_products,
            $other_services_required,
            $file_path
        );

        if (mysqli_stmt_execute($stmt)) {
            // Update assessments table
            $medical_officer_id = $_SESSION['user_id'] ?? 1;
            $update_sql         = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
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
