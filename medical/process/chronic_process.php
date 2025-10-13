<?php

function processChronicAssessment($conn)
{
    // session_start();

    $error_message = "";

    // Use $_REQUEST which contains both POST and GET data
    $request_data = $_POST; // Default to POST
    if (empty($_POST) && !empty($_GET)) {
        $request_data = $_GET;
    }

    // Fetch ALL data from request with proper fallbacks
    $assessment_id = intval($request_data['assessment_id'] ?? 0);
    $user_id = intval($_SESSION['user_id'] ?? ($request_data['user_id'] ?? 1));

    if (empty($assessment_id)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'No assessment data received.',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
        return false;
    }

    // Extract all fields with empty string fallbacks
    $onset_date = $request_data['onset_date'] ?? '';
    $last_intervention_date = $request_data['last_intervention_date'] ?? '';
    $interventions = $request_data['interventions'] ?? '';
    $cause_of_disability = $request_data['cause_of_disability'] ?? '';
    $structural_impairments = $request_data['structural_impairments'] ?? '';
    $regions_affected = $request_data['regions_affected'] ?? '';

    // Clinical data - SCORES and REMARKS (24 fields)
    $cardiovascular_score = $request_data['score_cardiovascular'] ?? '';
    $cardiovascular_remark = $request_data['remark_cardiovascular'] ?? '';
    $respiratory_score = $request_data['score_respiratory'] ?? '';
    $respiratory_remark = $request_data['remark_respiratory'] ?? '';
    $cancer_score = $request_data['score_cancer'] ?? '';
    $cancer_remark = $request_data['remark_cancer'] ?? '';
    $musculoskeletal_score = $request_data['score_musculoskeletal'] ?? '';
    $musculoskeletal_remark = $request_data['remark_musculoskeletal'] ?? '';
    $neurological_score = $request_data['score_neurological'] ?? '';
    $neurological_remark = $request_data['remark_neurological'] ?? '';
    $gastrointestinal_score = $request_data['score_gastrointestinal'] ?? '';
    $gastrointestinal_remark = $request_data['remark_gastrointestinal'] ?? '';
    $dermatological_score = $request_data['score_dermatological'] ?? '';
    $dermatological_remark = $request_data['remark_dermatological'] ?? '';
    $hematologic_score = $request_data['score_hematologic'] ?? '';
    $hematologic_remark = $request_data['remark_hematologic'] ?? '';
    $lymphatic_score = $request_data['score_lymphatic'] ?? '';
    $lymphatic_remark = $request_data['remark_lymphatic'] ?? '';
    $genitourinary_score = $request_data['score_genitourinary'] ?? '';
    $genitourinary_remark = $request_data['remark_genitourinary'] ?? '';
    $frailty_score = $request_data['score_frailty'] ?? '';
    $frailty_remark = $request_data['remark_frailty'] ?? '';
    $other_score = $request_data['score_other'] ?? '';
    $other_remark = $request_data['remark_other'] ?? '';

    // Functional data - DIFFICULTIES and REMARKS (10 fields)
    $mobility_difficulty = $request_data['difficulty_mobility'] ?? '';
    $mobility_remark = $request_data['remark_mobility'] ?? '';
    $selfcare_difficulty = $request_data['difficulty_selfcare'] ?? '';
    $selfcare_remark = $request_data['remark_selfcare'] ?? '';
    $domestic_difficulty = $request_data['difficulty_domestic'] ?? '';
    $domestic_remark = $request_data['remark_domestic'] ?? '';
    $majorlife_difficulty = $request_data['difficulty_majorlife'] ?? '';
    $majorlife_remark = $request_data['remark_majorlife'] ?? '';
    $community_difficulty = $request_data['difficulty_community'] ?? '';
    $community_remark = $request_data['remark_community'] ?? '';

    // Disability rating (4 fields)
    $disability_rating = $request_data['disability_rating'] ?? '';
    $recommended_assistive_products = $request_data['recommended_assistive_products'] ?? '';
    $other_services_required = $request_data['other_services_required'] ?? '';
    $conclusion_decision = $request_data['conclusion_decision'] ?? '';

    // File upload (1 field)
    $file_path = null;
    if (!empty($_FILES['supporting_document']['name'])) {
        $ext = strtolower(pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0755, true);
            $file_path = $upload_dir . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['supporting_document']['tmp_name'], $file_path);
        }
    }

    // SQL with exactly 47 placeholders
    $sql = "INSERT INTO chronic_disorder_assessments (
        assessment_id, user_id, onset_date, last_intervention_date, interventions, cause_of_disability, 
        structural_impairments, regions_affected, 
        cardiovascular_score, cardiovascular_remark, 
        respiratory_score, respiratory_remark, 
        cancer_score, cancer_remark, 
        musculoskeletal_score, musculoskeletal_remark, 
        neurological_score, neurological_remark, 
        gastrointestinal_score, gastrointestinal_remark, 
        dermatological_score, dermatological_remark, 
        hematologic_score, hematologic_remark, 
        lymphatic_score, lymphatic_remark, 
        genitourinary_score, genitourinary_remark, 
        frailty_score, frailty_remark, 
        other_score, other_remark, 
        mobility_difficulty, mobility_remark, 
        selfcare_difficulty, selfcare_remark, 
        domestic_difficulty, domestic_remark, 
        majorlife_difficulty, majorlife_remark, 
        community_difficulty, community_remark, 
        disability_rating, recommended_assistive_products, 
        other_services_required, conclusion_decision, supporting_document
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Debug counts
    $placeholders_count = substr_count($sql, '?');
    echo "SQL placeholders count: " . $placeholders_count . "<br>";

    // Type string: 2 integers + 45 strings = 47 characters
    $types = "ii" . str_repeat("s", 45);

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind all 47 parameters explicitly
        $bound = mysqli_stmt_bind_param(
            $stmt,
            $types,
            // 2 integers
            $assessment_id,                    // 1
            $user_id,                         // 2

            // 6 basic strings
            $onset_date,                      // 3
            $last_intervention_date,          // 4
            $interventions,                   // 5
            $cause_of_disability,             // 6
            $structural_impairments,          // 7
            $regions_affected,                // 8

            // 24 clinical fields (12 systems × 2 fields each)
            $cardiovascular_score,            // 9
            $cardiovascular_remark,           // 10
            $respiratory_score,               // 11
            $respiratory_remark,              // 12
            $cancer_score,                    // 13
            $cancer_remark,                   // 14
            $musculoskeletal_score,           // 15
            $musculoskeletal_remark,          // 16
            $neurological_score,              // 17
            $neurological_remark,             // 18
            $gastrointestinal_score,          // 19
            $gastrointestinal_remark,         // 20
            $dermatological_score,            // 21
            $dermatological_remark,           // 22
            $hematologic_score,               // 23
            $hematologic_remark,              // 24
            $lymphatic_score,                 // 25
            $lymphatic_remark,                // 26
            $genitourinary_score,             // 27
            $genitourinary_remark,            // 28
            $frailty_score,                   // 29
            $frailty_remark,                  // 30
            $other_score,                     // 31
            $other_remark,                    // 32

            // 10 functional fields (5 areas × 2 fields each)
            $mobility_difficulty,             // 33
            $mobility_remark,                 // 34
            $selfcare_difficulty,             // 35
            $selfcare_remark,                 // 36
            $domestic_difficulty,             // 37
            $domestic_remark,                 // 38
            $majorlife_difficulty,            // 39
            $majorlife_remark,                // 40
            $community_difficulty,            // 41
            $community_remark,                // 42

            // 4 disability rating fields
            $disability_rating,               // 43
            $recommended_assistive_products,  // 44
            $other_services_required,         // 45
            $conclusion_decision,             // 46

            // 1 file path
            $file_path                        // 47
        );

        if (!$bound) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error!',
                        text: 'Failed to bind parameters: " . addslashes(mysqli_stmt_error($stmt)) . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
            mysqli_stmt_close($stmt);
            return false;
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Update the assessment record after successful insertion
            $medical_officer_id = $_SESSION['user_id'] ?? 1;
            $disability_type = 'Chronic';
            $status = "checked";

            $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability_type, $medical_officer_id, $status, $assessment_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Chronic assessment saved successfully.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '../medical/complete_assessment';
                            });
                        });
                    </script>";
                    mysqli_stmt_close($update_stmt);
                    return true;
                } else {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Partial Success',
                                text: 'Assessment saved but status update failed: " . addslashes(mysqli_stmt_error($update_stmt)) . "',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '../medical/complete_assessment';
                            });
                        });
                    </script>";
                    mysqli_stmt_close($update_stmt);
                    return true; // Still return true since the main data was saved
                }
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Partial Success',
                            text: 'Assessment saved but status update preparation failed: " . addslashes(mysqli_error($conn)) . "',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '../medical/complete_assessment';
                        });
                    });
                </script>";
                return true; // Still return true since the main data was saved
            }
        } else {
            $error_msg = mysqli_stmt_error($stmt);
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed!',
                        text: 'Failed to save assessment: " . addslashes($error_msg) . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
            mysqli_stmt_close($stmt);
            return false;
        }
    } else {
        $error_msg = mysqli_error($conn);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Database Error!',
                    text: 'Failed to prepare statement: " . addslashes($error_msg) . "',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
        return false;
    }
}

?>