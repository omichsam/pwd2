<?php
include '../files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

@$user_id = intval($_GET['user_id']);

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

    -- Medical Officer
    mo.name AS medical_officer_name, 
    mo.license_id AS medical_license, 
    mo.email AS medical_email,

    -- County Officer
    co.name AS county_officer_name, 
    co.license_id AS county_license, 
    co.email AS county_email,

    -- Health Officer
    ho.name AS health_officer_name, 
    ho.license_id AS health_license, 
    ho.email AS health_email,

    -- Hospital Details
    h.name AS hospital_name, 
    hc.county_name AS hospital_county,

    -- Progressive Chronic Disability Assessment
    pad.onset_date, 
    pad.last_intervention_date, 
    pad.interventions, 
    pad.cause_of_disability, 
    pad.structural_impairments, 
    pad.regions_affected,

    pad.score_cardiovascular, 
    pad.score_respiratory, 
    pad.score_cancer, 
    pad.score_musculoskeletal, 
    pad.score_neurological, 
    pad.score_gastrointestinal, 
    pad.score_dermatological, 
    pad.score_hematologic, 
    pad.score_lymphatic, 
    pad.score_genitourinary, 
    pad.score_frailty, 
    pad.score_other,

    pad.findings_clinical, 
    pad.remarks_clinical,

    pad.difficulty_mobility, 
    pad.difficulty_selfcare, 
    pad.difficulty_domestic, 
    pad.difficulty_majorlife, 
    pad.difficulty_community, 

    pad.findings_functional, 
    pad.remarks_functional,

    pad.rating_none, 
    pad.rating_mild, 
    pad.rating_moderate, 
    pad.rating_severe, 
    pad.rating_complete,

    pad.conclusion_duration, 
    pad.recommended_assistive_products, 
    pad.other_services_required, 
    pad.supporting_document,
    pad.created_at

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN officials mo ON a.medical_officer_id = mo.id
LEFT JOIN officials co ON a.county_officer_id = co.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN progressive_assessment_details pad ON pad.assessment_id = a.id
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
            <?php include '../files/nav.php'; ?>


            <!-- navigation -->
            <?php include '../files/sidebar.php'; ?>

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
                            <h6 class="mt-2 ">ASSESSMENT FORM FOR PROGRESSIVE CHRONIC DISORDERS(MOH/276G)</h6>
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
                                    <?= $certificateCode ?> | Issued on <?= date('d M Y') ?>
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
                                    <td><input class="form-control " readonly value="<?= $data['hospital_name'] ?> ">
                                    </td>
                                    <th>Date</th>
                                    <td><input class="form-control " readonly value="<?= $data['assessment_date'] ?> ">
                                    </td>
                                </tr>
                            </table>

                            <h6 class="mt-1">2. Applicant Information for the purpose of reporting on Disability
                                Assessment:</h6>
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Name</th>
                                    <td><input class="form-control " readonly value="<?= $data['user_name'] ?> "></td>
                                    <th>ID No.</th>
                                    <td><input class="form-control " readonly value="<?= $data['id_number'] ?> "></td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td><input class="form-control " readonly value="<?= $data['gender'] ?> "></td>
                                    <th>DOB</th>
                                    <td><input class="form-control " readonly value="<?= $data['dob'] ?> "></td>
                                </tr>
                                <tr>
                                    <!-- <th>Occupation</th>
                                    <td><input class="form-control " readonly value="< ?= $data['occupation'] ?> "></td> -->
                                    <th>Phone</th>
                                    <td><input class="form-control " readonly value="<?= $data['mobile_number'] ?> ">
                                    </td>
                                    <th>County/Subcounty</th>
                                    <td><input class="form-control " readonly
                                            value="<?= $data['user_county'] ?>/<?= $data['user_subcounty'] ?> "></td>
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
                                    <td><input class="form-control " readonly value="<?= $data['next_of_kin_name'] ?> ">
                                    </td>
                                    <th>Relation</th>
                                    <td><input class="form-control " readonly
                                            value="<?= $data['next_of_kin_relationship'] ?> "></td>
                                    <th>Phone No.</th>
                                    <td colspan="3 "><input class="form-control " readonly
                                            value="<?= $data['next_of_kin_mobile'] ?> ">
                                    </td>
                                </tr>

                            </table>


                            <h6 class="mt-4">4. Summary Findings</h6>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="table-secondarys">Medical History (brief)</th>
                                        <td><?php echo htmlspecialchars(@$data['medical_history']); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="table-secondarys">Date of Injury/Onset of Illness</th>
                                        <td><?php echo htmlspecialchars($data['onset_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="table-secondarys">Date of Last Intervention</th>
                                        <td><?php echo htmlspecialchars($data['last_intervention_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="table-secondarys">List Past and Ongoing Interventions</th>
                                        <td><?php echo nl2br(htmlspecialchars($data['interventions'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="table-secondarys">Cause of Disability</th>
                                        <td><?php echo htmlspecialchars($data['cause_of_disability']); ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mt-4">5. Structural Impairments</h6>
                            <p class="border p-2" style="min-height: 60px;">
                                <?php echo nl2br(htmlspecialchars($data['structural_impairments'])); ?>
                            </p>

                            <h6 class="mt-3">6. Region(s) Affected</h6>
                            <p class="border p-2" style="min-height: 60px;">
                                <?php echo nl2br(htmlspecialchars($data['regions_affected'])); ?>
                            </p>

                            <h6 class="mt-4">7. Assessment</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered text-centerr align-middle">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="width: 30%;">Assessment Area</th>
                                            <th style="width: 70%;">Score for Nature of Impairments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Cardiopulmonary / Cardiovascular</td>
                                            <td><?php echo htmlspecialchars($data['score_cardiovascular']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Respiratory</td>
                                            <td><?php echo htmlspecialchars($data['score_respiratory']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Malignancies / Cancer</td>
                                            <td><?php echo htmlspecialchars($data['score_cancer']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Musculoskeletal</td>
                                            <td><?php echo htmlspecialchars($data['score_musculoskeletal']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Neurological</td>
                                            <td><?php echo htmlspecialchars($data['score_neurological']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Gastro-Intestinal disorders</td>
                                            <td><?php echo htmlspecialchars($data['score_gastrointestinal']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Dermatological</td>
                                            <td><?php echo htmlspecialchars($data['score_dermatological']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Hematologic system</td>
                                            <td><?php echo htmlspecialchars($data['score_hematologic']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Vascular conditions</td>
                                            <td><?php echo htmlspecialchars($data['score_lymphatic']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Genito-urinary</td>
                                            <td><?php echo htmlspecialchars($data['score_genitourinary']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Frailty</td>
                                            <td><?php echo htmlspecialchars($data['score_frailty']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Other</td>
                                            <td><?php echo htmlspecialchars($data['score_other']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Findings / Diagnostic Tests</h6>
                            <p class="border p-2" style="min-height: 100px;">
                                <?php echo nl2br(htmlspecialchars($data['findings_clinical'])); ?>
                            </p>

                            <h6>Remarks</h6>
                            <p class="border p-2" style="min-height: 80px;">
                                <?php echo nl2br(htmlspecialchars($data['remarks_clinical'])); ?>
                            </p>

                            <!-- 8. Function and Participation Restrictions -->
                            <h6 class="mt-4">8. Function and Participation Restrictions</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-secondary text-center">
                                        <tr>
                                            <th>AREA</th>
                                            <th>Score for Nature of Difficulty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Mobility</td>
                                            <td><?php echo htmlspecialchars($data['difficulty_mobility']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Self-care</td>
                                            <td><?php echo htmlspecialchars($data['difficulty_selfcare']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Domestic life</td>
                                            <td><?php echo htmlspecialchars($data['difficulty_domestic']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Major life areas</td>
                                            <td><?php echo htmlspecialchars($data['difficulty_majorlife']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Community, social, civic life</td>
                                            <td><?php echo htmlspecialchars($data['difficulty_community']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Functional Findings and Remarks -->
                            <h6>Findings (Functional)</h6>
                            <p class="border p-2" style="min-height: 60px;">
                                <?php echo nl2br(htmlspecialchars($data['findings_functional'])); ?>
                            </p>
                            <h6>Remarks (Functional)</h6>
                            <p class="border p-2" style="min-height: 60px;">
                                <?php echo nl2br(htmlspecialchars($data['remarks_functional'])); ?>
                            </p>

                            <!-- Disability Rating -->
                            <h6 class="mt-4">Disability Rating</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>No disability</th>
                                            <th>Mild</th>
                                            <th>Moderate</th>
                                            <th>Severe</th>
                                            <th>Complete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['rating_none']); ?></td>
                                            <td><?php echo htmlspecialchars($data['rating_mild']); ?></td>
                                            <td><?php echo htmlspecialchars($data['rating_moderate']); ?></td>
                                            <td><?php echo htmlspecialchars($data['rating_severe']); ?></td>
                                            <td><?php echo htmlspecialchars($data['rating_complete']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Conclusion -->
                            <h6 class="mt-4">Conclusion</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status (Temporary or Permanent)</label>
                                    <input type="text" class="form-control" id="status" name="status"
                                        value="<?php echo htmlspecialchars($data['conclusion_duration']); ?>" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="assistive_products" class="form-label">Recommended Assistive
                                    Product(s)</label>
                                <input type="text" class="form-control" id="assistive_products"
                                    name="assistive_products"
                                    value="<?php echo htmlspecialchars($data['recommended_assistive_products']); ?>" />
                            </div>
                            <div class="mb-4">
                                <label for="services_required" class="form-label">Other Services Required</label>
                                <input type="text" class="form-control" id="services_required" name="services_required"
                                    value="<?php echo htmlspecialchars($data['other_services_required']); ?>" />
                            </div>



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
                                    <td><?= $data['health_officer_name'] ?> (Medical Officer)</td>
                                    <td><?= $data['health_license'] ?></td>
                                    <td><?= $data['assessment_date'] ?></td>
                                </tr>
                                <tr>
                                    <td><?= $data['medical_officer_name'] ?> (Medical Officer)</td>
                                    <td><?= $data['medical_license'] ?></td>
                                    <td><?= $data['assessment_date'] ?></td>
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
                                    <td><?= $data['county_officer_name'] ?> (County Officer)</td>
                                    <td><?= $data['user_county'] ?></td>
                                    <td><?= $data['county_license'] ?></td>
                                    <td><?= $data['assessment_date'] ?></td>
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
        const certCode = "<?= $certificateCode ?>";
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: certCode,
            width: 100,
            height: 100
        });
    </script>


    <script>
        QRCode.toCanvas(document.getElementById('qrcode'), "<?= $certificateCode ?>", function (error) {
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

    <?php include 'files/footer.php'; ?>