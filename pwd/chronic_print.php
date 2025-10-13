<?php
include 'files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

// @$user_id = intval($_GET['user_id']);
 $user_id = $pwdUser['id'] ?? null;

// Updated SQL with correct field mappings for chronic_disorder_assessments table
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

    -- Chronic Disorder Assessment - CORRECTED FIELDS
    cda.onset_date, 
    cda.last_intervention_date, 
    cda.interventions, 
    cda.cause_of_disability, 
    cda.structural_impairments, 
    cda.regions_affected,

    -- Clinical Scores with Remarks
    cda.cardiovascular_score, 
    cda.cardiovascular_remark,
    cda.respiratory_score, 
    cda.respiratory_remark,
    cda.cancer_score, 
    cda.cancer_remark,
    cda.musculoskeletal_score, 
    cda.musculoskeletal_remark,
    cda.neurological_score, 
    cda.neurological_remark,
    cda.gastrointestinal_score, 
    cda.gastrointestinal_remark,
    cda.dermatological_score, 
    cda.dermatological_remark,
    cda.hematologic_score, 
    cda.hematologic_remark,
    cda.lymphatic_score, 
    cda.lymphatic_remark,
    cda.genitourinary_score, 
    cda.genitourinary_remark,
    cda.frailty_score, 
    cda.frailty_remark,
    cda.other_score, 
    cda.other_remark,

    -- Functional Difficulties with Remarks
    cda.mobility_difficulty, 
    cda.mobility_remark,
    cda.selfcare_difficulty, 
    cda.selfcare_remark,
    cda.domestic_difficulty, 
    cda.domestic_remark,
    cda.majorlife_difficulty, 
    cda.majorlife_remark,
    cda.community_difficulty, 
    cda.community_remark,

    cda.disability_rating,
    cda.recommended_assistive_products, 
    cda.other_services_required, 
    cda.conclusion_decision,
    cda.supporting_document,
    cda.created_at

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN officials mo ON a.medical_officer_id = mo.id
LEFT JOIN officials co ON a.county_officer_id = co.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN chronic_disorder_assessments cda ON cda.assessment_id = a.id
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
                            <h6 class="mt-2 ">ASSESSMENT FORM FOR PROGRESSIVE CHRONIC DISORDERS(MOH/276G)</h6>
                        </div>

                        <div class="text-right my-2 no-print ">
                            <button class="btn btn-primary btn-md mx-5 " onclick="window.print() ">Print</button>
                            <button class="btn btn-success btn-md " onclick="exportPDF() ">Export PDF</button>
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
                                    Assessment System.</small>
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
                                    <th>Phone</th>
                                    <td><input class="form-control " readonly value="<?= $data['mobile_number'] ?> ">
                                    </td>
                                    <th>County/Subcounty</th>
                                    <td><input class="form-control " readonly
                                            value="<?= $data['user_county'] ?>/<?= $data['user_subcounty'] ?> "></td>
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
                            <table class="table table-bordered table-md">
                                <tbody>
                                    <tr>
                                        <th class="table-secondary" style="width: 25%;">Date of Injury/Onset of Illness
                                        </th>
                                        <td style="width: 25%;">
                                            <?php echo htmlspecialchars($data['onset_date']); ?>
                                        </td>
                                        <th class="table-secondary" style="width: 25%;">Date of Last Intervention</th>
                                        <td style="width: 25%;">
                                            <?php echo htmlspecialchars($data['last_intervention_date']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-secondary" style="width: 25%;">List Past and Ongoing
                                            Interventions</th>
                                        <td style="width: 25%;">
                                            <?php echo nl2br(htmlspecialchars($data['interventions'])); ?>
                                        </td>

                                        <th class="table-secondary" style="width: 25%;">Cause of Disability</th>
                                        <td  style="width: 25%;">
                                            <?php echo htmlspecialchars($data['cause_of_disability']); ?>
                                        </td>
                                    </tr>
                                    <tr>

                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mt-4">5. Structural Impairments</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Structural Impairments</th>
                                    <td>
                                        <?php
                                        echo nl2br(htmlspecialchars($data['structural_impairments']));
                                        ?>
                                    </td>
                                </tr>
                            </table>

                            <h6 class="mt-3">6. Region(s) Affected</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Region(s) Affected</th>
                                    <td>
                                        <?php
                                        echo nl2br(htmlspecialchars($data['regions_affected']));
                                        ?>
                                    </td>
                                </tr>
                            </table>

                            <!-- Structural / Clinical Assessment Table -->
                            <h6 class="mt-4">7. Structural / Clinical Assessment</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="width: 30%;">Assessment Section</th>
                                            <th style="width: 20%;">Score/Finding</th>
                                            <th style="width: 50%;">Clinical Remarks/Findings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $clinical_assessments = [
                                            'cardiovascular_score' => ['label' => 'Cardiopulmonary / Cardiovascular', 'remark' => 'cardiovascular_remark'],
                                            'respiratory_score' => ['label' => 'Respiratory', 'remark' => 'respiratory_remark'],
                                            'cancer_score' => ['label' => 'Malignancies / Cancer', 'remark' => 'cancer_remark'],
                                            'musculoskeletal_score' => ['label' => 'Musculoskeletal', 'remark' => 'musculoskeletal_remark'],
                                            'neurological_score' => ['label' => 'Neurological', 'remark' => 'neurological_remark'],
                                            'gastrointestinal_score' => ['label' => 'Gastro-Intestinal disorders', 'remark' => 'gastrointestinal_remark'],
                                            'dermatological_score' => ['label' => 'Dermatological', 'remark' => 'dermatological_remark'],
                                            'hematologic_score' => ['label' => 'Hematologic system', 'remark' => 'hematologic_remark'],
                                            'lymphatic_score' => ['label' => 'Vascular conditions', 'remark' => 'lymphatic_remark'],
                                            'genitourinary_score' => ['label' => 'Genito-urinary', 'remark' => 'genitourinary_remark'],
                                            'frailty_score' => ['label' => 'Frailty', 'remark' => 'frailty_remark'],
                                            'other_score' => ['label' => 'Other', 'remark' => 'other_remark']
                                        ];

                                        foreach ($clinical_assessments as $score_field => $info):
                                            $score_value = $data[$score_field];
                                            $remark_value = $data[$info['remark']];
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $info['label']; ?></td>
                                                <td>
                                                    <?php if (!empty($score_value)): ?>
                                                        <?php echo htmlspecialchars($score_value); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-left">
                                                    <?php if (!empty($remark_value)): ?>
                                                        <?php echo nl2br(htmlspecialchars($remark_value)); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No remarks</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Functional / Participation Restrictions Table -->
                            <h6 class="mt-4">8. Functional / Participation Restrictions</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-secondary text-center">
                                        <tr>
                                            <th style="width: 30%;">Functional Area</th>
                                            <th style="width: 20%;">Difficulty Level</th>
                                            <th style="width: 50%;">Remarks/Findings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $functional_areas = [
                                            'mobility_difficulty' => ['label' => 'Mobility', 'remark' => 'mobility_remark'],
                                            'selfcare_difficulty' => ['label' => 'Self-care', 'remark' => 'selfcare_remark'],
                                            'domestic_difficulty' => ['label' => 'Domestic life', 'remark' => 'domestic_remark'],
                                            'majorlife_difficulty' => ['label' => 'Major life areas', 'remark' => 'majorlife_remark'],
                                            'community_difficulty' => ['label' => 'Community, social, civic life', 'remark' => 'community_remark']
                                        ];

                                        foreach ($functional_areas as $difficulty_field => $info):
                                            $difficulty_value = $data[$difficulty_field];
                                            $remark_value = $data[$info['remark']];
                                            ?>
                                            <tr>
                                                <td class="text-left"><?php echo $info['label']; ?></td>
                                                <td>
                                                    <?php if (!empty($difficulty_value)): ?>
                                                        <?php echo htmlspecialchars($difficulty_value); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-left">
                                                    <?php if (!empty($remark_value)): ?>
                                                        <?php echo nl2br(htmlspecialchars($remark_value)); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No remarks</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Disability Rating -->
                            <h6 class="mt-4">9. Disability Rating</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Disability Rating</th>
                                    <td>
                                        <?php
                                        if (!empty($data['disability_rating'])) {
                                            echo nl2br(htmlspecialchars($data['disability_rating']));
                                        } else {
                                            echo '<span class="text-muted">No disability rating provided</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>

                            <!-- Conclusion -->
                            <h6 class="mt-4">10. Conclusion and Recommendations</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Conclusion Decision</th>
                                    <td>
                                        <input type="text" class="form-control" id="conclusion" name="conclusion"
                                            value="<?php echo htmlspecialchars($data['conclusion_decision']); ?>"
                                            readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Recommended Assistive Product(s)</th>
                                    <td>
                                        <textarea class="form-control" id="assistive_products" name="assistive_products"
                                            rows="3"
                                            readonly><?php echo htmlspecialchars($data['recommended_assistive_products']); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Other Services Required</th>
                                    <td>
                                        <textarea class="form-control" id="services_required" name="services_required"
                                            rows="3"
                                            readonly><?php echo htmlspecialchars($data['other_services_required']); ?></textarea>
                                    </td>
                                </tr>
                            </table>


                            <h6 class="mt-1">11. Approval </h6>
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
                filename: 'Chronic_Disorder_Assessment_Form.pdf',
                margin: 0.5,
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            }).save();
        }
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

    <?php include 'files/footer.php'; ?>