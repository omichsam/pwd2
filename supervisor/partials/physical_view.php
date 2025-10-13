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

        d.name AS doctor_name, d.email AS doctor_email, d.mobile_number AS doctor_mobile, d.type AS doctor_type,

        -- Health Officer
        ho.name AS health_officer_name, ho.license_id AS health_officer_license, ho.email AS health_officer_email,
        ho.mobile_number AS health_officer_mobile, ho.type AS health_officer_type,   
    
        h.name AS hospital_name, hc.county_name AS hospital_county, h.subcounty AS hospital_subcounty,
        h.address AS hospital_address,

        pda.id AS pda_id, pda.assessment_id, pda.user_id AS pda_user_id, pda.disability_type AS pda_disability_type,
        pda.onset_date, pda.last_intervention_date, pda.cause_of_disability, pda.regions_affected,
        pda.impairment_score_muscle_power, pda.remark_muscle_power,
        pda.impairment_score_joint_motion, pda.remark_joint_motion,
        pda.impairment_score_structural_deviation, pda.remark_structural_deviation,
        pda.impairment_score_limb_amputation, pda.remark_limb_amputation,
        pda.impairment_score_limb_length, pda.remark_limb_length,
        pda.impairment_score_balance_coordination, pda.remark_balance_coordination,
        pda.impairment_score_other_impairments, pda.remark_other_impairments,
        pda.impairment_rating,
        pda.function_mobility, pda.remark_mobility,
        pda.function_hand_use, pda.remark_hand_use,
        pda.function_grip_strength, pda.remark_grip_strength,
        pda.function_selfcare, pda.remark_selfcare,
        pda.function_daily_life, pda.remark_daily_life,
        pda.function_work, pda.remark_work,
        pda.disability_rating,
        pda.assistive_products, pda.other_services, pda.conclusion_decision,
        pda.supporting_document AS file_path,
        pda.created_at AS pda_created_at

    FROM users u
    JOIN assessments a ON a.user_id = u.id
    LEFT JOIN physical_disability_assessment pda ON pda.assessment_id = a.id
    LEFT JOIN officials d ON a.medical_officer_id = d.id
    LEFT JOIN officials ho ON a.health_officer_id = ho.id
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    LEFT JOIN counties uc ON u.county_id = uc.id
    LEFT JOIN counties hc ON h.county_id = hc.id

    WHERE u.id = ? AND a.disability_type = 'Physical'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<div class='alert alert-warning'>No physical disability assessment data found for this user.</div>";
    exit;
}
?>

<div class="section-body">
    <div class="card">
        <div class="card-header">
            <h4>Patient: <?php echo htmlspecialchars($data['user_name']); ?></h4>
        </div>
        <div class="card-body">

            <!-- Personal Information Section -->
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

            <!-- Assessment Information -->
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

            <!-- Doctor Information -->
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

            <!-- Health Officer Information -->
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
                    <label>Email</label>
                    <input type="email" class="form-control"
                        value="<?php echo htmlspecialchars($data['health_officer_email']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Mobile</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['health_officer_mobile']); ?>" readonly>
                </div>
            </div>

            <!-- Hospital Information -->
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

            <!-- Physical Disability Assessment -->
            <div class="form-divider mt-4">
                <u>Physical Disability Assessment</u>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Onset Date</label>
                    <input type="date" class="form-control" value="<?php echo htmlspecialchars($data['onset_date']); ?>"
                        readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Last Intervention Date</label>
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
                    <label>Regions Affected</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['regions_affected']); ?>" readonly>
                </div>
            </div>

            <!-- Impairment Scores -->
            <div class="form-divider mt-4">
                <u>Impairment Scores</u>
            </div>

            <div class="row">
                <?php
                $impairments = [
                    'impairment_score_muscle_power' => ['label' => 'Muscle Power', 'remark' => 'remark_muscle_power'],
                    'impairment_score_joint_motion' => ['label' => 'Joint Motion', 'remark' => 'remark_joint_motion'],
                    'impairment_score_structural_deviation' => ['label' => 'Structural Deviation', 'remark' => 'remark_structural_deviation'],
                    'impairment_score_limb_amputation' => ['label' => 'Limb Amputation', 'remark' => 'remark_limb_amputation'],
                    'impairment_score_limb_length' => ['label' => 'Limb Length', 'remark' => 'remark_limb_length'],
                    'impairment_score_balance_coordination' => ['label' => 'Balance/Coordination', 'remark' => 'remark_balance_coordination'],
                    'impairment_score_other_impairments' => ['label' => 'Other Impairments', 'remark' => 'remark_other_impairments'],
                ];

                foreach ($impairments as $field => $info) {
                    echo '<div class="form-group col-md-6">
                            <label>' . $info['label'] . ' Score</label>
                            <input type="text" class="form-control" value="' . htmlspecialchars($data[$field]) . '" readonly>
                        </div>';
                    echo '<div class="form-group col-md-6">
                            <label>' . $info['label'] . ' Remarks</label>
                            <textarea class="form-control" rows="2" readonly>' . htmlspecialchars($data[$info['remark']]) . '</textarea>
                        </div>';
                }
                ?>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label>Overall Impairment Rating</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['impairment_rating']); ?>" readonly>
                </div>
            </div>

            <!-- Functional Abilities -->
            <div class="form-divider mt-4">
                <u>Functional Abilities</u>
            </div>

            <div class="row">
                <?php
                $functions = [
                    'function_mobility' => ['label' => 'Mobility', 'remark' => 'remark_mobility'],
                    'function_hand_use' => ['label' => 'Hand Use', 'remark' => 'remark_hand_use'],
                    'function_grip_strength' => ['label' => 'Grip Strength', 'remark' => 'remark_grip_strength'],
                    'function_selfcare' => ['label' => 'Self Care', 'remark' => 'remark_selfcare'],
                    'function_daily_life' => ['label' => 'Daily Life', 'remark' => 'remark_daily_life'],
                    'function_work' => ['label' => 'Work', 'remark' => 'remark_work'],
                ];

                foreach ($functions as $field => $info) {
                    echo '<div class="form-group col-md-6">
                            <label>' . $info['label'] . '</label>
                            <input type="text" class="form-control" value="' . htmlspecialchars($data[$field]) . '" readonly>
                        </div>';
                    echo '<div class="form-group col-md-6">
                            <label>' . $info['label'] . ' Remarks</label>
                            <textarea class="form-control" rows="2" readonly>' . htmlspecialchars($data[$info['remark']]) . '</textarea>
                        </div>';
                }
                ?>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label>Overall Disability Rating</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['disability_rating']); ?>" readonly>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="form-divider mt-4">
                <u>Recommendations</u>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label>Assistive Products</label>
                    <textarea class="form-control" rows="3"
                        readonly><?php echo htmlspecialchars($data['assistive_products']); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label>Other Services</label>
                    <textarea class="form-control" rows="3"
                        readonly><?php echo htmlspecialchars($data['other_services']); ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label>Conclusion Decision</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['conclusion_decision']); ?>" readonly>
                </div>
            </div>

            <!-- Uploaded Document -->
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