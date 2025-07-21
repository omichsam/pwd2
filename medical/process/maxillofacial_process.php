<?php

function processMaxillofacialAssessment($conn)
{
    // session_start();

    $error_message = "";

    $assessment_id                  = $_POST['assessment_id'] ?? null;
    $medical_history                = $_POST['medical_history'] ?? '';
    $dental_history                 = $_POST['dental_history'] ?? '';
    $dental_assessment              = $_POST['dental_assessment'] ?? '';
    $conclusion                     = $_POST['conclusion'] ?? '';
    $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
    $other_services_required        = $_POST['other_services_required'] ?? '';
    $status                         = "checked";
    $disability                     = "Maxillofacial";

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

            // Insert into documents table
            $document_type = "maxillofacial_supporting";
            $doc_sql       = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // Insert into maxillofacial_assessment_details
    $sql = "INSERT INTO maxillofacial_assessment_details (
        assessment_id, medical_history, dental_history, dental_assessment, conclusion,
        recommended_assistive_products, other_services_required
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "issssss",
            $assessment_id,
            $medical_history,
            $dental_history,
            $dental_assessment,
            $conclusion,
            $recommended_assistive_products,
            $other_services_required
        );

        if (mysqli_stmt_execute($stmt)) {
            // Update the assessment record
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
