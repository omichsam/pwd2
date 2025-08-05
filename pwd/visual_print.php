<?php
include 'files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

@$user_id = intval($pwdUser['id']);

// Updated SQL with JOINs to counties table
$sql = "SELECT
    u.name AS user_name,
    a.id AS assessment_id,
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

    /* Visual Assessment Details */
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
    vad.created_at,
    vad.id AS visual_assessment_id

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN officials mo ON a.medical_officer_id = mo.id
LEFT JOIN officials co ON a.county_officer_id = co.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN visual_assessment_details vad ON vad.assessment_id = a.id
WHERE u.id = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Create unique certificate code
$assessmentId = $data['assessment_id'];
$certPrefix = "MOH276B";
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
                            <div class="breadcrumb-item"> Assessment Report</div>
                        </div>
                    </div>
                    <div class="section-body"></div>
                    <div class="container my-3 ">
                        <div class="text-center ">
                            <img src="../assets/img/Coat_of_arms.png" class="header-logo mb-1 "
                                alt="Kenyan Coat of Arms ">
                            <div class="header-text ">Republic of Kenya</div>
                            <div>Ministry of Health</div>
                            <h6 class="mt-2 "> ASSESSMENT FORM FOR VISUAL IMPAIRMENTS
                                <m class="text-danger">(MOH/276B)</m>
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
                                <!-- <p class="mb-1"><strong>Certificate ID:</strong>
                                    CERT-< ?= strtoupper(substr(md5($data['id_number'] . $data['assessment_date']), 0, 8)) ?>
                                    | Issued on
                                    < ?= date('d M Y') ?>
                                </p> -->

                                <p class="mb-1"><strong>Certificate ID:</strong>
                                    <?php echo $certificateCode ?> | Issued on <?php echo date('d M Y') ?>
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
                                    <!-- <th>Occupation</th>
                                    <td><input class="form-control " readonly value="< ?= $data['occupation'] ?> "></td> -->
                                    <th>Phone</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['mobile_number'] ?> ">
                                    </td>
                                    <th>County/Subcounty</th>
                                    <td><input class="form-control " readonly
                                            value="<?php echo $data['user_county'] ?>/<?php echo $data['user_subcounty'] ?> ">
                                    </td>
                                </tr>
                                <tr>
                                    <!-- <th>County/Subcounty</th>
                                    <td><input class="form-control " readonly
                                            value="< ?= $data['user_county'] ?>/< ?= $data['user_subcounty'] ?> "></td> -->
                                    <!-- <th>Marital Status</th>
                                    <td><input class="form-control " readonly value="< ?= $data['marital_status'] ?> ">
                                    </td> -->
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


                            <!-- <h5 class="mt-4">SUMMARY FINDINGS</h5> -->
                            <div class="container mt-4">
                                <h5 class="mb-3">Visual Disability Assessment</h5>

                                <!-- History Section -->
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label><strong>Assistive Device</strong></label>
                                        <div class="borders p-2">
                                            <?php echo nl2br(htmlspecialchars($data['assistive_device'])); ?>dd
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label><strong>Medical History</strong></label>
                                        <div class="borders p-2">
                                            <?php echo nl2br(htmlspecialchars($data['medical_history'])); ?>ss
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label><strong>Ocular History</strong></label>
                                        <div class="borders p-2">
                                            <?php echo nl2br(htmlspecialchars($data['ocular_history'])); ?> dd
                                        </div>
                                    </div>
                                </div>

                                <!-- Distance Visual Acuity -->
                                <h6 class="mt-3">Distance Visual Acuity</h6>
                                <table class="table table-smm table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Test</th>
                                            <th colspan="2">With Correction</th>
                                            <th colspan="2">Without Correction</th>
                                        </tr>
                                        <tr>
                                            <th>Right</th>
                                            <th>Left</th>
                                            <th>Right</th>
                                            <th>Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Distance</td>
                                            <td><?php echo $data['right_eye_with_correction']; ?></td>
                                            <td><?php echo $data['left_eye_with_correction']; ?></td>
                                            <td><?php echo $data['right_eye_without_correction']; ?></td>
                                            <td><?php echo $data['left_eye_without_correction']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Near Vision</td>
                                            <td colspan="2"><?php echo $data['near_vision_with_correction']; ?></td>
                                            <td colspan="2"><?php echo $data['near_vision_without_correction']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Ophthalmic Examination Combined -->

                                <!-- Ophthalmic Examination -->
                                <h5>Ophthalmic Examination</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center align-middle">
                                        <thead class="table-secondarys">
                                            <tr>
                                                <th colspan="3">Examination</th>
                                                <th colspan="3">Examination</th>
                                            </tr>
                                            <tr>
                                                <th>Examination</th>
                                                <th>Right Eye</th>
                                                <th>Left Eye</th>
                                                <th>Examination</th>
                                                <th>Right Eye</th>
                                                <th>Left Eye</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Present eyeball</td>
                                                <td><?php echo htmlspecialchars($data['present_eyeball_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['present_eyeball_left']); ?></td>
                                                <td>Cornea</td>
                                                <td><?php echo htmlspecialchars($data['cornea_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['cornea_left']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Squint</td>
                                                <td><?php echo htmlspecialchars($data['squint_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['squint_left']); ?></td>
                                                <td>Anterior Chamber</td>
                                                <td><?php echo htmlspecialchars($data['anterior_chamber_right']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($data['anterior_chamber_left']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Nystagmus</td>
                                                <td><?php echo htmlspecialchars($data['nystagmus_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['nystagmus_left']); ?></td>
                                                <td>Iris</td>
                                                <td><?php echo htmlspecialchars($data['iris_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['iris_left']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tearing</td>
                                                <td><?php echo htmlspecialchars($data['tearing_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['tearing_left']); ?></td>
                                                <td>Pupil</td>
                                                <td><?php echo htmlspecialchars($data['pupil_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['pupil_left']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Lids</td>
                                                <td><?php echo htmlspecialchars($data['lids_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['lids_left']); ?></td>
                                                <td>Lens</td>
                                                <td><?php echo htmlspecialchars($data['lens_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['lens_left']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Conjunctiva</td>
                                                <td><?php echo htmlspecialchars($data['conjunctiva_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['conjunctiva_left']); ?></td>
                                                <td>Fundus</td>
                                                <td><?php echo htmlspecialchars($data['fundus_right']); ?></td>
                                                <td><?php echo htmlspecialchars($data['fundus_left']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Specialized Tests -->
                                <h6 class="mt-3">Specialized Tests</h6>
                                <table class="table table-smm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Test</th>
                                            <th>Findings / Defect</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Humphreys Visual Field</td>
                                            <td><?php echo htmlspecialchars($data['hvf']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Colour Vision</td>
                                            <td><?php echo htmlspecialchars($data['colour_vision']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Stereopsis</td>
                                            <td><?php echo htmlspecialchars($data['stereopsis']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Conclusion Section -->
                                <h5>Conclusion</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middlee text-centerr">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th style="width: 20%;">Impairment Category</th>
                                                <th style="width: 10%;">Tick</th>
                                                <th style="width: 70%;" colspan="2">Conclusion Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Normal</td>
                                                <td></td>
                                                <td colspan="3" rowspan="5" class="text-start">
                                                    <strong>Cause of Vision Impairment:</strong><br>
                                                    <?php echo htmlspecialchars($data['cause_of_vision']); ?><br><br>

                                                    <strong>Percentage Disability:</strong><br>
                                                    <?php echo htmlspecialchars($data['percentage_disability']); ?>%<br><br>

                                                    <strong>Possible Intervention:</strong><br>
                                                    <?php echo htmlspecialchars($data['possible_intervention']); ?><br><br>

                                                    <strong>Recommendation:</strong><br>
                                                    <?php echo htmlspecialchars($data['recommendation']); ?><br><br>

                                                    <strong>Conclusion Duration:</strong><br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" disabled <?php if ($data['conclusion_duration'] === 'Temporary')
                                                            echo 'checked'; ?>>
                                                        <label class="form-check-label">Temporary</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" disabled <?php if ($data['conclusion_duration'] === 'Permanent')
                                                            echo 'checked'; ?>>
                                                        <label class="form-check-label">Permanent</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Mild Impairment</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Moderate Impairment</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Severe Impairment</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Blind</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Conclusion Section in Table -->
                                <!-- <h6 class="mt-3">Conclusion</h6>
                                <table class="table table-sm table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Cause of Vision Impairment</th>
                                            <th>Percentage Disability</th>
                                            <th>Possible Intervention</th>
                                            <th>Recommendation</th>
                                            <th>Conclusion Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>< ?php echo htmlspecialchars($data['category']); ?></td>
                                            <td>< ?php echo htmlspecialchars($data['cause_of_vision']); ?></td>
                                            <td>< ?php echo htmlspecialchars($data['percentage_disability']); ?>%</td>
                                            <td>< ?php echo htmlspecialchars($data['possible_intervention']); ?></td>
                                            <td>< ?php echo htmlspecialchars($data['recommendation']); ?></td>
                                            <td>< ?php echo htmlspecialchars($data['conclusion_duration']); ?></td>
                                        </tr>
                                    </tbody>
                                </table> -->
                            </div>




                            <h6 class="mt-1">9.Approval </h6>
                            <h6 class="mt-1 smalll"><b>Review By the Medical Assessment Team</b></h6>
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
                            <h6 class="mt-1 smalll"><b>Approval by the County Health Director</b></h6>
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
                            </tr>



                        </form>
                    </div>

                </section>
            </div>
        </div>
    </div>

    <script>
        // function exportPDF() {
        //     const element = document.getElementById('assessmentForm');
        //     html2pdf().from(element).set({
        //         filename: 'Hearing_Assessment_Form.pdf',
        //         margin: 0.5,
        //         html2canvas: { scale: 2 },
        //         jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        //     }).save();
        // }

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

    <!-- <script>
        const certId = "CERT-< ?= strtoupper(substr(md5($data['id_number'] . $data['assessment_date']), 0, 8)) ?>";
        const issueDate = "Issued on < ?= date('d M Y') ?>";
        const qrText = certId + " | " + issueDate;

        new QRCode(document.getElementById("qrcode"), {
            text: qrText,
            width: 100,
            height: 100,
        });
    </script> -->
</body>

</html>