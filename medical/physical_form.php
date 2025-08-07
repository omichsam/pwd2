<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-4">
  <h5 class="text-center fw-bold mb-3">Physical Disability Assessment</h5>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="accordion" id="accordionPhysical">

      <!-- Summary Findings -->
      <div class="accordion-item">
        <!-- <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
            1. Summary Findings
          </button>
        </h2> -->
        <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" id="headingOne">
                    <h4 class=""> 1. Summary Findings</h4>
        </div>
        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionPhysical">
          <div class="accordion-body py-2 px-3">
            <input type="hidden" name="disability_type" value="physical">
            <input type="hidden" name="assessment_id" value="<?php echo $_GET['assessment_id'] ?? '' ?>">
            <input type="hidden" name="user_id" value="<?php echo $pwdUser['id'] ?? '' ?>">

            <div class="row g-2">
              <h5 class="">Medical History</h5>
              <!-- <div class="col-md-6">
                <label class="form-label small">Medical History</label>
                <textarea name="medical_history" class="form-control form-control-sm" rows="2"></textarea>
              </div> -->
              <div class="col-md-3">
                <label class="form-label small">Injury/Onset</label>
                <input type="date" name="onset_date" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small">Last Intervention</label>
                <input type="date" name="last_intervention_date" class="form-control form-control-sm">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Cause of Disability</label>
                <textarea name="cause_of_disability" class="form-control form-control-sm" rows="2"></textarea>
              </div>
              <!-- <div class="col-md-6">
                <label class="form-label small">Cause of Disability</label>
                <input type="text" name="cause_of_disability" class="form-control form-control-sm">
              </div> -->
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Structural Impairments -->
<div class="accordion-item">

  <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseStructural" aria-expanded="true" id="headingStructural">
        <h4 class=""> 2. Structural Impairments</h4>
    </div>

  <div id="collapseStructural" class="accordion-collapse collapse" data-bs-parent="#disabilityAssessmentAccordion">
    <div class="accordion-body">

      <!-- Region Checkboxes -->
      <label class="fw-bold mb-2">Tick Region(s) Being Assessed</label>
      <div class="row g-2 mb-3">
        <?php
            $regions = [
                's710_head_neck'       => 's710 Head & Neck Region',
                's720_shoulder'        => 's720 Shoulder Region',
                's730_upper_extremity' => 's730 Upper Extremity (arm, hand)',
                's740_pelvis'          => 's740 Pelvis',
                's750_lower_extremity' => 's750 Lower Extremity (leg, foot)',
                's760_trunk'           => 's760 Trunk',
                's8_skin_other'        => 's8 Skin and Related Structures / Other',
            ];
            foreach ($regions as $key => $label) {
                echo '
              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="region_assessed[]" value="' . $key . '" id="chk_' . $key . '">
                  <label class="form-check-label" for="chk_' . $key . '">' . $label . '</label>
                </div>
              </div>';
            }
        ?>
      </div>

      <!-- Region(s) Affected -->
      <div class="mb-3">
        <label class="form-label">Region(s) Affected</label>
        <textarea name="regions_affected" class="form-control form-control-sm" rows="2"></textarea>
      </div>

      <!-- Assessment Areas -->
      <div class="row mb-2">
        <?php
            $areas = [
                'muscle_power'         => 'Muscle Power of Affected Muscle Groups',
                'joint_motion'         => 'Range of Motion of Joints Affected',
                'structural_deviation' => 'Degree of Structural Angulation / Deviation',
                'limb_amputation'      => 'Level of Limb Amputation',
                'limb_length'          => 'Bilateral Lower Limb Length',
                'balance_coordination' => 'Balance and Coordination',
                'other_impairments'    => 'Other Physical Impairments (Specify)',
            ];

            foreach ($areas as $field => $label) {
            ?>
          <div class="col-md-6 mb-2">
            <label class="form-label"><?php echo $label ?></label>
            <select name="impairment_score_<?php echo $field ?>" class="form-select form-select-sm structural-score">
              <option value="">Select Impairment Level</option>
              <option value="none">No Impairment</option>
              <option value="mild">Mild</option>
              <option value="moderate">Moderate</option>
              <option value="severe">Severe</option>
              <option value="complete">Complete</option>
            </select>
          </div>
        <?php }?>
      </div>

      <!-- Findings and Remarks -->
      <div class="mb-2">
        <label class="form-label">Findings (Summarized)</label>
        <textarea name="structural_findings" class="form-control form-control-sm" rows="2"></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label">Remarks</label>
        <textarea name="structural_remarks" class="form-control form-control-sm" rows="2"></textarea>
      </div>

      <!-- Score for Impairments -->
      <div class="border p-3 rounded bg-light mb-3">
        <label class="fw-bold mb-2 d-block">SCORE FOR IMPAIRMENTS</label>
        <div class="row g-2">
          <div class="col-md-2">
            <label class="form-label small">No Impairment</label>
            <input type="text" class="form-control form-control-sm" name="score_none" id="score_none" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Mild</label>
            <input type="text" class="form-control form-control-sm" name="score_mild" id="score_mild" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Moderate</label>
            <input type="text" class="form-control form-control-sm" name="score_moderate" id="score_moderate" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Severe</label>
            <input type="text" class="form-control form-control-sm" name="score_severe" id="score_severe" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Complete</label>
            <input type="text" class="form-control form-control-sm" name="score_complete" id="score_complete" readonly>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- JS Logic to auto-update counts -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const dropdowns = document.querySelectorAll('.structural-score');
  const counters = {
    none: 0,
    mild: 0,
    moderate: 0,
    severe: 0,
    complete: 0
  };

  function updateScores() {
    // Reset counts
    Object.keys(counters).forEach(key => counters[key] = 0);

    dropdowns.forEach(dropdown => {
      const value = dropdown.value;
      if (value && counters.hasOwnProperty(value)) {
        counters[value]++;
      }
    });

    // Update input values
    document.getElementById('score_none').value = counters.none;
    document.getElementById('score_mild').value = counters.mild;
    document.getElementById('score_moderate').value = counters.moderate;
    document.getElementById('score_severe').value = counters.severe;
    document.getElementById('score_complete').value = counters.complete;
  }

  dropdowns.forEach(dropdown => {
    dropdown.addEventListener('change', updateScores);
  });

  updateScores(); // run on load
});
</script>

    <!-- Functional Impairments -->
<div class="accordion-item">
  <!-- <h2 class="accordion-header" id="headingThree">
    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
      3. Functional & Participation
    </button>
  </h2> -->
   <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" id="headingStructural">
        <h4 class=""> 3. Functional & Participation </h4>
    </div>
  <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionPhysical">
    <div class="accordion-body py-2 px-3">
      <div class="row g-2">
        <?php
            $functions = [
                'mobility'      => 'Mobility',
                'hand_use'      => 'Domestic Life',
                'grip_strength' => 'Community Social Civic',
                'selfcare'      => 'Self-care',
                'daily_life'    => 'Major Life Areas',
                'work'          => 'Score For Function and Participation Restriction',
            ];
// Mobility 
// Self-Care 
// Domestic Life 
// Major Life Areas 
// Community Social Civic
// Score Fr fuvction and Participation Restriction
            //  $functions = [
            //     'mobility'      => 'Mobility',  Mobility 
            //     'hand_use'      => 'Hand Use',   Domestic Life 
            //     'grip_strength' => 'Grip Strength',  Community Social Civic
            //     'selfcare'      => 'Self-care',    Self-Care 
            //     'daily_life'    => 'Daily Life',  Major Life Areas 
            //     'work'          => 'Work/Tasks',   Score Fr fuvction and Participation Restriction
            // ];
        foreach ($functions as $key => $label): ?>
            <div class="col-md-4">
              <label class="form-label small"><?php echo $label ?></label>
              <select name="function_<?php echo $key ?>" class="form-select form-select-sm functional-score">
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

      <!-- Score Summary -->
      <div class="border p-3 rounded bg-light mt-3 mb-2">
        <!-- <label class="fw-bold mb-2 d-block">SCORE FOR FUNCTIONAL DIFFICULTIES</label> -->
        <label class="fw-bold mb-2 d-block">Disability Rating</label>
        <div class="row g-2">
          <div class="col-md-2">
            <label class="form-label small">No Difficulty</label>
            <input type="text" class="form-control form-control-sm" name="count_no_difficulty" id="count_no_difficulty" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Mild</label>
            <input type="text" class="form-control form-control-sm" name="count_mild" id="count_mild" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Moderate</label>
            <input type="text" class="form-control form-control-sm" name="count_moderate" id="count_moderate" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Severe</label>
            <input type="text" class="form-control form-control-sm" name="count_severe" id="count_severe" readonly>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Complete</label>
            <input type="text" class="form-control form-control-sm" name="count_complete" id="count_complete" readonly>
          </div>
        </div>
      </div>

      <!-- Findings & Remarks -->
      <div class="row g-2">
        <!-- <div class="col-md-6">
          <label class="form-label small">Findings</label>
          <textarea name="findings_functional" class="form-control form-control-sm" rows="2"></textarea>
        </div> -->
        <div class="col-md-6">
          <label class="form-label small">Remarks</label>
          <textarea name="remarks_functional" class="form-control form-control-sm" rows="2"></textarea>
        </div>
      </div>

      <!-- Ratings and Summary -->
      <div class="row g-2 mt-2">
        <div class="col-md-6">
          <label class="form-label small">Conclusion</label>
          <select name="conclusion_duration" class="form-select form-select-sm">
            <option value="">Select</option>
            <option value="Temporary">Temporary</option>
            <option value="Permanent">Permanent</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Upload Document</label>
          <input type="file" name="supporting_document" class="form-control form-control-sm">
        </div>
      </div>

      <div class="row g-2 mt-2">
        <div class="col-md-6">
          <label class="form-label small">Recommended Assistive Products</label>
          <input type="text" name="assistive_products" class="form-control form-control-sm">
        </div>
        <div class="col-md-6">
          <label class="form-label small">Other Required Services</label>
          <input type="text" name="other_services" class="form-control form-control-sm">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Script to handle counting -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selects = document.querySelectorAll('.functional-score');
  const scores = {
    no_difficulty: 0,
    mild: 0,
    moderate: 0,
    severe: 0,
    complete: 0
  };

  function updateScoreDisplay() {
    Object.keys(scores).forEach(key => scores[key] = 0);

    selects.forEach(select => {
      const val = select.value;
      if (val && scores.hasOwnProperty(val)) scores[val]++;
    });

    document.getElementById('count_no_difficulty').value = scores.no_difficulty;
    document.getElementById('count_mild').value = scores.mild;
    document.getElementById('count_moderate').value = scores.moderate;
    document.getElementById('count_severe').value = scores.severe;
    document.getElementById('count_complete').value = scores.complete;
  }

  selects.forEach(select => {
    select.addEventListener('change', updateScoreDisplay);
  });

  updateScoreDisplay(); // run once on load
});
</script>



    </div>

    <!-- Submit -->
    <div class="text-center mt-3">
      <button type="submit" class="btn btn-primary btn-md px-4">Submit</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
