<?php include 'files/header.php'; ?>


<body>

  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


      <!-- top navigation  -->
      <?php include 'files/nav.php'; ?>

      <?php
      $success = null;
      $error_message = "";

      // Include DB connection file or establish connection here
// Example: $conn = new mysqli("localhost", "root", "", "pwd");
      
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $assessment_id = $_POST['assessment_id'] ?? null;
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
        $conclusion = $_POST['hearing_disability_conclusion'] ?? '';
        $recommended_assistive_products = $_POST['recommended_assistive_products'] ?? '';
        $required_services = $_POST['required_services'] ?? '';
        $status = "checked";

        // File upload handling
        $file_uploaded = false;
        $file_path = '';

        // Set the upload directory
        $upload_dir = "../uploads/";

        // Check if the upload directory exists, if not, create it
        if (!is_dir($upload_dir)) {
          if (!mkdir($upload_dir, 0755, true)) {
            $error_message = "Failed to create the upload directory.";
          }
        }

        // Handle file upload, check for no file selected
        if (isset($_FILES['supporting_file']) && $_FILES['supporting_file']['error'] === UPLOAD_ERR_OK) {
          $file_name = $_FILES['supporting_file']['name'];
          $file_tmp = $_FILES['supporting_file']['tmp_name'];
          $file_size = $_FILES['supporting_file']['size'];
          $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

          $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];

          // Check file extension
          if (in_array($file_ext, $allowed_exts)) {
            // Generate a unique file path
            $file_path = $upload_dir . uniqid() . '.' . $file_ext;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $file_path)) {
              $file_uploaded = true;
            } else {
              $error_message = "Error uploading file.";
            }
          } else {
            $error_message = "Invalid file type. Only PDF, JPG, JPEG, PNG files are allowed.";
          }
        } elseif ($_FILES['supporting_file']['error'] === UPLOAD_ERR_NO_FILE) {
          // No file uploaded, we will still continue without file
          $file_uploaded = false;
          $file_path = ''; // Ensure no file path is stored
        } else {
          $error_message = "File upload error: " . $_FILES['supporting_file']['error'];
        }

        // Insert hearing disability assessment details into the database
        if (empty($error_message)) {
          // Insert hearing disability assessment details
          $sql = "INSERT INTO hearing_disability_assessments (
            assessment_id,
            history_of_hearing_loss,
            history_of_hearing_devices,
            hearing_loss_type_right,
            hearing_loss_type_left,
            hearing_grade_right,
            hearing_grade_left,
            hearing_level_dbhl_right,
            hearing_level_dbhl_left,
            monaural_percent_right,
            monaural_percent_left,
            binaural_percent,
            conclusion,
            recommended_assistive_products,
            required_services
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

          $stmt = mysqli_prepare($conn, $sql);

          if ($stmt) {
            mysqli_stmt_bind_param(
              $stmt,
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
              $conclusion,
              $recommended_assistive_products,
              $required_services
            );

            if (mysqli_stmt_execute($stmt)) {
              $disability = 'Hearing'; // Set disability type for hearing
      
              // Update assessments table to mark the disability type
              $update_sql = "UPDATE assessments SET disability_type = ?, status = ? WHERE id = ?";
              $update_stmt = mysqli_prepare($conn, $update_sql);

              if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "ssi", $disability, $status, $assessment_id);

                if (mysqli_stmt_execute($update_stmt)) {
                  // If the file was uploaded, insert the document details into the database
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

                  // Success message
                  echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Assessment and hearing details saved.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = 'complete_assessment';
                                });
                            });
                        </script>";
                } else {
                  $error_message = "Failed to update assessment: " . mysqli_stmt_error($update_stmt);
                }
                mysqli_stmt_close($update_stmt);
              } else {
                $error_message = "Failed to prepare update statement: " . mysqli_error($conn);
              }
            } else {
              $error_message = "Failed to save assessment: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
          } else {
            $error_message = "Failed to prepare statement: " . mysqli_error($conn);
          }
        }

        // Show error if any
        if (!empty($error_message)) {
          echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '" . addslashes($error_message) . "',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
        }

        mysqli_close($conn);
      }
      ?>


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
                  <option value="Hearing Impairments">Hearing Impairments</option>
                  <option value="Physical Disabilities">Physical Disabilities</option>
                  <option value="Multiple Disabilities">Multiple Disabilities</option>
                  <option value="Mental/Intellectual">Mental/Intellectual Disabilities</option>
                  <option value="Visual Impairments">Visual Impairments</option>
                  <option value="Progressive Chronic Disorders">Progressive Chronic Disorders</option>
                  <option value="Speech Disabilities">Speech Disabilities</option>
                </select>
              </div>
              <div class="hr"></div>
              <!-- Physical Disabilities Form -->
              <div id="Physical Disabilities" class="disability-form card">
                <h4>Physical Disability Assessment</h4>
                <form action="submit_physical_assessment.php" method="post">
                  <div class="mb-3">
                    <label>Description</label>
                    <textarea name="physical_description" class="form-control"></textarea>
                  </div>
                  <div class="mb-3">
                    <label>Mobility Level</label>
                    <input type="text" name="mobility_level" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
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
                <h4>Visual Impairment Assessment</h4>
                <form action="submit_visual_assessment.php" method="post">
                  <div class="mb-3">
                    <label>Vision Test Summary</label>
                    <textarea name="vision_summary" class="form-control"></textarea>
                  </div>
                  <div class="mb-3">
                    <label>Corrective Measures</label>
                    <input type="text" name="corrective_measures" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
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