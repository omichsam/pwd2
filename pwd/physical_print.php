<?php
include 'files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

// @$user_id = intval($_GET['user_id']);
 $user_id = $pwdUser['id'] ?? null;

// Updated SQL with correct field mappings for physical_disability_assessment table
$sql = "SELECT
    u.name AS user_name,
    a.id AS assessment_id,
    a.update_time, 
    u.gender,
    u.dob,
    u.marital_status,
    u.id_number,
    u.occupation,
    u.mobile_number,
    u.email,
    u.next_of_kin_name,
    u.next_of_kin_mobile,
    u.next_of_kin_relationship,
    uc.county_name AS user_county,
    u.subcounty AS user_subcounty,
    a.assessment_date,

    /* Medical Officer */
    mo.name AS medical_officer_name,
    mo.license_id AS medical_license,
    mo.email AS medical_email,

    /* County Officer */
    co.name AS county_officer_name,
    co.license_id AS county_license,
    co.email AS county_email,

    /* Health Officer */
    ho.name AS health_officer_name,
    ho.license_id AS health_license,
    ho.email AS health_email,

    /* Hospital Details */
    h.name AS hospital_name,
    hc.county_name AS hospital_county,

    /* Physical Assessment Details - CORRECTED FIELDS */
    pda.onset_date,
    pda.last_intervention_date,
    pda.cause_of_disability,
    pda.regions_affected,

    /* Impairment Scores with Remarks */
    pda.impairment_score_muscle_power,
    pda.remark_muscle_power,
    pda.impairment_score_joint_motion,
    pda.remark_joint_motion,
    pda.impairment_score_structural_deviation,
    pda.remark_structural_deviation,
    pda.impairment_score_limb_amputation,
    pda.remark_limb_amputation,
    pda.impairment_score_limb_length,
    pda.remark_limb_length,
    pda.impairment_score_balance_coordination,
    pda.remark_balance_coordination,
    pda.impairment_score_other_impairments,
    pda.remark_other_impairments,
    pda.impairment_rating,

    /* Functional Abilities with Remarks */
    pda.function_mobility,
    pda.remark_mobility,
    pda.function_hand_use,
    pda.remark_hand_use,
    pda.function_grip_strength,
    pda.remark_grip_strength,
    pda.function_selfcare,
    pda.remark_selfcare,
    pda.function_daily_life,
    pda.remark_daily_life,
    pda.function_work,
    pda.remark_work,

    pda.disability_rating,
    pda.assistive_products,
    pda.other_services,
    pda.conclusion_decision,
    pda.supporting_document,
    pda.created_at,
    pda.id AS physical_assessment_id

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN officials mo ON a.medical_officer_id = mo.id
LEFT JOIN officials co ON a.county_officer_id = co.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN physical_disability_assessment pda ON pda.assessment_id = a.id
WHERE u.id = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Create unique certificate code
$assessmentId = $data['assessment_id'];
$certPrefix = "MOH276C";
$certHash = strtoupper(substr(md5($assessmentId . $data['id_number']), 0, 6)); // Short hash
$certificateCode = "CERT-$certPrefix-$assessmentId-$certHash";
?>




<style>
    body {
        font-size: 13px;
    }

    .header-logo {
        height: 60px;
    }

    .header-text {
        font-weight: bold;
        text-transform: uppercase;
    }

    .table th,
    .table td {
        padding: 0.3rem;
    }

    .form-control[readonly],
    textarea[readonly] {
        border: none;
        background-color: transparent;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>


<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg sticky no-print"></div>


            <!-- top navigation  -->
            <?php include 'files/nav.php'; ?>


            <!-- navigation -->
            <?php include 'files/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header no-print">
                        <h6>View Report</h6>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                            <div class="breadcrumb-item"> Asessment Report</div>
                        </div>
                    </div>
                    <div class="section-body"></div>
                    <div class="container my-3 ">
                        <div class="text-center ">
                            <img src="../assets/img/Coat_of_arms.png" class="header-logo mb-1 "
                                alt="Kenyan Coat of Arms ">
                            <div class="header-text ">Republic of Kenya</div>
                            <div>Ministry of Health</div>
                            <h6 class="mt-2 "> ASSESSMENT FORM FOR PHYSICAL DISABILITIES
                                <m class="text-danger">(MOH/276A)</m>
                            </h6>
                        </div>

                        <div class="text-right my-2 no-print ">
                            <button class="btn btn-primary btn-md mx-5 " onclick="window.print() ">Print</button>
                            <button class="btn btn-success btn-md " onclick="exportPDF() ">Export PDF</button>
                            <!-- <button class="btn btn-warning btn-sm " onclick="toggleEdit() ">Toggle Edit</button> -->
                        </div>

                        <div class="position-relative mt-3 p-3 mt-2" style="min-height: 120px;">
                            <!-- QR code container: positioned top right -->
                            <div id="qrcode"
                                style="position: absolute; top: 10px; right: 10px; width: 100px; height: 100px;"></div>


                            <!-- Centered text -->
                            <div class="text-center h-100 d-flex flex-column justify-content-center">
                                <p class="mb-1"><strong>Certificate ID:</strong>
                                    <?= $certificateCode ?> | Approved on <?= date('d M Y', strtotime($data['update_time'])) ?>  
                                </p>
                                
                                <small>This document is officially generated from the Ministry of Health Disability
                                    Assessment
                                    System.</small>
                            </div>
                        </div>

                        <form id="assessmentForm ">
                            <h6>1. Health Facility Details</h6>
                            <table class="table table-bordered ">
                                <tr>
                                    <th class="font-weight-bold">Name of Health Facility</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['hospital_name'] ?> ">
                                    </td>
                                    <th>Date</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['assessment_date'] ?> ">
                                    </td>
                                </tr>
                            </table>

                            <h6 class="mt-1">2. Applicant Information for the purpose of reporting on Disability
                                Assessment:</h6>
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Name</th>
                                    <td><input class="form-control " readonly value="<?php echo $data['user_name'] ?> ">
                                    </td>
                                    <th>ID No.</th>
                                    <td><input class="form-control " readonly value="<?php echo $data['id_number'] ?> ">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td><input class="form-control " readonly value="<?php echo $data['gender'] ?> ">
                                    </td>
                                    <th>DOB</th>
                                    <td><input class="form-control " readonly value="<?php echo $data['dob'] ?> "></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['mobile_number'] ?> ">
                                    </td>
                                    <th>County/Subcounty</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['user_county'] ?>/<?php echo $data['user_subcounty'] ?> ">
                                    </td>
                                </tr>
                            </table>

                            <h6 class="mt-1">3. Next of Kin Details:</h6>
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Next of Kin</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['next_of_kin_name'] ?> ">
                                    </td>
                                    <th>Relation</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['next_of_kin_relationship'] ?> "></td>
                                    <th>NOK Phone</th>
                                    <td colspan="3 "><input class="form-control " readonly
                                            value="<?php echo $data['next_of_kin_mobile'] ?> ">
                                    </td>
                                </tr>
                            </table>

                            <h5 class="mt-4">SUMMARY FINDINGS</h5>
                            <table class="table table table-bordered">
                                <tr>
                                    <th style="width: 25%">Date of Injury/Onset of Illness</th>
                                    <td><?php echo htmlspecialchars(@$data['onset_date']); ?></td>
                                    <th style="width: 25%">Date of Last Intervention</th>
                                    <td><?php echo htmlspecialchars(@$data['last_intervention_date']); ?></td>
                                </tr>
                                <tr>
                                    <th>Cause of Disability</th>
                                    <td colspan="3"><?php echo htmlspecialchars(@$data['cause_of_disability']); ?></td>
                                </tr>
                            </table>

                            <h5 class="mt-4">STRUCTURAL IMPAIRMENTS</h5>

                            <h6><b>S8. SKIN AND RELATED STRUCTURES / OTHER BODY STRUCTURES</b></h6>
                            <p class="small"><?php echo nl2br(htmlspecialchars($data['regions_affected'])); ?></p>

                            <h6 class="mt-3">IMPAIRMENT SCORING</h6>
                            <table class="table table-s table-bordere">
                                <thead>
                                    <tr class="small">
                                        <th>Area</th>
                                        <th>Score</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $impairments = [
                                        'impairment_score_muscle_power' => ['label' => 'Muscle Power of affected muscle groups', 'remark' => 'remark_muscle_power'],
                                        'impairment_score_joint_motion' => ['label' => 'Range of motion of joints affected', 'remark' => 'remark_joint_motion'],
                                        'impairment_score_structural_deviation' => ['label' => 'Degree of structural angulation / deviation', 'remark' => 'remark_structural_deviation'],
                                        'impairment_score_limb_amputation' => ['label' => 'Level of limb amputation', 'remark' => 'remark_limb_amputation'],
                                        'impairment_score_limb_length' => ['label' => 'Bilateral lower limb length', 'remark' => 'remark_limb_length'],
                                        'impairment_score_balance_coordination' => ['label' => 'Balance and coordination', 'remark' => 'remark_balance_coordination'],
                                        'impairment_score_other_impairments' => ['label' => 'Other physical impairments', 'remark' => 'remark_other_impairments']
                                    ];
                                    foreach ($impairments as $score_field => $info): ?>
                                        <tr>
                                            <td><?php echo $info['label']; ?></td>
                                            <td><?php echo ucfirst(htmlspecialchars($data[$score_field])); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($data[$info['remark']])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mt-3">Overall Impairment Rating</h6>
                                    <p class="borderr p-2 smalll">
                                        <?php echo nl2br(htmlspecialchars($data['impairment_rating'])); ?>
                                    </p>
                                </div>
                            </div>

                            <h5 class="mt-4">FUNCTION AND PARTICIPATION RESTRICTIONS</h5>
                            <table class="table table table-borderedd text-centeer">
                                <thead>
                                    <tr class="small">
                                        <th>Area</th>
                                        <th>Score</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $functions = [
                                        'function_mobility' => ['label' => 'Mobility', 'remark' => 'remark_mobility'],
                                        'function_hand_use' => ['label' => 'Hand Use', 'remark' => 'remark_hand_use'],
                                        'function_grip_strength' => ['label' => 'Grip Strength', 'remark' => 'remark_grip_strength'],
                                        'function_selfcare' => ['label' => 'Self-Care', 'remark' => 'remark_selfcare'],
                                        'function_daily_life' => ['label' => 'Daily Life Activities', 'remark' => 'remark_daily_life'],
                                        'function_work' => ['label' => 'Work', 'remark' => 'remark_work']
                                    ];
                                    foreach ($functions as $func_field => $info): ?>
                                        <tr>
                                            <td><?php echo $info['label']; ?></td>
                                            <td><?php echo ucwords(str_replace('_', ' ', $data[$func_field] ?? '')); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($data[$info['remark']])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <h6>Overall Disability Rating</h6>
                                <p class="borders p-2 smalll">
                                    <?php echo nl2br(htmlspecialchars($data['disability_rating'])); ?>
                                </p>
                            </div>

                            <h6 class="mt-3">Conclusion</h6>
                            <table class="table table-smm table-borderedd">
                                <tr>
                                    <th>Conclusion Decision</th>
                                    <td><?php echo htmlspecialchars($data['conclusion_decision']); ?></td>
                                </tr>
                                <tr>
                                    <th>Recommended Assistive Product(s)</th>
                                    <td><?php echo htmlspecialchars($data['assistive_products']); ?></td>
                                </tr>
                                <tr>
                                    <th>Other Required Services</th>
                                    <td><?php echo htmlspecialchars($data['other_services']); ?></td>
                                </tr>
                            </table>

                            <h6 class="mt-1">9.Approval </h6>
                            <h6 class="mt-1 small"><b>Review By the Medical Assessment Team</b></h6>
                            <table class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Reg. No.</th>
                                        <th>Assessment Date</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td><?php echo $data['health_officer_name'] ?> (Medical Officer)</td>
                                    <td><?php echo $data['health_license'] ?></td>
                                    <td><?php echo $data['assessment_date'] ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $data['medical_officer_name'] ?> (Medical Officer)</td>
                                    <td><?php echo $data['medical_license'] ?></td>
                                    <td><?php echo $data['assessment_date'] ?></td>
                                </tr>
                            </table>
                            <h6 class="mt-1 small"><b>Approval by the County Health Director</b></h6>
                            <table class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>County</th>
                                        <th>Medical Registration</th>
                                        <th>Approval Date</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td><?php echo $data['county_officer_name'] ?> (County Officer)</td>
                                    <td><?php echo $data['user_county'] ?></td>
                                    <td><?php echo $data['county_license'] ?></td>
                                    <td><?php echo $data['assessment_date'] ?></td>
                                </tr>
                            </table>

                        </form>
                    </div>

                </section>
            </div>
        </div>
    </div>

    <script>
        function exportPDF() {
            const element = document.getElementById('assessmentForm');
            html2pdf().from(element).set({
                filename: 'Physical_Disability_Assessment_Form.pdf',
                margin: 0.5,
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            }).save();
        }

        // function toggleEdit() {
        //     document.querySelectorAll('#assessmentForm .form-control, #assessmentForm textarea').forEach(el => {
        //         if (el.hasAttribute('readonly')) {
        //             el.removeAttribute('readonly');
        //             el.classList.add('border');
        //         } else {
        //             el.setAttribute('readonly', true);
        //             el.classList.remove('border');
        //         }
        //     });
        // }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        const certCode = "<?php echo $certificateCode ?>";
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: certCode,
            width: 100,
            height: 100
        });
    </script>

    <script>
        QRCode.toCanvas(document.getElementById('qrcode'), "<?php echo $certificateCode ?>", function (error) {
            if (error) console.error(error);
        });
    </script>

</body>

</html>