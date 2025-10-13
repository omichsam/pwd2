<?php
// You can include the following if needed in full page:
// include 'files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

if (!isset($_GET['user_id'])) {
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

    
    -- Health Officer
    ho.name AS health_officer_name, ho.license_id AS health_officer_license, ho.email AS health_officer_email,
    ho.mobile_number AS health_officer_mobile, ho.type AS health_officer_type,   

    h.name AS hospital_name, hc.county_name AS hospital_county, h.subcounty AS hospital_subcounty,
    h.address AS hospital_address,

    cda.id AS cda_id, cda.assessment_id AS cda_assessment_id, cda.user_id AS cda_user_id,
    cda.onset_date, cda.last_intervention_date, cda.interventions, cda.cause_of_disability,
    cda.structural_impairments, cda.regions_affected,

    cda.cardiovascular_score, cda.respiratory_score, cda.cancer_score, cda.musculoskeletal_score,
    cda.neurological_score, cda.gastrointestinal_score, cda.dermatological_score,
    cda.hematologic_score, cda.lymphatic_score, cda.genitourinary_score,
    cda.frailty_score, cda.other_score,

    cda.cardiovascular_remark, cda.respiratory_remark, cda.cancer_remark, 
    cda.musculoskeletal_remark, cda.neurological_remark, cda.gastrointestinal_remark,
    cda.dermatological_remark, cda.hematologic_remark, cda.lymphatic_remark,
    cda.genitourinary_remark, cda.frailty_remark, cda.other_remark,

    cda.mobility_difficulty, cda.selfcare_difficulty, cda.domestic_difficulty,
    cda.majorlife_difficulty, cda.community_difficulty,

    cda.mobility_remark, cda.selfcare_remark, cda.domestic_remark,
    cda.majorlife_remark, cda.community_remark,

    cda.disability_rating, cda.recommended_assistive_products, cda.other_services_required,
    cda.conclusion_decision, cda.supporting_document, cda.created_at AS cda_created_at,

    doc.document_type, doc.file_path

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN chronic_disorder_assessments cda ON cda.assessment_id = a.id
LEFT JOIN officials d ON a.medical_officer_id = d.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN documents doc ON doc.assessment_id = a.id

WHERE u.id = ? AND a.disability_type = 'Chronic'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<div class='alert alert-warning'>No Progressive Chronic assessment data found for this user.</div>";
    exit;
}
?>



<div class="section-body">
    <div class="card">
        <div class="card-header">
            <h4>Patient: <?php echo htmlspecialchars($data['user_name']); ?></h4>
        </div>
        <div class="card-body">

            <div class="form-divider mt-3">
                <u>Personal Info</u>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Full Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['user_name']); ?>"
                        readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Gender</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['gender']); ?>"
                        readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Date of Birth</label>
                    <input type="date" class="form-control" value="<?php echo htmlspecialchars($data['dob']); ?>"
                        readonly>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Marital Status</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['marital_status']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>ID Number</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['id_number']); ?>"
                        readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Occupation</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['occupation']); ?>"
                        readonly>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Mobile Number</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['mobile_number']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>"
                        readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>User Type</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['user_type']); ?>"
                        readonly>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Next of Kin Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['next_of_kin_name']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Next of Kin Mobile</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['next_of_kin_mobile']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Next of Kin Relationship</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['next_of_kin_relationship']); ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>County</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['user_county']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Subcounty</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['user_subcounty']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Education Level</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['education_level']); ?>" readonly>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Assessment Information</u>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Assessment Date</label>
                    <input type="date" class="form-control"
                        value="<?php echo htmlspecialchars($data['assessment_date']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Disability Type</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['disability_type']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Status</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['status']); ?>"
                        readonly>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Doctor Information</u>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Doctor's Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['doctor_name']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Doctor's Email</label>
                    <input type="email" class="form-control"
                        value="<?php echo htmlspecialchars($data['doctor_email']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Doctor's Mobile</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['doctor_mobile']); ?>" readonly>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Approver Information</u>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['health_officer_name']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label> Email</label>
                    <input type="email" class="form-control"
                        value="<?php echo htmlspecialchars($data['health_officer_email']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label> Mobile</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['health_officer_mobile']); ?>" readonly>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Hospital Information</u>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Hospital Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['hospital_name']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Hospital County</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['hospital_county']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Hospital Subcounty</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['hospital_subcounty']); ?>" readonly>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Summary Findings</u>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Date of Injury/Onset of Illness</label>
                    <input type="date" class="form-control" value="<?php echo htmlspecialchars($data['onset_date']); ?>"
                        readonly>
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
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['interventions']); ?></textarea>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Structural / Clinical Assessment</u>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Structural Impairments</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['structural_impairments']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Region(s) Affected</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['regions_affected']); ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Cardiopulmonary / Cardiovascular</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['cardiovascular_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Respiratory</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['respiratory_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Malignancies / Cancer</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['cancer_score']); ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Musculoskeletal</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['musculoskeletal_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Neurological</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['neurological_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Gastrointestinal Disorders</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['gastrointestinal_score']); ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Dermatological</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['dermatological_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Hematologic System</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['hematologic_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Vascular Conditions</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['lymphatic_score']); ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Genito-urinary</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['genitourinary_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Frailty</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['frailty_score']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Other</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['other_score']); ?>" readonly>
                </div>
            </div>

            <!-- Clinical Remarks Section -->
            <div class="form-divider mt-4">
                <u>Clinical Assessment Remarks</u>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Cardiovascular Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['cardiovascular_remark']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Respiratory Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['respiratory_remark']); ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Cancer Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['cancer_remark']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Musculoskeletal Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['musculoskeletal_remark']); ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Neurological Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['neurological_remark']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Gastrointestinal Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['gastrointestinal_remark']); ?></textarea>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Functional / Participation Restrictions</u>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Mobility</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['mobility_difficulty']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Self-care</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['selfcare_difficulty']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Domestic Life</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['domestic_difficulty']); ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Major Life Areas</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['majorlife_difficulty']); ?>" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>Community, Social, Civic Life</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['community_difficulty']); ?>" readonly>
                </div>
            </div>

            <!-- Functional Remarks Section -->
            <div class="form-divider mt-4">
                <u>Functional Assessment Remarks</u>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Mobility Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['mobility_remark']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Self-care Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['selfcare_remark']); ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Domestic Life Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['domestic_remark']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Major Life Areas Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['majorlife_remark']); ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Community Life Remark</label>
                    <textarea class="form-control" rows="2"
                        readonly><?php echo htmlspecialchars($data['community_remark']); ?></textarea>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Total Disability Rating</u>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Disability Rating</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['disability_rating']); ?>" readonly>
                </div>
            </div>

            <div class="form-divider mt-4">
                <u>Other Information</u>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Conclusion Decision</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['conclusion_decision']); ?>" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>Supporting Document</label>
                    <?php if (!empty($data['supporting_document'])): ?>
                        <a href="<?php echo htmlspecialchars($data['supporting_document']); ?>" target="_blank"
                            class="btn btn-primary btn-block">View Document</a>
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
                    <?php if (!empty($data['file_path'])): ?>
                        <?php
                        $document_url = "" . htmlspecialchars($data['file_path']);
                        $document_extension = pathinfo($data['file_path'], PATHINFO_EXTENSION);
                        $file_name = basename($data['file_path']);
                        ?>

                        <div class="mb-2">
                            <span class="font-weight-bold">File:</span>
                            <?php echo htmlspecialchars($file_name); ?>
                            <span class="badge badge-secondary ml-2"><?php echo strtoupper($document_extension); ?></span>
                        </div>

                        <div class="btn-group">
                            <?php if ($document_extension == 'pdf'): ?>
                                <a href="<?php echo $document_url; ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-file-pdf"></i> View PDF
                                </a>
                            <?php elseif (in_array($document_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <a href="<?php echo $document_url; ?>" target="_blank" class="btn btn-success">
                                    <i class="fas fa-image"></i> View Image
                                </a>
                            <?php else: ?>
                                <a href="<?php echo $document_url; ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-file"></i> View Document
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo $document_url; ?>" download class="btn btn-warning">
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