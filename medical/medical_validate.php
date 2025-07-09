<?php include 'files/header.php'; ?>
<?php
$success = null;
$error_message = "";

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Get and sanitize input data for both hearing and physical assessments
  $assessment_id = (int) ($_GET['assessment_id'] ?? 0);
  $status = "checked"; // Status for the assessment
  $medical_officer_id = (int) ($_SESSION['user_id'] ?? 1);

  // File upload handling for both hearing and physical assessments
  $file_uploaded = false;
  $file_path = '';
  if (isset($_FILES['supporting_file']) && $_FILES['supporting_file']['error'] === 0) {
    $file_name = $_FILES['supporting_file']['name'];
    $file_tmp = $_FILES['supporting_file']['tmp_name'];
    $file_size = $_FILES['supporting_file']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
    if (in_array($file_ext, $allowed_exts)) {
      $upload_dir = "../uploads/";
      if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);  // Create the directory if it doesn't exist
      }
      $file_path = $upload_dir . uniqid() . '.' . $file_ext;

      if (move_uploaded_file($file_tmp, $file_path)) {
        $file_uploaded = true;
      } else {
        $error_message = "Error uploading file.";
      }
    } else {
      $error_message = "Invalid file type. Only PDF, JPG, JPEG, PNG files are allowed.";
    }
  }

  // Define a flag to determine which form (hearing or physical) to process
  $is_hearing_assessment = isset($_POST['hearing_disability_conclusion']);  // If hearing assessment fields are set, then it's a hearing assessment.

  // Handle the form submission logic based on the assessment type
  if ($is_hearing_assessment) {
    // Collect hearing assessment data
    $history_of_hearing_loss = $_POST['history_of_hearing_loss'] ?? '';
    $history_of_hearing_devices = $_POST['history_of_hearing_devices'] ?? '';
    $hearing_loss_type_right = $_POST['hearing_loss_type_right'] ?? '';
    $hearing_loss_type_left = $_POST['hearing_loss_type_left'] ?? '';
    $hearing_grade_right = $_POST['hearing_grade_right'] ?? '';
    $hearing_grade_left = $_POST['hearing_grade_left'] ?? '';
    $hearing_level_dbhl_right = $_POST['hearing_level_dbhl_right'] ?? null;
    $hearing_level_dbhl_left = $_POST['hearing_level_dbhl_left'] ?? null;
    $monoaural_percent_right = $_POST['monoaural_percent_right'] ?? null;
    $monoaural_percent_left = $_POST['monoaural_percent_left'] ?? null;
    $binaural_percent = $_POST['binaural_percent'] ?? null;
    $hearing_disability_conclusion = $_POST['hearing_disability_conclusion'] ?? '';
    $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
    $required_services = $_POST['required_services'] ?? '';

    // Set the disability type to "Hearing"
    $disability = "Hearing";

    // Insert hearing disability assessment data into the database
    $sql_hearing = "INSERT INTO hearing_disability_assessments (
            assessment_id,
            history_of_hearing_loss,
            history_of_hearing_devices,
            hearing_loss_type_right,
            hearing_loss_type_left,
            hearing_grade_right,
            hearing_grade_left,
            hearing_level_dbhl_right,
            hearing_level_dbhl_left,
            monoaural_percent_right,
            monoaural_percent_left,
            binaural_percent,
            conclusion,
            recommended_assistive_products,
            required_services
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and execute the statement for hearing assessment
    if ($stmt_hearing = mysqli_prepare($conn, $sql_hearing)) {
      mysqli_stmt_bind_param(
        $stmt_hearing,
        "isssssssddddsss",
        $assessment_id,
        $history_of_hearing_loss,
        $history_of_hearing_devices,
        $hearing_loss_type_right,
        $hearing_loss_type_left,
        $hearing_grade_right,
        $hearing_grade_left,
        $hearing_level_dbhl_right,
        $hearing_level_dbhl_left,
        $monoaural_percent_right,
        $monoaural_percent_left,
        $binaural_percent,
        $hearing_disability_conclusion,
        $recommended_assistive_products,
        $required_services
      );

      if (mysqli_stmt_execute($stmt_hearing)) {
        // Update the assessment status after hearing disability insertion
        $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);

        if ($update_stmt) {
          mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);

          if (mysqli_stmt_execute($update_stmt)) {
            // Insert the uploaded document into the documents table if uploaded
            if ($file_uploaded) {
              $insert_document_sql = "INSERT INTO documents (user_id, assessment_id, file_path, document_type) 
                                                    VALUES (?, ?, ?, ?)";
              $insert_document_stmt = mysqli_prepare($conn, $insert_document_sql);

              if ($insert_document_stmt) {
                $document_type = 'Hearing Assessment File';
                mysqli_stmt_bind_param($insert_document_stmt, "iiss", $pwdUser['id'], $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($insert_document_stmt);
                mysqli_stmt_close($insert_document_stmt);
              }
            }

            echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Hearing assessment details saved.',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = 'complete_assessment';
                                    });
                                });
                            </script>";
            exit;
          } else {
            $error_message = "Failed to update assessment: " . mysqli_stmt_error($update_stmt);
          }
          mysqli_stmt_close($update_stmt);
        } else {
          $error_message = "Failed to prepare update statement: " . mysqli_error($conn);
        }
      } else {
        $error_message = "Failed to save hearing assessment: " . mysqli_stmt_error($stmt_hearing);
      }
      mysqli_stmt_close($stmt_hearing);
    } else {
      $error_message = "Failed to prepare hearing assessment statement: " . mysqli_error($conn);
    }
  } else {
    // Collect physical disability data
    $medical_history = $_POST['medical_history'] ?? '';
    $injury_date = $_POST['injury_date'] ?? null;
    $last_intervention_date = $_POST['last_intervention_date'] ?? null;
    $cause_of_disability = $_POST['cause_of_disability'] ?? '';
    $muscle_power_score = $_POST['muscle_power_score'] ?? '';
    $joint_range_score = $_POST['joint_range_score'] ?? '';
    $angulation_score = $_POST['angulation_score'] ?? '';
    $amputation_score = $_POST['amputation_score'] ?? '';
    $impairments_remark = $_POST['impairments_remark'] ?? '';
    $mobility_score = $_POST['mobility_score'] ?? '';
    $self_care_score = $_POST['self_care_score'] ?? '';
    $restrictions_remark = $_POST['restrictions_remark'] ?? '';
    $disability_rating = $_POST['disability_rating'] ?? '';
    $conclusion_type = $_POST['conclusion_type'] ?? '';
    $assistive_products = $_POST['assistive_products'] ?? '';
    $required_services = $_POST['required_services'] ?? '';

    // Set the disability type to "Physical"
    $disability = "Physical";

    // Insert physical disability assessment data into the database
    $sql_physical = "INSERT INTO physical_disability_assessments (
            assessment_id,
            medical_history,
            injury_date,
            last_intervention_date,
            cause_of_disability,
            muscle_power_score,
            joint_range_score,
            angulation_score,
            amputation_score,
            impairments_remark,
            mobility_score,
            self_care_score,
            restrictions_remark,
            disability_rating,
            conclusion_type,
            assistive_products,
            required_services
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and execute the statement for physical disability
    if ($stmt_physical = mysqli_prepare($conn, $sql_physical)) {
      mysqli_stmt_bind_param(
        $stmt_physical,
        "sssssssssssssssss",
        $assessment_id,
        $medical_history,
        $injury_date,
        $last_intervention_date,
        $cause_of_disability,
        $muscle_power_score,
        $joint_range_score,
        $angulation_score,
        $amputation_score,
        $impairments_remark,
        $mobility_score,
        $self_care_score,
        $restrictions_remark,
        $disability_rating,
        $conclusion_type,
        $assistive_products,
        $required_services
      );

      if (mysqli_stmt_execute($stmt_physical)) {
        // Update the assessment status after physical disability insertion
        $update_sql = "UPDATE assessments SET disability_type = ?, medical_officer_id = ?, status = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);

        if ($update_stmt) {
          mysqli_stmt_bind_param($update_stmt, "sisi", $disability, $medical_officer_id, $status, $assessment_id);

          if (mysqli_stmt_execute($update_stmt)) {
            // Insert the uploaded document into the documents table if uploaded
            if ($file_uploaded) {
              $insert_document_sql = "INSERT INTO documents (user_id, assessment_id, file_path, document_type) 
                                                    VALUES (?, ?, ?, ?)";
              $insert_document_stmt = mysqli_prepare($conn, $insert_document_sql);

              if ($insert_document_stmt) {
                $document_type = 'Physical Assessment File';
                mysqli_stmt_bind_param($insert_document_stmt, "iiss", $pwdUser['id'], $assessment_id, $file_path, $document_type);
                mysqli_stmt_execute($insert_document_stmt);
                mysqli_stmt_close($insert_document_stmt);
              }
            }

            echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Physical assessment details saved.',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = 'complete_assessment';
                                    });
                                });
                            </script>";
            exit;
          } else {
            $error_message = "Failed to update assessment: " . mysqli_stmt_error($update_stmt);
          }
          mysqli_stmt_close($update_stmt);
        } else {
          $error_message = "Failed to prepare update statement: " . mysqli_error($conn);
        }
      } else {
        $error_message = "Failed to save physical assessment: " . mysqli_stmt_error($stmt_physical);
      }
      mysqli_stmt_close($stmt_physical);
    } else {
      $error_message = "Failed to prepare physical assessment statement: " . mysqli_error($conn);
    }
  }

  // Show error if any
  if (!empty($error_message)) {
    echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '" . addslashes($error_message) . "',
                    confirmButtonText: 'OK'
                });
            </script>";
  }

  mysqli_close($conn);
}
?>


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
            <h1>Disability Assessment Form</h1>
          </div>
          <div class="row">

            <div class="container">
              <h3 class="mb-4"><u>Select Disability Type</u></h3>
              <div class="mb-3">
                <label for="disabilityType">Disability Type</label>
                <select id="disabilityType" class="form-control" required>
                  <option value="">-- Select --</option>
                  <option value="Physical Disabilities">Physical Disabilities</option>
                  <option value="Hearing Impairments">Hearing Impairments</option>
                  <!-- <option value="Visual Impairments">Visual Impairments</option>
                  <option value="Multiple Disabilities">Multiple Disabilities</option>
                  <option value="Mental/Intellectual">Mental/Intellectual Disabilities</option>
                  <option value="Progressive Chronic Disorders">Progressive Chronic Disorders</option>
                  <option value="Speech Disabilities">Speech Disabilities</option> -->
                </select>
              </div>
              <div class="hr"></div>
              <!-- Physical Disabilities Form -->
              <div id="Physical Disabilities" class="disability-form card">
                 <?php include('physical_form.php');?>
              </div>

              <!-- Multiple Disabilities Form -->
              <div id="Multiple Disabilities" class="disability-form card">
                <h4>Multiple Disability Assessment</h4>
                <form action="submit_multiple_assessment.php" method="post">
                  <div class="mb-3">
                    <label>Conditions Combined</label>
                    <textarea name="combined_conditions" class="form-control"></textarea>
                  </div>
                  <div class="mb-3">
                    <label>Severity</label>
                    <input type="text" name="severity" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>

              <!-- Mental/Intellectual Disabilities Form -->
              <div id="Mental/Intellectual" class="disability-form card">
                <h4>Mental/Intellectual Disability Assessment</h4>
                <form action="submit_mental_assessment.php" method="post">
                  <div class="mb-3">
                    <label>Cognitive Level</label>
                    <input type="text" name="cognitive_level" class="form-control">
                  </div>
                  <div class="mb-3">
                    <label>Behavioral Notes</label>
                    <textarea name="behavioral_notes" class="form-control"></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>

              <!-- Visual Impairments Form -->
              <div id="Visual Impairments" class="disability-form card">
                 <?php include('visual_form.php');?>
              </div>

              <!-- Hearing Impairments Form -->
              <div id="Hearing Impairments" class="disability-form card">
                <!-- <h5>Hearing Impairment Assessment</h5> -->
                <?php include 'hearing_form.php'; ?>
              </div>

              <!-- Progressive Chronic Disorders Form -->
              <div id="Progressive Chronic Disorders" class="disability-form card">
                <h4>Progressive Chronic Disorder Assessment</h4>
                <form action="submit_chronic_assessment.php" method="post">
                  <div class="mb-3">
                    <label>Diagnosis</label>
                    <input type="text" name="diagnosis" class="form-control">
                  </div>
                  <div class="mb-3">
                    <label>Stage</label>
                    <input type="text" name="stage" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>

              <!-- Speech Disabilities Form -->
              <div id="Speech Disabilities" class="disability-form card">
                <h4>Speech Disability Assessment</h4>
                <form action="submit_speech_assessment.php" method="post">
                  <div class="mb-3">
                    <label>Speech Clarity Level</label>
                    <input type="text" name="speech_clarity" class="form-control">
                  </div>
                  <div class="mb-3">
                    <label>Communication Method</label>
                    <input type="text" name="communication_method" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>

            </div>

          </div>

        </section>
      </div>

      <script>
        const select = document.getElementById("disabilityType");
        const forms = document.querySelectorAll(".disability-form");

        function showSelectedForm(value) {
          // Hide all forms first
          forms.forEach(form => form.style.display = "none");

          // Show only if a valid selection
          if (value && document.getElementById(value)) {
            document.getElementById(value).style.display = "block";
          }
        }

        // On change of select dropdown
        select.addEventListener("change", function () {
          const selectedValue = this.value;
          localStorage.setItem("selectedDisability", selectedValue); // Save selection
          showSelectedForm(selectedValue);
        });

        // On page load: hide all and load from localStorage if available
        window.addEventListener("DOMContentLoaded", () => {
          const stored = localStorage.getItem("selectedDisability") || "";
          select.value = stored;
          showSelectedForm(stored);
        });
      </script>


      <!-- Bootstrap JS -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const leftInput = document.querySelector('[name="monoaural_percent_left"]');
          const rightInput = document.querySelector('[name="monoaural_percent_right"]');
          const binauralInput = document.querySelector('[name="binaural_percent"]');

          function calculateBinaural() {
            const left = parseFloat(leftInput.value);
            const right = parseFloat(rightInput.value);

            if (!isNaN(left) && !isNaN(right)) {
              const binaural = left < right
                ? (5 * left + right) / 6
                : (5 * right + left) / 6;

              binauralInput.value = binaural.toFixed(2);
            } else {
              binauralInput.value = '';
            }
          }

          leftInput.addEventListener('input', calculateBinaural);
          rightInput.addEventListener('input', calculateBinaural);
        });
      </script>


      <?php include 'files/footer.php'; ?>