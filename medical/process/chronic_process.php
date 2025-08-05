<?php

function processProgressiveAssessment($conn)
{
    // session_start(); // uncomment if session needed

    $assessment_id = $_POST['assessment_id'] ?? null;
    $user_id       = $_POST['user_id'] ?? null;

    // Summary Findings
    $onset_date             = $_POST['onset_date'] ?? null;
    $last_intervention_date = $_POST['last_intervention_date'] ?? null;
    $interventions          = $_POST['interventions'] ?? '';
    $cause_of_disability    = $_POST['cause_of_disability'] ?? '';

    // Structural / Clinical Assessment
    $structural_impairments = $_POST['structural_impairments'] ?? '';
    $regions_affected       = $_POST['regions_affected'] ?? '';

    $score_fields = [
        'cardiovascular', 'respiratory', 'cancer', 'musculoskeletal', 'neurological',
        'gastrointestinal', 'dermatological', 'hematologic', 'lymphatic',
        'genitourinary', 'frailty', 'other',
    ];

    foreach ($score_fields as $field) {
        ${"score_" . $field} = $_POST["score_" . $field] ?? '';
    }

    $findings_clinical = $_POST['findings_clinical'] ?? '';
    $remarks_clinical  = $_POST['remarks_clinical'] ?? '';

    // Functional Assessment
    $difficulty_fields = ['mobility', 'selfcare', 'domestic', 'majorlife', 'community'];
    foreach ($difficulty_fields as $field) {
        ${"difficulty_" . $field} = $_POST["difficulty_" . $field] ?? '';
    }

    $findings_functional = $_POST['findings_functional'] ?? '';
    $remarks_functional  = $_POST['remarks_functional'] ?? '';

    $rating_none     = $_POST['rating_none'] ?? 0;
    $rating_mild     = $_POST['rating_mild'] ?? 0;
    $rating_moderate = $_POST['rating_moderate'] ?? 0;
    $rating_severe   = $_POST['rating_severe'] ?? 0;
    $rating_complete = $_POST['rating_complete'] ?? 0;

    $conclusion_duration            = $_POST['conclusion_duration'] ?? '';
    $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
    $other_services_required        = $_POST['other_services_required'] ?? '';

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

            $document_type = "progressive_supporting";
            $doc_sql       = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // INSERT QUERY
        $sql = "INSERT INTO progressive_assessment_details (
        assessment_id, user_id,
        onset_date, last_intervention_date, interventions, cause_of_disability,
        structural_impairments, regions_affected,
        score_cardiovascular, score_respiratory, score_cancer, score_musculoskeletal,
        score_neurological, score_gastrointestinal, score_dermatological,
        score_hematologic, score_lymphatic, score_genitourinary, score_frailty, score_other,
        findings_clinical, remarks_clinical,
        difficulty_mobility, difficulty_selfcare, difficulty_domestic, difficulty_majorlife, difficulty_community,
        findings_functional, remarks_functional,
        rating_none, rating_mild, rating_moderate, rating_severe, rating_complete,
        conclusion_duration, recommended_assistive_products, other_services_required,
        supporting_document
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?
    )";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "iissssssssssssssssssssssssssiissssssss",
            $assessment_id, $user_id,
            $onset_date, $last_intervention_date, $interventions, $cause_of_disability,
            $structural_impairments, $regions_affected,
            $score_cardiovascular, $score_respiratory, $score_cancer, $score_musculoskeletal,
            $score_neurological, $score_gastrointestinal, $score_dermatological,
            $score_hematologic, $score_lymphatic, $score_genitourinary, $score_frailty, $score_other,
            $findings_clinical, $remarks_clinical,
            $difficulty_mobility, $difficulty_selfcare, $difficulty_domestic,
            $difficulty_majorlife, $difficulty_community,
            $findings_functional, $remarks_functional,
            $rating_none, $rating_mild, $rating_moderate, $rating_severe, $rating_complete,
            $conclusion_duration, $recommended_assistive_products, $other_services_required, $file_path
        );

        if (mysqli_stmt_execute($stmt)) {
            $disability         = "Progressive_Chronic";
            $status             = "checked";
            $medical_officer_id = $_SESSION['user_id'] ?? 1;

            $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: 'Progressive assessment saved successfully.',
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
        echo "Prepare error: " . mysqli_error($conn);
    }
}
