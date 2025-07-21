<?php

function processChronicDisabilityAssessment($conn)
{
    $assessment_id = $_POST['assessment_id'] ?? null;
    $user_id       = $_POST['user_id'] ?? null;

    // Summary Fields
    $medical_history        = $_POST['medical_history'] ?? '';
    $onset_date             = $_POST['onset_date'] ?? '';
    $last_intervention_date = $_POST['last_intervention_date'] ?? '';
    $interventions          = $_POST['interventions'] ?? '';
    $cause_of_disability    = $_POST['cause_of_disability'] ?? '';

    // Structural Scores
    $scores = [
        'cardiovascular', 'respiratory', 'cancer', 'musculoskeletal',
        'neurological', 'gastrointestinal', 'dermatological',
        'hematologic', 'lymphatic', 'genitourinary', 'frailty', 'other',
    ];
    foreach ($scores as $area) {
        $$area = $_POST['score_' . $area] ?? '';
    }
    $findings_clinical = $_POST['findings_clinical'] ?? '';
    $remarks_clinical  = $_POST['remarks_clinical'] ?? '';

    // Functional Scores
    $functional_areas = ['mobility', 'selfcare', 'domestic', 'majorlife', 'community'];
    foreach ($functional_areas as $area) {
        $$area = $_POST['difficulty_' . $area] ?? '';
    }
    $findings_functional = $_POST['findings_functional'] ?? '';
    $remarks_functional  = $_POST['remarks_functional'] ?? '';

    // Ratings
    $rating_none         = $_POST['rating_none'] ?? 0;
    $rating_mild         = $_POST['rating_mild'] ?? 0;
    $rating_moderate     = $_POST['rating_moderate'] ?? 0;
    $rating_severe       = $_POST['rating_severe'] ?? 0;
    $rating_complete     = $_POST['rating_complete'] ?? 0;
    $conclusion_duration = $_POST['conclusion_duration'] ?? '';

    $status     = "checked";
    $disability = "Structural/Functional Disability";
    $file_path  = null;

    // Upload file
    if (! empty($_FILES['supporting_document']['name'])) {
        $ext = strtolower(pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $upload_dir = "../uploads/";
            if (! is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_path = $upload_dir . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['supporting_document']['tmp_name'], $file_path);

            $document_type = "chronic_supporting";
            $doc_sql       = "INSERT INTO documents (assessment_id, file_path, document_type) VALUES (?, ?, ?)";
            if ($doc_stmt = mysqli_prepare($conn, $doc_sql)) {
                mysqli_stmt_bind_param($doc_stmt, "iss", $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($doc_stmt);
                mysqli_stmt_close($doc_stmt);
            }
        }
    }

    // Insert into chronic_disability_assessments
    $sql = "INSERT INTO chronic_disability_assessments (
        assessment_id, user_id, medical_history, onset_date, last_intervention_date, interventions, cause_of_disability,
        score_cardiovascular, score_respiratory, score_cancer, score_musculoskeletal,
        score_neurological, score_gastrointestinal, score_dermatological,
        score_hematologic, score_lymphatic, score_genitourinary, score_frailty, score_other,
        findings_clinical, remarks_clinical,
        difficulty_mobility, difficulty_selfcare, difficulty_domestic, difficulty_majorlife, difficulty_community,
        findings_functional, remarks_functional,
        rating_none, rating_mild, rating_moderate, rating_severe, rating_complete,
        conclusion_duration, document_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "isssssssssssssssssssssssssssiissss",
            $assessment_id, $user_id, $medical_history, $onset_date, $last_intervention_date, $interventions, $cause_of_disability,
            $cardiovascular, $respiratory, $cancer, $musculoskeletal,
            $neurological, $gastrointestinal, $dermatological,
            $hematologic, $lymphatic, $genitourinary, $frailty, $other,
            $findings_clinical, $remarks_clinical,
            $mobility, $selfcare, $domestic, $majorlife, $community,
            $findings_functional, $remarks_functional,
            $rating_none, $rating_mild, $rating_moderate, $rating_severe, $rating_complete,
            $conclusion_duration, $file_path
        );

        if (mysqli_stmt_execute($stmt)) {
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
?>