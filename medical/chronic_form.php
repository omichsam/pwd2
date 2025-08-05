<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4 mb-4">
  <h6 class="mb-4 text-center fw-bold"> Progressive Chronic Disorders Assessments</h6>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="accordion" id="disabilityAssessmentAccordion">

      <!-- 1. Summary Findings -->
      <div class="accordion-item"> #collapseOne
        <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" id="headingOne">
            <h4 class=""> 1. Summary Findings</h4>
        </div>
        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#disabilityAssessmentAccordion">
          <div class="accordion-body p-2">
            <input type="hidden" name="structural_disability" value="structural_disability">
            <input type="hidden" name="assessment_id" value="<?php echo $_GET['assessment_id'] ?? '' ?>">
            <input type="hidden" name="disability_type" value="chronic">
            <input type="hidden" name="user_id" value="<?php echo $pwdUser['id'] ?? '' ?>">

            <div class="mb-2">
              <label class="form-label badge badge-success  text-wrap">Medical History (Brief)</label>
              <!-- <textarea name="medical_history" class="form-control form-control-sm" rows="2"></textarea> -->
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Date of Injury/Onset of Illness</label>
                <input type="date" name="onset_date" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Date of Last Intervention</label>
                <input type="date" name="last_intervention_date" class="form-control">
              </div>
            </div>

            <!-- --- -->
             <div class="mb-3">
              <label class="form-label">List Past and Ongoing Interventions</label>
              <textarea class="form-control" name="interventions" rows="2"></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label">Cause of Disability</label>
              <input type="text" class="form-control" name="cause_of_disability">
            </div>
             <!-- ----- -->
          </div>
        </div>
      </div>

      <!-- 2. Structural / Clinical Assessment -->
      <div class="accordion-item">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" id="headingTwo">
             <h4>2. Structural / Clinical Assessment</h4>
        </div>
        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#disabilityAssessmentAccordion">
          <div class="accordion-body">
             <div class="mb-3">
              <label class="form-label">Structural Impairments</label>
              <textarea class="form-control" name="structural_impairments" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Region(s) Affected</label>
              <textarea class="form-control" name="regions_affected" rows="2"></textarea>
            </div>

            <div class="row mb-4">
              <?php
                  $areas = [
                      'cardiovascular'   => 'Cardiopulmonary / Cardiovascular',
                      'respiratory'      => 'Respiratory',
                      'cancer'           => 'Malignancies / Cancer',
                      'musculoskeletal'  => 'Musculoskeletal',
                      'neurological'     => 'Neurological',
                      'gastrointestinal' => 'Gastrointestinal disorders',
                      'dermatological'   => 'Dermatological',
                      'hematologic'      => 'Hematologic system',
                      'lymphatic'        => 'Vascular conditions',
                      'genitourinary'    => 'Genito-urinary',
                      'frailty'          => 'Frailty',
                      'other'            => 'Other',
                  ];
              foreach ($areas as $key => $label): ?>
                <div class="col-md-6 mb-3">
                  <label class="form-label"><?php echo $label; ?></label>
                  <select name="score_<?php echo $key; ?>" class="form-select">
                    <option value="">Select</option>
                    <option value="none">No Impairment</option>
                    <option value="mild">Mild Impairment</option>
                    <option value="moderate">Moderate Impairment</option>
                    <option value="severe">Severe Impairment</option>
                    <option value="complete">Complete Impairment</option>
                  </select>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="mb-3">
              <label class="form-label">Findings (Clinical)</label>
              <textarea class="form-control" name="findings_clinical" rows="2"></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label">Remarks (Clinical)</label>
              <textarea class="form-control" name="remarks_clinical" rows="2"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Functional / Participation -->
    <div class="accordion-item">
        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" id="headingThree">
                    <h4>3. Functional / Participation Restrictions</h4>
            </div>
          <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#disabilityAssessmentAccordion">
            <div class="accordion-body">
              <div class="row mb-4">
                  <?php
                      $functions = [
                          'mobility'  => 'Mobility',
                          'selfcare'  => 'Self-care',
                          'domestic'  => 'Domestic life',
                          'majorlife' => 'Major life areas',
                          'community' => 'Community, social, civic life',
                      ];
                  foreach ($functions as $key => $label): ?>
                    <div class="col-md-6 mb-3">
                      <label class="form-label"><?php echo $label; ?></label>
                      <select name="difficulty_<?php echo $key; ?>" class="form-select functional-rating">
                        <option value="">Select</option>
                        <option value="no_difficulty">No Difficulty</option>
                        <option value="mild">Mild</option>
                        <option value="moderate">Moderate</option>
                        <option value="severe">Severe</option>
                        <option value="complete">Complete</option>
                      </select>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="mb-3">
                  <label class="form-label">Findings (Functional)</label>
                  <textarea class="form-control" name="findings_functional" rows="2"></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label">Remarks (Functional)</label>
                  <textarea class="form-control" name="remarks_functional" rows="2"></textarea>
                </div>

                <label class="badge bg-success text-wrap mb-2 text-light">Total Disability Rating</label>
                <!-- <h6 class="text-success">Total Disability Rating</h6> -->
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label>None</label>
                    <input type="text" class="form-control" id="rating_none" name="rating_none" readonly>
                  </div>
                  <div class="col-md-2">
                    <label>Mild</label>
                    <input type="text" class="form-control" id="rating_mild" name="rating_mild" readonly>
                  </div>
                  <div class="col-md-2">
                    <label>Moderate</label>
                    <input type="text" class="form-control" id="rating_moderate" name="rating_moderate" readonly>
                  </div>
                  <div class="col-md-2">
                    <label>Severe</label>
                    <input type="text" class="form-control" id="rating_severe" name="rating_severe" readonly>
                  </div>
                  <div class="col-md-2">
                    <label>Complete</label>
                    <input type="text" class="form-control" id="rating_complete" name="rating_complete" readonly>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Conclusion</label>
                    <select class="form-select" name="conclusion_duration">
                      <option value="">Select</option>
                      <option value="Temporary">Temporary</option>
                      <option value="Permanent">Permanent</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Upload Supporting Document</label>
                    <input type="file" name="supporting_document" class="form-control">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Recommended Assistive Products</label>
                  <input type="text" class="form-control" name="recommended_assistive_products">
                </div>

                <div class="mb-4">
                  <label class="form-label">Other Services Required</label>
                  <input type="text" class="form-control" name="other_services_required">
                </div>
            </div>
          </div>
        </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const selects = document.querySelectorAll('.functional-rating');
                    const counters = {
                    none: 0,
                    mild: 0,
                    moderate: 0,
                    severe: 0,
                    complete: 0
                    };

                    function updateCounts() {
                    // Reset
                    for (let key in counters) counters[key] = 0;

                    // Count selected values
                    selects.forEach(select => {
                        const value = select.value;
                        if (value === 'no_difficulty') counters.none++;
                        else if (value && counters[value] !== undefined) counters[value]++;
                    });

                    // Update inputs
                    document.getElementById('rating_none').value = counters.none;
                    document.getElementById('rating_mild').value = counters.mild;
                    document.getElementById('rating_moderate').value = counters.moderate;
                    document.getElementById('rating_severe').value = counters.severe;
                    document.getElementById('rating_complete').value = counters.complete;
                    }

                    // Listen to all dropdown changes
                    selects.forEach(select => {
                    select.addEventListener('change', updateCounts);
                    });

                    updateCounts(); // initial count
                });
                </script>

        </div>
    <div class="text-center mt-4 mb-3 text-left">
      <button type="submit" class="btn btn-primary px-4 py-2 text-left">Submit Assessment</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
