<?php
    // You can include the following if needed in full page:
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
    u.next_of_kin_relationship, uc.county_name AS user_county, u.subcounty AS user_subcounty,
    u.education_level,

    a.id AS assessment_id, a.disability_type, a.assessment_date, a.assessment_time, a.status,
    a.created_at AS assessment_created,

    d.name AS doctor_name, d.license_id AS doctor_license, d.email AS doctor_email,
    d.mobile_number AS doctor_mobile, d.type AS doctor_type,

    h.name AS hospital_name, hc.county_name AS hospital_county, h.subcounty AS hospital_subcounty,
    h.address AS hospital_address,

    pad.id AS pad_id, pad.assessment_id AS pad_assessment_id, pad.user_id AS pad_user_id,
    pad.onset_date, pad.last_intervention_date, pad.interventions, pad.cause_of_disability,
    pad.structural_impairments, pad.regions_affected,

    pad.score_cardiovascular, pad.score_respiratory, pad.score_cancer, pad.score_musculoskeletal,
    pad.score_neurological, pad.score_gastrointestinal, pad.score_dermatological,
    pad.score_hematologic, pad.score_lymphatic, pad.score_genitourinary,
    pad.score_frailty, pad.score_other,

    pad.findings_clinical, pad.remarks_clinical,

    pad.difficulty_mobility, pad.difficulty_selfcare, pad.difficulty_domestic,
    pad.difficulty_majorlife, pad.difficulty_community,

    pad.findings_functional, pad.remarks_functional,

    pad.rating_none, pad.rating_mild, pad.rating_moderate, pad.rating_severe, pad.rating_complete,
    pad.conclusion_duration, pad.recommended_assistive_products, pad.other_services_required,
    pad.supporting_document, pad.created_at AS pad_created_at,

    doc.document_type

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN progressive_assessment_details pad ON pad.assessment_id = a.id
LEFT JOIN officials d ON a.medical_officer_id = d.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN documents doc ON doc.assessment_id = a.id

WHERE u.id = ? AND a.disability_type = 'Progressive_Chronic'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data   = $result->fetch_assoc();

    if (! $data) {
        echo "<div class='alert alert-warning'>No Progressive Chronic assessment data found for this user.</div>";
        exit;
    }
?>



 <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Patient:                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo htmlspecialchars($data['user_name']); ?></h4>
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
                                            <u>Summary Findings</u>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Date of Injury/Onset of Illness</label>
                                                <input type="date" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['onset_date']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Date of Last Intervention</label>
                                                <input type="date" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['last_intervention_date']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Cause of Disability</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['cause_of_disability']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label>Past and Ongoing Interventions</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['interventions']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Structural / Clinical Assessment</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Structural Impairments</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['structural_impairments']); ?></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Region(s) Affected</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['regions_affected']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Cardiopulmonary / Cardiovascular</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_cardiovascular']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Respiratory</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_respiratory']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Malignancies / Cancer</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_cancer']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Musculoskeletal</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_musculoskeletal']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Neurological</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_neurological']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Gastrointestinal Disorders</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_gastrointestinal']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Dermatological</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_dermatological']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Hematologic System</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_hematologic']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Vascular Conditions</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_lymphatic']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Genito-urinary</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_genitourinary']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Frailty</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_frailty']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Other</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['score_other']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Findings (Clinical)</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['findings_clinical']); ?></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Remarks (Clinical)</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['remarks_clinical']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Functional / Participation Restrictions</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Mobility</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['difficulty_mobility']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Self-care</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['difficulty_selfcare']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Domestic Life</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['difficulty_domestic']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Major Life Areas</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['difficulty_majorlife']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Community, Social, Civic Life</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['difficulty_community']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Findings (Functional)</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['findings_functional']); ?></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Remarks (Functional)</label>
                                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($data['remarks_functional']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Total Disability Rating</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label>None</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['rating_none']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Mild</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['rating_mild']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Moderate</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['rating_moderate']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Severe</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['rating_severe']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Complete</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['rating_complete']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-divider mt-4">
                                            <u>Other Information</u>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Conclusion Duration</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['conclusion_duration']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Supporting Document</label>
                                                <?php if (! empty($data['supporting_document'])): ?>
                                                    <a href="<?php echo htmlspecialchars($data['supporting_document']); ?>" target="_blank" class="btn btn-primary btn-block">View Document</a>
                                                <?php else: ?>
                                                    <input type="text" class="form-control" value="No document uploaded" readonly>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Recommended Assistive Products</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['recommended_assistive_products']); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Other Services Required</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['other_services_required']); ?>" readonly>
                                            </div>
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

