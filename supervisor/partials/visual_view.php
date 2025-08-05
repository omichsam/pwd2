<?php
// You can include the following if needed in full page:
// include 'files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

if (!isset($_GET['user_id'])) {
    echo 'User ID is required.';
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

    vad.assistive_device,
    vad.medical_history,
    vad.ocular_history,
    vad.right_eye_with_correction,
    vad.right_eye_without_correction,
    vad.left_eye_with_correction,
    vad.left_eye_without_correction,
    vad.near_vision_with_correction,
    vad.near_vision_without_correction,

    vad.present_eyeball_right,
    vad.squint_right,
    vad.nystagmus_right,
    vad.tearing_right,
    vad.lids_right,
    vad.conjunctiva_right,
    vad.cornea_right,
    vad.anterior_chamber_right,
    vad.iris_right,
    vad.pupil_right,
    vad.lens_right,
    vad.fundus_right,

    vad.present_eyeball_left,
    vad.squint_left,
    vad.nystagmus_left,
    vad.tearing_left,
    vad.lids_left,
    vad.conjunctiva_left,
    vad.cornea_left,
    vad.anterior_chamber_left,
    vad.iris_left,
    vad.pupil_left,
    vad.lens_left,
    vad.fundus_left,

    vad.hvf,
    vad.colour_vision,
    vad.stereopsis,

    vad.category,
    vad.cause_of_vision,
    vad.percentage_disability,
    vad.possible_intervention,
    vad.recommendation,
    vad.conclusion_duration,

    doc.document_type

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN visual_assessment_details vad ON vad.assessment_id = a.id
LEFT JOIN officials d ON a.medical_officer_id = d.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN documents doc ON doc.assessment_id = a.id

WHERE u.id = ? AND a.disability_type = 'Visual'";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<div class='alert alert-warning'>No Visual assessment data found for this user.</div>";
    exit;
}
?>

<div class='section-body'>
    <div class='card'>
        <div class='card-header'>
            <h4>Patient: <?php echo htmlspecialchars($data['user_name']);
            ?></h4>
        </div>
        <div class='card-body'>

            <div class='form-divider mt-3'>
                <u>Personal Info</u>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Full Name</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['user_name']); ?>"
                        readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Gender</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['gender']); ?>"
                        readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Date of Birth</label>
                    <input type='date' class='form-control' value="<?php echo htmlspecialchars($data['dob']); ?>"
                        readonly>
                </div>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Marital Status</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['marital_status']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>ID Number</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['id_number']); ?>"
                        readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Occupation</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['occupation']); ?>"
                        readonly>
                </div>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Mobile Number</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['mobile_number']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Email</label>
                    <input type='email' class='form-control' value="<?php echo htmlspecialchars($data['email']); ?>"
                        readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>User Type</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['user_type']); ?>"
                        readonly>
                </div>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Next of Kin Name</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['next_of_kin_name']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Next of Kin Mobile</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['next_of_kin_mobile']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Next of Kin Relationship</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['next_of_kin_relationship']); ?>" readonly>
                </div>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>County</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['user_county']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Subcounty</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['user_subcounty']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Education Level</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['education_level']); ?>" readonly>
                </div>
            </div>

            <div class='form-divider mt-4'>
                <u>Assessment Information</u>
            </div>
            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Assessment Date</label>
                    <input type='date' class='form-control'
                        value="<?php echo htmlspecialchars($data['assessment_date']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Disability Type</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['disability_type']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Status</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['status']); ?>"
                        readonly>
                </div>
            </div>

            <div class='form-divider mt-4'>
                <u>Doctor Information</u>
            </div>
            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Doctor's Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['doctor_name']); ?>" readonly>
                </div>
                <!-- <div class="form-group col-md-4">
                                                <label>Doctor's License ID</label>
<input type = 'text' class = 'form-control'
value = "< ?php echo htmlspecialchars($data['doctor_license']); ?>"
readonly>
</div> -->

                <div class='form-group col-md-4'>
                    <label>Doctor's Email</label>
                    <input type="email" class="form-control"
                        value="<?php echo htmlspecialchars($data['doctor_email']); ?>" readonly>
                </div>

                <div class="form-group col-md-4">
                    <label>Doctor's Mobile</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['doctor_mobile']); ?>" readonly>
                </div>
            </div>

            <div class='row' hidden>
                <div class='form-group col-md-4'>
                    <label>Doctor's Mobile</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($data['doctor_mobile']); ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label>Doctor's Type</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['doctor_type']); ?>" readonly>
                </div>
            </div>

            <div class='form-divider mt-4'>
                <u>Approver Information</u>
            </div>
            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Name</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['health_officer_name']); ?>" readonly>
                </div>

                <div class='form-group col-md-4'>
                    <label> Email</label>
                    <input type='email' class='form-control'
                        value="<?php echo htmlspecialchars($data['health_officer_email']); ?>" readonly>
                </div>

                <div class='form-group col-md-4'>
                    <label> Mobile</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['health_officer_mobile']); ?>" readonly>
                </div>
            </div>

            <div class='form-divider mt-4'>
                <u>Hospital Information</u>
            </div>
            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Hospital Name</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['hospital_name']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Hospital County</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['hospital_county']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Hospital Subcounty</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['hospital_subcounty']); ?>" readonly>
                </div>
            </div>

            <div class='form-group' hidden>
                <label>Hospital Address</label>
                <textarea class='form-control' rows='3' readonly><?php echo htmlspecialchars($data['hospital_address']);
                ?></textarea>
            </div>

            <div class='form-divider mt-4'>
                <u>Visual Assessment Details</u>
            </div>

            <div class='row'>
                <div class='form-group col-md-6'>
                    <label>Assistive Device</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['assistive_device']); ?>" readonly>
                </div>
                <div class='form-group col-md-6'>
                    <label>Medical History</label>
                    <textarea class='form-control' rows='2' readonly><?php echo htmlspecialchars($data['medical_history']);
                    ?></textarea>
                </div>
            </div>

            <div class='form-group'>
                <label>Ocular History</label>
                <textarea class='form-control' rows='2' readonly><?php echo htmlspecialchars($data['ocular_history']);
                ?></textarea>
            </div>

            <div class='form-divider mt-4'>
                <u>Vision Details</u>
            </div>

            <div class='row'>
                <div class='form-group col-md-3'>
                    <label>Right Eye ( with correction )</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['right_eye_with_correction']); ?>" readonly>
                </div>
                <div class='form-group col-md-3'>
                    <label>Right Eye ( without correction )</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['right_eye_without_correction']); ?>" readonly>
                </div>
                <div class='form-group col-md-3'>
                    <label>Left Eye ( with correction )</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['left_eye_with_correction']); ?>" readonly>
                </div>
                <div class='form-group col-md-3'>
                    <label>Left Eye ( without correction )</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['left_eye_without_correction']); ?>" readonly>
                </div>
            </div>

            <div class='row'>
                <div class='form-group col-md-6'>
                    <label>Near Vision ( with correction )</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['near_vision_with_correction']); ?>" readonly>
                </div>
                <div class='form-group col-md-6'>
                    <label>Near Vision ( without correction )</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['near_vision_without_correction']); ?>" readonly>
                </div>
            </div>

            <div class='form-divider mt-4'>
                <u>Eye Examination - Right Eye</u>
            </div>

            <div class='row'>
                <?php
                $right_eye_fields = ['present_eyeball_right', 'squint_right', 'nystagmus_right', 'tearing_right', 'lids_right', 'conjunctiva_right', 'cornea_right', 'anterior_chamber_right', 'iris_right', 'pupil_right', 'lens_right', 'fundus_right'];
                foreach ($right_eye_fields as $field) {
                    echo '<div class="form-group col-md-3">
                         <label>' . ucwords(str_replace('_', ' ', str_replace('_right', '', $field))) . '</label>
                            <input type="text" class="form-control" value="' . htmlspecialchars($data[$field]) . '" readonly>
                         </div>';
                }
                ?>
            </div>

            <div class='form-divider mt-4'>
                <u>Eye Examination - Left Eye</u>
            </div>

            <div class='row'>
                <?php
                $left_eye_fields = ['present_eyeball_left', 'squint_left', 'nystagmus_left', 'tearing_left', 'lids_left', 'conjunctiva_left', 'cornea_left', 'anterior_chamber_left', 'iris_left', 'pupil_left', 'lens_left', 'fundus_left'];
                foreach ($left_eye_fields as $field) {
                    echo '<div class="form-group col-md-3">
                                                    <label>' . ucwords(str_replace('_', ' ', str_replace('_left', '', $field))) . '</label>
                                                    <input type="text" class="form-control"
                                                        value="' . htmlspecialchars($data[$field]) . '" readonly>
                                                </div>';
                }
                ?>
            </div>

            <div class='form-divider mt-4'>
                <u>Visual Function Tests</u>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>HVF( Humphreys VIsual Field )</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['hvf']); ?>"
                        readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Colour Vision</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['colour_vision']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Stereopsis</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['stereopsis']); ?>"
                        readonly>
                </div>
            </div>

            <div class='form-divider mt-4'>
                <u>Assessment Summary</u>
            </div>

            <div class='row'>
                <div class='form-group col-md-4'>
                    <label>Disability Category</label>
                    <input type='text' class='form-control' value="<?php echo htmlspecialchars($data['category']); ?>"
                        readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Cause of Vision Impairment</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['cause_of_vision']); ?>" readonly>
                </div>
                <div class='form-group col-md-4'>
                    <label>Disability Percentage</label>
                    <input type='text' class='form-control'
                        value="<?php echo htmlspecialchars($data['percentage_disability']); ?>" readonly>
                </div>
            </div>

            <div class='row'>
                <div class='form-group col-md-6'>
                    <label>Possible Intervention</label>
                    <textarea class='form-control' rows='2' readonly><?php echo htmlspecialchars($data['possible_intervention']);
                    ?></textarea>
                </div>
                <div class='form-group col-md-6'>
                    <label>Recommendation</label>
                    <textarea class='form-control' rows='2' readonly><?php echo htmlspecialchars($data['recommendation']);
                    ?></textarea>
                </div>
            </div>

            <div class='form-group'>
                <label>Conclusion Duration</label>
                <input type='text' class='form-control'
                    value="<?php echo htmlspecialchars($data['conclusion_duration']); ?>" readonly>
            </div>

            <div class='form-divider mt-4'>
                <u><strong>Uploaded Document</strong></u>
            </div>

            <div class='row'>
                <div class='form-group col-md-12'>
                    <label>Document</label>
                    <?php if (!empty($data['file_path'])): ?>
                        <?php
                        $document_url = '' . htmlspecialchars($data['file_path']);
                        $document_extension = pathinfo($data['file_path'], PATHINFO_EXTENSION);
                        $file_name = basename($data['file_path']);
                        ?>

                        <div class='mb-2'>
                            <span class='font-weight-bold'>File:</span>
                            <?php echo htmlspecialchars($file_name);
                            ?>
                            <span class='badge badge-secondary ml-2'>
                                <?php echo strtoupper($document_extension);
                                ?>
                            </span>
                        </div>

                        <div class='btn-group'>
                            <?php if ($document_extension == 'pdf'): ?>
                                <a href="<?php echo $document_url; ?>" target='_blank' class='btn btn-primary'>
                                    <i class='fas fa-file-pdf'></i> View PDF
                                </a>
                            <?php elseif (in_array($document_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <a href="<?php echo $document_url; ?>" target='_blank' class='btn btn-success'>
                                    <i class='fas fa-image'></i> View Image
                                </a>
                            <?php else: ?>
                                <a href="<?php echo $document_url; ?>" target='_blank' class='btn btn-primary'>
                                    <i class='fas fa-file'></i> View Document
                                </a>
                            <?php endif;
                            ?>

                            <a href="<?php echo $document_url; ?>" download class='btn btn-warning'>
                                <i class='fas fa-download'></i> Download
                            </a>
                        </div>

                    <?php else: ?>
                        <div class='alert alert-info'>
                            <i class='fas fa-info-circle'></i> No document uploaded
                        </div>
                    <?php endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>