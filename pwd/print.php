<?php
include 'files/header.php';
// include 'files/nav.php';
// include 'files/sidebar.php';

@$user_id = intval($pwdUser['id']);

// Updated SQL with JOINs to counties table
$sql = "SELECT 
    u.name AS user_name,  a.id AS assessment_id, u.gender, u.dob, u.marital_status, u.id_number, u.occupation,
    u.mobile_number, u.email, u.next_of_kin_name, u.next_of_kin_mobile, u.next_of_kin_relationship,
    uc.county_name AS user_county, u.subcounty AS user_subcounty,
    a.assessment_date,

    -- Medical Officer
    mo.name AS medical_officer_name, mo.license_id AS medical_license, mo.email AS medical_email,

    -- County Officer
    co.name AS county_officer_name, co.license_id AS county_license, co.email AS county_email,

    -- Health Officer
    ho.name AS health_officer_name, ho.license_id AS health_license, ho.email AS health_email,

    -- Hospital Details
    h.name AS hospital_name, hc.county_name AS hospital_county,

    -- Hearing Assessment Details
    hda.history_of_hearing_loss, hda.history_of_hearing_devices,
    hda.hearing_test_type_right, hda.hearing_test_type_left,
    hda.hearing_loss_degree_right, hda.hearing_loss_degree_left,
    hda.hearing_level_dbhl_right, hda.hearing_level_dbhl_left,
    hda.monaural_percentage_right, hda.monaural_percentage_left,
    hda.overall_binaural_percentage, hda.conclusion,
    hda.recommended_assistive_products, hda.required_services

FROM users u
JOIN assessments a ON a.user_id = u.id
LEFT JOIN officials mo ON a.medical_officer_id = mo.id
LEFT JOIN officials co ON a.county_officer_id = co.id
LEFT JOIN officials ho ON a.health_officer_id = ho.id
LEFT JOIN hospitals h ON a.hospital_id = h.id
LEFT JOIN counties uc ON u.county_id = uc.id
LEFT JOIN counties hc ON h.county_id = hc.id
LEFT JOIN hearing_disability_assessments hda ON hda.assessment_id = a.id
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
                            <h6 class="mt-2 ">ASSESSMENT FORM FOR HEARING IMPAIRMENTS (MOH 276C)</h6>
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
                                    <th>NOK Phone</th>
                                    <td colspan="3 "><input class="form-control " readonly
                                            value="<?= $data['next_of_kin_mobile'] ?> ">
                                    </td>
                                </tr>

                            </table>


                            <h6 class="mt-1">4. Hearing History</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>History of Hearing Loss</th>
                                    <td><textarea class="form-control "
                                            readonly><?= $data['history_of_hearing_loss'] ?></textarea></td>
                                </tr>
                                <tr>
                                    <th>Hearing Device Usage</th>
                                    <td><textarea class="form-control "
                                            readonly><?= $data['history_of_hearing_devices'] ?></textarea>
                                    </td>
                                </tr>
                            </table>

                            <h6 class="mt-1">5. Hearing Test Results</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Right Ear</th>
                                        <th>Left Ear</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <th>Type of Hearing Loss</th>
                                    <td><?= $data['hearing_test_type_right'] ?></td>
                                    <td><?= $data['hearing_test_type_left'] ?></td>
                                </tr>
                                <tr>
                                    <th>Degree</th>
                                    <td><?= $data['hearing_loss_degree_right'] ?></td>
                                    <td><?= $data['hearing_loss_degree_left'] ?></td>
                                </tr>
                            </table>

                            <h6 class="mt-1">6. Disability Calculation</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Right dBHL</th>
                                    <td><?= $data['hearing_level_dbhl_right'] ?></td>
                                    <th>%</th>
                                    <td><?= $data['monaural_percentage_right'] ?>%</td>
                                </tr>
                                <tr>
                                    <th>Left dBHL</th>
                                    <td><?= $data['hearing_level_dbhl_left'] ?></td>
                                    <th>%</th>
                                    <td><?= $data['monaural_percentage_left'] ?>%</td>
                                </tr>
                                <tr>
                                    <th colspan="3 ">Overall Binaural %</th>
                                    <td><?= $data['overall_binaural_percentage'] ?>%</td>
                                </tr>
                            </table>

                            <h6 class="mt-1">7. Conclusion</h6>
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Conclusion</th>
                                    <td><?= $data['conclusion'] ?></td>
                                </tr>
                            </table>

                            <h6 class="mt-1">8. Recommendations</h6>
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Assistive Products</th>
                                    <td><?= $data['recommended_assistive_products'] ?></td>
                                </tr>
                                <tr>
                                    <th>Required Services</th>
                                    <td><?= $data['required_services'] ?></td>
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
</body>

</html>