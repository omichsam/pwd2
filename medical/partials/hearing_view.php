<?php
    // include 'files/header.php';
    // include 'files/nav.php';
    // include 'files/sidebar.php';

    if (! isset($_GET['user_id'])) {
        echo "User ID is required.";
        exit;
    }

    $user_id = intval($_GET['user_id']);

    $sql = "SELECT
    u.name AS user_name, u.gender, u.dob, u.marital_status, u.id_number, u.occupation,
    u.mobile_number, u.email, u.type AS user_type, u.next_of_kin_name, u.next_of_kin_mobile,
    u.next_of_kin_relationship, uc.county_name AS user_county, u.subcounty AS user_subcounty, u.education_level,

    a.id AS assessment_id, a.disability_type, a.assessment_date, a.assessment_time, a.status,
    a.created_at AS assessment_created,

    d.name AS doctor_name, d.license_id AS doctor_license, d.email AS doctor_email,
    d.mobile_number AS doctor_mobile, d.type AS doctor_type,

    h.name AS hospital_name, hc.county_name AS hospital_county, h.subcounty AS hospital_subcounty, h.address AS hospital_address,

    hda.history_of_hearing_loss, hda.history_of_hearing_devices,
    hda.hearing_test_type_right, hda.hearing_test_type_left, hda.hearing_loss_degree_right,
    hda.hearing_loss_degree_left, hda.hearing_level_dbhl_right, hda.hearing_level_dbhl_left,
    hda.monaural_percentage_right, hda.monaural_percentage_left, hda.overall_binaural_percentage,
    hda.conclusion, hda.recommended_assistive_products, hda.required_services,

    doc.file_path, doc.document_type

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN hearing_disability_assessments hda ON hda.assessment_id = a.id
LEFT JOIN officials d ON a.medical_officer_id = d.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN documents doc ON doc.assessment_id = a.id
WHERE u.id = ? AND a.disability_type = 'Hearing'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data   = $result->fetch_assoc();

    if (! $data) {
        echo "Data not found.";
        exit;
    }
?>


 <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Patient:                                                                                                 <?php echo htmlspecialchars($data['user_name']); ?></h4>
                </div>
                 <div class="card-body">

                                            <div class="form-divider mt-3">
                                            <u>Personal Info</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Full Name</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['user_name']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Gender</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['gender']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Date of Birth</label>
                                                <input type="date" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['dob']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Marital Status</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['marital_status']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>ID Number</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['id_number']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Occupation</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['occupation']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Mobile Number</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['mobile_number']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Email</label>
                                                <input type="email" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['email']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>User Type</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['user_type']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Next of Kin Name</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['next_of_kin_name']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Next of Kin Mobile</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['next_of_kin_mobile']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Next of Kin Relationship</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['next_of_kin_relationship']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>County</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['user_county']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Subcounty</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['user_subcounty']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Education Level</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['education_level']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Assessment Information</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Assessment Date</label>
                                                <input type="date" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['assessment_date']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Disability Type</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['disability_type']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Status</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['status']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Doctor Information</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Name</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['doctor_name']); ?>"
                                                    readonly>
                                            </div>
                                            <!-- <div class="form-group col-md-4">
                                                <label>Doctor's License ID</label>
                                                <input type="text" class="form-control"
                                                    value="< ?php echo htmlspecialchars($data['doctor_license']); ?>"
                                                    readonly>
                                            </div> -->

                                            <div class="form-group col-md-4">
                                                <label>Doctor's Email</label>
                                                <input type="email" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['doctor_email']); ?>"
                                                    readonly>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Doctor's Mobile</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['doctor_mobile']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row" hidden>
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Mobile</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['doctor_mobile']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Type</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['doctor_type']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Hospital Information</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Hospital Name</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hospital_name']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Hospital County</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hospital_county']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Hospital Subcounty</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hospital_subcounty']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-group" hidden>
                                            <label>Hospital Address</label>
                                            <textarea class="form-control" rows="3"
                                                readonly><?php echo htmlspecialchars($data['hospital_address']); ?></textarea>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Hearing Assessment</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>History of Hearing Loss</label>
                                                <textarea class="form-control" rows="3"
                                                    readonly><?php echo htmlspecialchars($data['history_of_hearing_loss']); ?></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>History of Hearing Devices</label>
                                                <textarea class="form-control" rows="3"
                                                    readonly><?php echo htmlspecialchars($data['history_of_hearing_devices']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Hearing Test Type (Right)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hearing_test_type_right']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Hearing Test Type (Left)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hearing_test_type_left']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Hearing Loss Degree (Right)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hearing_loss_degree_right']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Hearing Loss Degree (Left)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hearing_loss_degree_left']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Hearing Level (Right)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hearing_level_dbhl_right']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Hearing Level (Left)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['hearing_level_dbhl_left']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Monaural Percentage (Right)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['monaural_percentage_right']); ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Monaural Percentage (Left)</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['monaural_percentage_left']); ?>"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Overall Binaural Percentage</label>
                                            <input type="text" class="form-control"
                                                value="<?php echo htmlspecialchars($data['overall_binaural_percentage']); ?>"
                                                readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Conclusion</label>
                                            <textarea class="form-control" rows="3"
                                                readonly><?php echo htmlspecialchars($data['conclusion']); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Recommended Assistive Products</label>
                                            <textarea class="form-control" rows="3"
                                                readonly><?php echo htmlspecialchars($data['recommended_assistive_products']); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Required Services</label>
                                            <textarea class="form-control" rows="3"
                                                readonly><?php echo htmlspecialchars($data['required_services']); ?></textarea>
                                        </div>

                               <div class="form-divider mt-4">
                                            <u><strong>Uploaded Document</strong></u>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label>Document</label>
                                                <?php if (! empty($data['file_path'])): ?>
<?php
    $document_url       = "" . htmlspecialchars($data['file_path']);
    $document_extension = pathinfo($data['file_path'], PATHINFO_EXTENSION);
    $file_name          = basename($data['file_path']);
?>

                                                    <div class="mb-2">
                                                        <span class="font-weight-bold">File:</span>
                                                        <?php echo htmlspecialchars($file_name); ?>
                                                        <span
                                                            class="badge badge-secondary ml-2"><?php echo strtoupper($document_extension); ?></span>
                                                    </div>

                                                    <div class="btn-group">
                                                        <?php if ($document_extension == 'pdf'): ?>
                                                            <a href="<?php echo $document_url; ?>" target="_blank"
                                                                class="btn btn-primary">
                                                                <i class="fas fa-file-pdf"></i> View PDF
                                                            </a>
                                                        <?php elseif (in_array($document_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                            <a href="<?php echo $document_url; ?>" target="_blank"
                                                                class="btn btn-success">
                                                                <i class="fas fa-image"></i> View Image
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo $document_url; ?>" target="_blank"
                                                                class="btn btn-primary">
                                                                <i class="fas fa-file"></i> View Document
                                                            </a>
                                                        <?php endif; ?>

                                                        <a href="<?php echo $document_url; ?>" download
                                                            class="btn btn-warning">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>

                                                <?php else: ?>
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle"></i> No document uploaded
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                </div>
            </div>
        </div>

