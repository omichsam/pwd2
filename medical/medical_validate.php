<?php include 'files/header.php'; ?>


<body>

  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>


    <?php

$success = null;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Step 1: Get and validate fields
    $assessment_id =  $_GET["assessment_id"];
    $disability_type =  "Hearing";
    $medical_officer_id = $pwdUser['id'] ?? 11;
    $status = 'checked';

    // Step 2: Check if all required fields are present
    if ($assessment_id <= 0 || empty($disability_type) || $medical_officer_id <= 0) {
        $error_message = "Missing required fields. Please ensure assessment ID and disability type are provided.";
    } else {
        // Step 3: Prepare and execute update query
        $sql = "UPDATE assessments 
                SET disability_type = ?, status = ?, medical_officer_id = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssii", $disability_type, $status, $medical_officer_id, $assessment_id);
            if (mysqli_stmt_execute($stmt)) {
                // Step 4: Check if row was actually updated
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Assessment Updated!',
                                text: 'The assessment was successfully updated.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'complete_assessment';
                            });
                        });
                    </script>";
                } else {
                    $error_message = "No changes were made. Record may already be up-to-date or not found.";
                }
            } else {
                $error_message = "Execution failed: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "SQL prepare failed: " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }

    // Step 5: Display any error message using SweetAlert
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
                  <option value="Physical Disabilities">Physical Disabilities</option>
                  <option value="Hearing Impairments">Hearing Impairments</option>
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