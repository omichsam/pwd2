<?php
include 'files/header.php';
?>

<!-- top navigation  -->
<?php include 'files/nav.php'; ?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approval'])) {
    // Get the form data
    // assessment_id=1&health_officer_id+=1&decision=approve&comment=
    $assessment_id = $_POST['assessment_id'];
    $health_officer_id = $_POST['health_officer_id'];
    $decision = $_POST['decision'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    // Validate input
    if ($decision === 'reject' && empty($comment)) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Rejection Reason Required',
                    text: 'Please provide a comment when rejecting the assessment.'
                });
              </script>";
        exit;
    }

    // Prepare the SQL query
    if ($decision === 'approve') {
        $status = 'approved_by_health_officer';
        $comment = ''; // No comment needed for approval
    } else {
        $status = 'rejected';
    }


    // Update the assessment status and health officer ID in the database
    $query = "UPDATE assessments SET status = ?, comment = ?, health_officer_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssii", $status, $comment, $health_officer_id, $assessment_id);

    if (mysqli_stmt_execute($stmt)) {
        // Success
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'The assessment has been updated.'
                }).then(() => {
                    window.location.href = 'complete_assessment'; // Redirect or reload
                });
              </script>";
    } else {
        // Error
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'There was an issue updating the assessment.'
                });
              </script>";
    }

    mysqli_stmt_close($stmt);

}
?>

<?php
// Assuming you're using PDO and have already established a database connection
$user_id = intval($_GET['user_id']);
$type = $_GET['type'];  // 'hearing', 'physical', etc.

// Prepare different queries based on disability type
if ($type == 'Hearing') {
    $sql = "
    SELECT 
        -- User Info
        u.name AS user_name, u.gender, u.dob, u.marital_status, u.id_number, u.occupation,
        u.mobile_number, u.email, u.type AS user_type, u.next_of_kin_name, u.next_of_kin_mobile,
        u.next_of_kin_relationship, uc.county_name AS user_county, u.subcounty AS user_subcounty, u.education_level,

        -- Assessment Info
        a.id AS assessment_id, a.disability_type, a.assessment_date, a.assessment_time, a.status,
        a.created_at AS assessment_created,

        -- Medical Officer
        mo.name AS medical_officer_name, mo.license_id AS medical_officer_license, mo.email AS medical_officer_email,
        mo.mobile_number AS medical_officer_mobile, mo.type AS medical_officer_type,

        -- Health Officer
        ho.name AS health_officer_name, ho.license_id AS health_officer_license, ho.email AS health_officer_email,
        ho.mobile_number AS health_officer_mobile, ho.type AS health_officer_type,

        -- Hospital Info
        h.name AS hospital_name, hc.county_name AS hospital_county, h.subcounty AS hospital_subcounty, h.address AS hospital_address,

        -- Hearing Disability Assessment
        hda.id AS hearing_assessment_id, hda.history_of_hearing_loss, hda.history_of_hearing_devices,
        hda.hearing_test_type_right, hda.hearing_test_type_left, hda.hearing_loss_degree_right,
        hda.hearing_loss_degree_left, hda.hearing_level_dbhl_right, hda.hearing_level_dbhl_left,
        hda.monaural_percentage_right, hda.monaural_percentage_left, hda.overall_binaural_percentage,
        hda.conclusion, hda.recommended_assistive_products, hda.required_services
    FROM users u
    JOIN assessments a ON a.user_id = u.id
    LEFT JOIN officials mo ON a.medical_officer_id = mo.id
    LEFT JOIN officials ho ON a.health_officer_id = ho.id
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    LEFT JOIN hearing_disability_assessments hda ON hda.assessment_id = a.id
    LEFT JOIN counties uc ON u.county_id = uc.id
    LEFT JOIN counties hc ON h.county_id = hc.id
    WHERE u.id = ? AND a.disability_type = 'Hearing';
    ";
} elseif ($type == 'Physical') {
    $sql = "
    SELECT 
        -- User Info
        u.name AS user_name, u.gender, u.dob, u.marital_status, u.id_number, u.occupation,
        u.mobile_number, u.email, u.type AS user_type, u.next_of_kin_name, u.next_of_kin_mobile,
        u.next_of_kin_relationship, uc.county_name AS user_county, u.subcounty AS user_subcounty, u.education_level,

        -- Assessment Info
        a.id AS assessment_id, a.disability_type, a.assessment_date, a.assessment_time, a.status,
        a.created_at AS assessment_created,

        -- Medical Officer
        mo.name AS medical_officer_name, mo.license_id AS medical_officer_license, mo.email AS medical_officer_email,
        mo.mobile_number AS medical_officer_mobile, mo.type AS medical_officer_type,

        -- Health Officer
        ho.name AS health_officer_name, ho.license_id AS health_officer_license, ho.email AS health_officer_email,
        ho.mobile_number AS health_officer_mobile, ho.type AS health_officer_type,

        -- Hospital Info
        h.name AS hospital_name, hc.county_name AS hospital_county, h.subcounty AS hospital_subcounty, h.address AS hospital_address,

        -- Physical Disability Assessment
        pda.id AS physical_assessment_id, pda.medical_history, pda.injury_date, pda.last_intervention_date,
        pda.cause_of_disability, pda.structural_impairments, pda.region_affected,
        pda.s710_head_neck, pda.s720_shoulder, pda.s730_upper_extremity, pda.s740_pelvis,
        pda.s750_lower_extremity, pda.s760_trunk, pda.s8_skin_other, pda.muscle_power_score,
        pda.joint_range_score, pda.angulation_score, pda.amputation_score, pda.limb_length_score,
        pda.balance_coordination_score, pda.restrictions_remark, pda.impairments_remark,
        pda.other_impairments_score, pda.impairments_total_score, pda.mobility_score, pda.self_care_score,
        pda.domestic_life_score, pda.major_life_areas_score, pda.community_social_score, pda.restrictions_total_score,
        pda.disability_rating, pda.conclusion_type, pda.findings, pda.remarks, pda.assistive_products, pda.required_services
    FROM users u
    JOIN assessments a ON a.user_id = u.id
    LEFT JOIN officials mo ON a.medical_officer_id = mo.id
    LEFT JOIN officials ho ON a.health_officer_id = ho.id
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    LEFT JOIN physical_disability_assessments pda ON pda.assessment_id = a.id
    LEFT JOIN counties uc ON u.county_id = uc.id
    LEFT JOIN counties hc ON h.county_id = hc.id
    WHERE u.id = ? AND a.disability_type = 'Physical';
    ";
} else {
    // Handle unknown disability type
    echo "Unknown disability type!";
    exit;
}

// Prepare and execute query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Bind the user ID to the query
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Check if data is found
if (!$data) {
    echo "Data not found.";
    exit;
}

// Output the data as JSON
// echo json_encode($data);
?>

<!-- navigation -->
<?php include 'files/sidebar.php'; ?>




<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>


            <!-- top navigation  -->
            <?php include 'files/nav.php'; ?>


            <!-- navigation -->
            <?php include 'files/sidebar.php'; ?>

            <!-- Main Content -->



            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Approval</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                            <div class="breadcrumb-item">Single Assessment</div>
                        </div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-8 col-md-8 col-lg-8 text-left">
                                <h2 class="section-title">Hi,
                                    <?php echo htmlspecialchars($pwdUser['name']); ?>!
                                </h2>
                                <p class="section-lead">View information about Assessment for
                                    <?php echo htmlspecialchars($data['user_name']); ?>.
                                </p>
                            </div>
                            <!-- <div class="col-md-4"></div> -->
                            <div class="col-md-4 text-right mt-4">
                                <!-- <a href="complete_assessment" class="btn btn-primary shadow-sm text-right approve-btn"
                                    data-id="123">
                                    << Approve </a> -->

                                <!-- <a class="btn btn-primary shadow-sm text-right approve-btn" data-id="123">
                                    << Approve </a>  -->

                                <?php if (isset($_GET['from']) && $_GET['from'] === 'assessment') { ?>
                                    <button class="btn btn-primary open-approval-modal approve-btn shadow-sm text-right"
                                        data-id="<?= $data['assessment_id'] ?>">
                                        Approve/Reject Assessment
                                    </button>
                                <?php } ?>


                            </div>
                        </div>





                        <div class="row mt-sm-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-top border-success">
                                    <form class="needs-validation p-3" novalidate>
                                        <div class="card-header bg-light">
                                            <h4 class="mb-0">Assessment Summary</h4>
                                        </div>

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
                                            <u>Medical Officer Information</u>
                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Name</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['medical_officer_name']); ?>"
                                                    readonly>
                                            </div>
                                            <!-- <div class="form-group col-md-4">
                                                <label>Doctor's License ID</label>
                                                <input type="text" class="form-control"
                                                    value="< ?php echo htmlspecialchars($data['doctor_license']); ?>"
                                                    readonly>
                                            </div> -->
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Mobile</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['medical_officer_mobile']); ?>"
                                                    readonly>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Doctor's Email</label>
                                                <input type="email" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['medical_officer_email']); ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row" hidden>
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Mobile</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['medical_officer_mobile']); ?>"
                                                    readonly>
                                            </div>
                                            <!-- <div class="form-group col-md-4">
                                                <label>Doctor's Type</label>
                                                <input type="text" class="form-control"
                                                    value="< ?php echo htmlspecialchars($data['doctor_type']); ?>"
                                                    readonly>
                                            </div> -->
                                        </div>



                                        <!-- medical officer  -->
                                        <div class="form-divider mt-4">
                                            <u>Health Officer Information</u>
                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Name</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['health_officer_name']); ?>"
                                                    readonly>
                                            </div>
                                            <!-- <div class="form-group col-md-4">
                                                <label>Doctor's License ID</label>
                                                <input type="text" class="form-control"
                                                    value="< ?php echo htmlspecialchars($data['doctor_license']); ?>"
                                                    readonly>
                                            </div> -->
                                            <div class="form-group col-md-4">
                                                <label>Doctor's Mobile</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['health_officer_mobile']); ?>"
                                                    readonly>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Doctor's Email</label>
                                                <input type="email" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['health_officer_email']); ?>"
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

                                        <?php if ($type == "Hearing") { ?>
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
                                        <?php }
                                        if ($type == "Physical") { ?>


                                            <div class="form-divider mt-4">
                                                <u>Medical Information</u>
                                            </div>

                                            <div class="row">
                                                <!-- Medical History -->
                                                <div class="form-group col-md-4">
                                                    <label>Medical History</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['medical_history']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Injury Date -->
                                                <div class="form-group col-md-4">
                                                    <label>Injury Date</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['injury_date']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Last Intervention Date -->
                                                <div class="form-group col-md-4">
                                                    <label>Last Intervention Date</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['last_intervention_date']); ?>"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="form-divider mt-4">
                                                <u>Disability Information</u>
                                            </div>

                                            <div class="row">
                                                <!-- Cause of Disability -->
                                                <div class="form-group col-md-4">
                                                    <label>Cause of Disability</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['cause_of_disability']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Muscle Power Score -->
                                                <div class="form-group col-md-4">
                                                    <label>Muscle Power Score</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['muscle_power_score']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Joint Range Score -->
                                                <div class="form-group col-md-4">
                                                    <label>Joint Range Score</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['joint_range_score']); ?>"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="form-divider mt-4">
                                                <u>Assessment Details</u>
                                            </div>

                                            <div class="row">
                                                <!-- Angulation Score -->
                                                <div class="form-group col-md-4">
                                                    <label>Angulation Score</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['angulation_score']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Amputation Score -->
                                                <div class="form-group col-md-4">
                                                    <label>Amputation Score</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['amputation_score']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Impairments Remark -->
                                                <div class="form-group col-md-4">
                                                    <label>Impairments Remark</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['impairments_remark']); ?>"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="form-divider mt-4">
                                                <u>Scores and Ratings</u>
                                            </div>

                                            <div class="row">
                                                <!-- Mobility Score -->
                                                <div class="form-group col-md-4">
                                                    <label>Mobility Score</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['mobility_score']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Self-Care Score -->
                                                <div class="form-group col-md-4">
                                                    <label>Self-Care Score</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['self_care_score']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Restrictions Remark -->
                                                <div class="form-group col-md-4">
                                                    <label>Restrictions Remark</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['restrictions_remark']); ?>"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="form-divider mt-4">
                                                <u>Conclusion and Recommendations</u>
                                            </div>

                                            <div class="row">
                                                <!-- Disability Rating -->
                                                <div class="form-group col-md-4">
                                                    <label>Disability Rating</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['disability_rating']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Conclusion Type -->
                                                <div class="form-group col-md-4">
                                                    <label>Conclusion Type</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['conclusion_type']); ?>"
                                                        readonly>
                                                </div>

                                                <!-- Assistive Products -->
                                                <div class="form-group col-md-4">
                                                    <label>Assistive Products</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['assistive_products']); ?>"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="form-divider mt-4">
                                                <u>Required Services</u>
                                            </div>

                                            <div class="row">
                                                <!-- Required Services -->
                                                <div class="form-group col-md-4">
                                                    <label>Required Services</label>
                                                    <input type="text" class="form-control"
                                                        value="<?php echo htmlspecialchars($data['required_services']); ?>"
                                                        readonly>
                                                </div>
                                            </div>


                                        <?php } ?>

                                        <div class="form-divider mt-4">
                                            <u><strong>Uploaded Document</strong></u>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label>Document</label>
                                                <?php if (!empty($data['file_path'])): ?>
                                                    <?php
                                                    // Construct the full document URL using the file path
                                                    $document_url = "../medical/uploads/" . htmlspecialchars($data['file_path']);
                                                    $document_extension = pathinfo($data['file_path'], PATHINFO_EXTENSION);
                                                    $file_name = basename($data['file_path']);
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




                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>



        </div>


        <!-- Modal -->
        <!-- Approval Modal -->
        <!-- Modal -->
        <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title" id="approvalModalLabel">Approval/Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="approvalForm" action="" method="POST">
                            <input type="hidden" name="assessment_id" id="assessment_id"
                                value="<?= $data['assessment_id'] ?>">
                            <input type="hidden" name="health_officer_id" id="health_officer_id"
                                value="<?= $pwdUser['id'] ?>">
                            <div class="mb-3">
                                <label for="decision" class="form-label">Decision</label>
                                <select class="form-select" id="decision" name="decision" required>
                                    <option value="">Select an option</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                </select>
                            </div>
                            <div class="mb-3" id="commentBox" class="d-none">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3"
                                    placeholder="Provide a comment for rejection"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="approval">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap Bundle + jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function () {
                // Show/hide comment box based on rejection decision
                $('#decision').on('change', function () {
                    if ($(this).val() === 'reject') {
                        $('#commentBox').removeClass('d-none');
                    } else {
                        $('#commentBox').addClass('d-none');
                        $('#comment').val('');
                    }
                });

                // Show modal and assign assessment ID
                $('.open-approval-modal').on('click', function () {
                    const assessmentId = $(this).data('id');
                    $('#assessment_id').val(assessmentId);
                    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                    modal.show();
                });
            });

        </script>


        <?php include 'files/footer.php'; ?>