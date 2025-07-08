<?php
$success = null;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["hearing"])) {
    $assessment_id = $_GET['assessment_id'] ?? null;
    $history_of_hearing_loss = $_POST['history_of_hearing_loss'] ?? '';
    $history_of_hearing_devices = $_POST['history_of_hearing_devices'] ?? '';
    $hearing_loss_type_right = $_POST['hearing_loss_type_right'] ?? '';
    $hearing_loss_type_left = $_POST['hearing_loss_type_left'] ?? '';
    $hearing_grade_right = $_POST['hearing_grade_right'] ?? '';
    $hearing_grade_left = $_POST['hearing_grade_left'] ?? '';
    $hearing_level_dbhl_right = $_POST['hearing_level_dbhl_right'] ?? null;
    $hearing_level_dbhl_left = $_POST['hearing_level_dbhl_left'] ?? null;
    $monoaural_percent_right = $_POST['monoaural_percent_right'] ?? null;
    $monoaural_percent_left = $_POST['monoaural_percent_left'] ?? null;
    $binaural_percent = $_POST['binaural_percent'] ?? null;
    $conclusion = $_POST['hearing_disability_conclusion'] ?? '';
    $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
    $required_services = $_POST['required_services'] ?? '';
    $status = "checked";

    // File upload handling
    $file_uploaded = false;
    $file_path = '';
    if (isset($_FILES['supporting_file']) && $_FILES['supporting_file']['error'] === 0) {
        $file_name = $_FILES['supporting_file']['name'];
        $file_tmp = $_FILES['supporting_file']['tmp_name'];
        $file_size = $_FILES['supporting_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
        if (in_array($file_ext, $allowed_exts)) {
            $upload_dir = "uploads/";
            $file_path = $upload_dir . uniqid() . '.' . $file_ext;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $file_uploaded = true;
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only PDF, JPG, JPEG, PNG files are allowed.";
        }
    }

    // Insert hearing disability assessment details into the database
    $sql = "INSERT INTO hearing_disability_assessments (
        assessment_id,
        history_of_hearing_loss,
        history_of_hearing_devices,
        hearing_test_type_right,
        hearing_test_type_left,
        hearing_loss_degree_right,
        hearing_loss_degree_left,
        hearing_level_dbhl_right,
        hearing_level_dbhl_left,
        monaural_percentage_right,
        monaural_percentage_left,
        overall_binaural_percentage,
        conclusion,
        recommended_assistive_products,
        required_services
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
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
            $conclusion,
            $recommended_assistive_products,
            $required_services
        );

        if (mysqli_stmt_execute($stmt)) {
            $disability = 'Hearing';
            $medical_officer_id = $_SESSION['user_id'] ?? 1;

            $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);

            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    // Insert the uploaded document into the documents table
                    if ($file_uploaded) {
                        $insert_document_sql = "INSERT INTO documents (user_id, assessment_id, file_path, document_type) 
                                                VALUES (?, ?, ?, ?)";
                        $insert_document_stmt = mysqli_prepare($conn, $insert_document_sql);
                        if ($insert_document_stmt) {
                            $document_type = 'Hearing Assessment File';
                            mysqli_stmt_bind_param($insert_document_stmt, "iiss", $pwdUser['id'], $assessment_id, $file_path, $document_type);
                            mysqli_stmt_execute($insert_document_stmt);
                        }
                    }

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Assessment and hearing details saved.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = 'complete_assessment';
                                });
                            });
                        </script>";
                } else {
                    $error_message = mysqli_stmt_error($update_stmt);
                }
                mysqli_stmt_close($update_stmt);
            } else {
                $error_message = mysqli_error($conn);
            }
        } else {
            $error_message = mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        $error_message = mysqli_error($conn);
    }

    mysqli_close($conn);

    // Show error if any
    if (!empty($error_message)) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '" . addslashes($error_message) . "',
                confirmButtonText: 'OK'
            });
        });
    </script>";
    }
}
?>

<div class="container mt-5 divider">
    <h4 class="mb-4">Hearing Impairment Assessment</h4>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="assessmentAccordion">
            <!-- 1. Medical History -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" id="headingOne">
                    <h4>1. Medical History</h4>
                </div>

                <div id="collapseOne" class=" accordion-body collapse show">
                    <div class="accordion-body">
                        <div class="row mb-3">
                            <div class="col-md-6" hidden>
                                <label>Hearing Impairments</label>
                                <input type="text" name="hearing_Impairments" value="hearing_Impairments"
                                    class="form-control">
                            </div>
                            <div class="col-md-12" hidden>
                                <label>Assessment ID</label>
                                <input type="assessment_id" name="assessment_id" value=<?= $_GET['assessment_id'] ?>
                                    class="form-control">
                            </div>

                            <div class="col-md-12" hidden>
                                <label>User id</label>
                                <input type="user_id" name="user_id" value=<?= $pwdUser['id'] ?> class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>History of Hearing Loss</label>
                            <textarea name="history_of_hearing_loss" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>History of Hearing Devices Usage</label>
                            <textarea name="history_of_hearing_devices" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Hearing Test Results -->
            <div class="accordion-item">
                <div class="accordion-header accordion-button collapsed" role="button" data-toggle="collapse"
                    data-target="#collapseTwo" id="headingTwo" data-bs-target="#collapseTwo">
                    <h4> 2. Hearing Test Results</h4>
                </div>

                <div id="collapseTwo" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Left Ear</h5>
                                <div class="mb-3">
                                    <label>Hearing Loss Type</label>
                                    <input type="text" name="hearing_loss_type_left" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Grade</label>
                                    <input type="text" name="hearing_grade_left" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Level (dBHL)</label>
                                    <input type="number" step="0.01" name="hearing_level_dbhl_left"
                                        class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Monoaural %</label>
                                    <input type="number" step="0.01" name="monoaural_percent_left" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Right Ear</h5>
                                <div class="mb-3">
                                    <label>Hearing Loss Type</label>
                                    <input type="text" name="hearing_loss_type_right" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Grade</label>
                                    <input type="text" name="hearing_grade_right" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Level (dBHL)</label>
                                    <input type="number" step="0.01" name="hearing_level_dbhl_right"
                                        class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Monoaural %</label>
                                    <input type="number" step="0.01" name="monoaural_percent_right"
                                        class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Binaural %</label>
                            <input type="number" step="0.01" name="binaural_percent" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Hearing Disability Conclusion</label>
                            <select name="hearing_disability_conclusion" class="form-control">
                                <option value="">Select</option>
                                <option value="Temporary">Temporary</option>
                                <option value="Permanent">Permanent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <!-- 3. Recommendations -->
            <div class="accordion-item">
                <div class="accordion-header accordion-button collapsed" role="button" data-toggle="collapse"
                    data-target="#collapseThree" id="headingThree" data-bs-target="#collapseThree">
                    <h4> 3. Recommendations & Services</h4>
                </div>

                <div id="collapseThree" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Recommended Assistive Product(s)</label>
                            <textarea name="recommended_assistive_products" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Required Services</label>
                            <textarea name="required_services" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Upload Supporting Documents (PDF, JPG, PNG)</label>
                            <input type="file" name="supporting_file" class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary" name="hearing">Submit Assessment</button>
            </div>
        </div>
    </form>
</div>