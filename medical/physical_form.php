<?php
$success = null;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $assessment_id = $_GET['assessment_id'] ?? null; // Get assessment ID from URL
    $medical_history = $_POST['medical_history'] ?? '';
    $injury_date = $_POST['injury_date'] ?? null;
    $last_intervention_date = $_POST['last_intervention_date'] ?? null;
    $cause_of_disability = $_POST['cause_of_disability'] ?? '';
    $medical_officer_id = $_POST['medical_officer_id'] ?? '';

    $muscle_power_score = $_POST['muscle_power_score'] ?? '';
    $joint_range_score = $_POST['joint_range_score'] ?? '';
    $angulation_score = $_POST['angulation_score'] ?? '';
    $amputation_score = $_POST['amputation_score'] ?? '';
    $impairments_remark = $_POST['impairments_remark'] ?? '';

    $mobility_score = $_POST['mobility_score'] ?? '';
    $self_care_score = $_POST['self_care_score'] ?? '';
    $restrictions_remark = $_POST['restrictions_remark'] ?? '';

    $disability_rating = $_POST['disability_rating'] ?? '';
    $conclusion_type = $_POST['conclusion_type'] ?? '';
    $assistive_products = $_POST['assistive_products'] ?? '';
    $required_services = $_POST['required_services'] ?? '';

    // Variables
    $medical_officer_id = $_SESSION['user_id'] ?? 1;
    $disability = "Physical"; // Set this directly if needed

    $medical_officer_id = (int) $medical_officer_id;
    $assessment_id = (int) $assessment_id;

    // File upload handling
    $file_uploaded = false;
    $file_path = '';

    // Set the upload directory
    $upload_dir = "uploads/";
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
    $sql = "INSERT INTO physical_disability_assessments (
            assessment_id,
            medical_history,
            injury_date,
            last_intervention_date,
            cause_of_disability,
            muscle_power_score,
            joint_range_score,
            angulation_score,
            amputation_score,
            impairments_remark,
            mobility_score,
            self_care_score,
            restrictions_remark,
            disability_rating,
            conclusion_type,
            assistive_products,
            required_services
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssssssss", // The data types: "s" for string, "i" for integer, etc.
            $assessment_id,
            $medical_history,
            $injury_date,
            $last_intervention_date,
            $cause_of_disability,
            $muscle_power_score,
            $joint_range_score,
            $angulation_score,
            $amputation_score,
            $impairments_remark,
            $mobility_score,
            $self_care_score,
            $restrictions_remark,
            $disability_rating,
            $conclusion_type,
            $assistive_products,
            $required_services
        );


        if (mysqli_stmt_execute($stmt)) {
            $disability = 'Physical';
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
                                    text: 'Assessment details details saved.',
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
    <h4 class="mb-4 text-center">Physical Disability Assessment</h4>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="assessmentAccordion">

            <!-- 1. Medical History -->




            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" id="headingOne">
                    <h4>1. Medical History</h4>
                </div>

                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="medical_history">Medical History</label>
                                <textarea name="medical_history" id="medical_history" class="form-control" rows="3"
                                    placeholder="Describe medical history..."></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="injury_date">Date of Injury</label>
                                <input type="date" name="injury_date" id="injury_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_intervention_date">Last Intervention Date</label>
                                <input type="date" name="last_intervention_date" id="last_intervention_date"
                                    class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cause_of_disability">Cause of Disability</label>
                                <input type="text" name="cause_of_disability" id="cause_of_disability"
                                    class="form-control" placeholder="E.g., accident, illness...">
                            </div>

                            <div class="col-md-6 mb-3" hidden>
                                <input type="text" name="medical_officer_id" value="<?= $pwdUser['id'] ?>"
                                    class="form-control">
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Impairment Scores -->
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" id="headingTwo">
                    <h4>2. Impairments Assessments</h4>
                </div>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="muscle_power_score">Muscle Power</label>
                                <select name="muscle_power_score" id="muscle_power_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joint_range_score">Joint Range</label>
                                <select name="joint_range_score" id="joint_range_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="angulation_score">Angulation</label>
                                <select name="angulation_score" id="angulation_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="amputation_score">Amputation</label>
                                <select name="amputation_score" id="amputation_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="impairments_remark">Overall Remarks / Findings</label>
                                <textarea name="impairments_remark" id="impairments_remark" class="form-control"
                                    rows="3" placeholder="Any relevant findings or remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Restrictions -->
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" id="headingThree">
                    <h4> 3. Activity & Participation Restrictions</h4>
                </div>



                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mobility_score">Mobility</label>
                                <select name="mobility_score" id="mobility_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Difficulty</option>
                                    <option>Mild Difficulty</option>
                                    <option>Moderate Difficulty</option>
                                    <option>Severe Difficulty</option>
                                    <option>Complete Difficulty</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="self_care_score">Self Care</label>
                                <select name="self_care_score" id="self_care_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Difficulty</option>
                                    <option>Mild Difficulty</option>
                                    <option>Moderate Difficulty</option>
                                    <option>Severe Difficulty</option>
                                    <option>Complete Difficulty</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="restrictions_remark">Overall Remarks / Findings</label>
                                <textarea name="restrictions_remark" id="restrictions_remark" class="form-control"
                                    rows="3" placeholder="Any relevant restrictions or remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Final Summary -->
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseFour"
                    aria-expanded="true" id="headingFour">
                    <h4> 4. Summary & Recommendation</h4>
                </div>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="disability_rating">Overall Disability Rating</label>
                                <select name="disability_rating" id="disability_rating" class="form-control">
                                    <option value="">Select</option>
                                    <option>No</option>
                                    <option>Mild</option>
                                    <option>Moderate</option>
                                    <option>Severe</option>
                                    <option>Complete</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="conclusion_type">Conclusion Type</label>
                                <select name="conclusion_type" id="conclusion_type" class="form-control">
                                    <option value="">Select</option>
                                    <option>Temporary</option>
                                    <option>Permanent</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="assistive_products">Assistive Products</label>
                                <textarea name="assistive_products" id="assistive_products" class="form-control"
                                    rows="3" placeholder="Specify any assistive products..."></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="required_services">Required Services</label>
                                <textarea name="required_services" id="required_services" class="form-control" rows="3"
                                    placeholder="Specify any required services..."></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="supporting_file">Upload Supporting Documents (PDF, JPG, PNG)</label>
                                <input type="file" name="supporting_file" id="supporting_file" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary" name="submit_assessment">Submit Assessment</button>
            </div>

        </div>
    </form>
</div>