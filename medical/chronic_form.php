<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4 mb-4">
  <h4 class="mb-4 text-center fw-bold display-6"> Progressive Chronic Disorders Assessments</h4>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="accordion" id="disabilityAssessmentAccordion">

      <!-- 1. Summary Findings -->
      <div class="accordion-item">
        <!-- <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true">
            1. Summary Findings
          </button>
        </h2> -->
        <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" id="headingOne">
            <h4 class=""> 1. Summary Findings</h4>
        </div>
        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#disabilityAssessmentAccordion">
          <div class="accordion-body p-2">
            <input type="hidden" name="structural_disability" value="structural_disability">
            <input type="hidden" name="assessment_id" value="">
            <input type="hidden" name="disability_type" value="chronic">
            <input type="hidden" name="user_id" value="">

            <div class="mb-2">
              <label class="form-label badge badge-success  text-wrap">Medical History (Brief)</label>
              <!-- <textarea name="medical_history" class="form-control form-control-sm" rows="2"></textarea> -->
            </div>

            <div class="row g-2 mb-2">
              <div class="col">
                <label class="form-label">Date of Injury/Onset</label>
                <input type="date" name="onset_date" class="form-control form-control-sm">
              </div>
              <div class="col">
                <label class="form-label">Date of Last Intervention</label>
                <input type="date" name="last_intervention_date" class="form-control form-control-sm">
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label">Past & Ongoing Interventions</label>
              <textarea name="interventions" class="form-control form-control-sm" rows="2"></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Cause of Disability</label>
              <input type="text" name="cause_of_disability" class="form-control form-control-sm">
            </div>
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
         <div class="mb-2">
              <label class="form-label">Structural Impairements</label>
              <textarea name="structural_impairments" class="form-control form-control-sm" rows="2"></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Regions Affected</label>
              <textarea name="regions_affected" class="form-control form-control-sm" rows="2"></textarea>
            </div>


            <div class="row mb-2">
              <?php
                  $labels = [
                      'cardiovascular'   => 'Cardiovascular',
                      'respiratory'      => 'Respiratory',
                      'cancer'           => 'Cancer / Tumor',
                      'musculoskeletal'  => 'Musculoskeletal',
                      'neurological'     => 'Neurological',
                      'gastrointestinal' => 'Gastrointestinal',
                      'dermatological'   => 'Dermatological',
                      'hematologic'      => 'Hematologic',
                      'lymphatic'        => 'Lymphatic',
                      'genitourinary'    => 'Genito-urinary',
                      'frailty'          => 'Frailty',
                      'other'            => 'Other',
                  ];
              foreach ($labels as $name => $label) {?>
                <div class="col-md-6 mb-3">
                  <label><?php echo $label ?></label>
                  <select name="score_<?php echo $name ?>" class="form-select">
                    <option value="">Select</option>
                    <option value="none">None</option>
                    <option value="mild">Mild</option>
                    <option value="moderate">Moderate</option>
                    <option value="severe">Severe</option>
                    <option value="complete">Complete</option>
                  </select>
                </div>
              <?php }?>
            </div>

            <div class="mb-3">
              <label>Findings (Clinical)</label>
              <textarea name="findings_clinical" class="form-control" rows="2"><?php echo $findings_clinical ?? '' ?></textarea>
            </div>

            <div class="mb-3">
              <label>Remarks (Clinical)</label>
              <textarea name="remarks_clinical" class="form-control" rows="2"><?php echo $remarks_clinical ?? '' ?></textarea>
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

      <div class="row mb-2">
        <?php
            $areas = [
                'mobility'  => 'Mobility',
                'selfcare'  => 'Self-care',
                'domestic'  => 'Domestic Life',
                'majorlife' => 'Major Life Areas',
                'community' => 'Community / Social Life',
            ];
        foreach ($areas as $name => $label) {?>
          <div class="col-md-6 mb-3">
            <label><?php echo $label ?></label>
            <select name="difficulty_<?php echo $name ?>" class="form-select functional-rating">
              <option value="">Select</option>
              <option value="no_difficulty">No Difficulty</option>
              <option value="mild">Mild</option>
              <option value="moderate">Moderate</option>
              <option value="severe">Severe</option>
              <option value="complete">Complete</option>
            </select>
          </div>
        <?php }?>
      </div>

      <div class="mb-3">
        <label>Findings (Functional)</label>
        <textarea name="findings_functional" class="form-control" rows="2"><?php echo $findings_functional ?? '' ?></textarea>
      </div>

      <div class="mb-3">
        <label>Remarks (Functional)</label>
        <textarea name="remarks_functional" class="form-control" rows="2"><?php echo $remarks_functional ?? '' ?></textarea>
      </div>

      <label class="badge bg-success text-wrap mb-2">Total Disability Rating (%)</label>

      <div class="row mb-2">
        <div class="col-md-2">
          <label>None</label>
          <input type="text" id="rating_none" name="rating_none" class="form-control" readonly>
        </div>
        <div class="col-md-2">
          <label>Mild</label>
          <input type="text" id="rating_mild" name="rating_mild" class="form-control" readonly>
        </div>
        <div class="col-md-2">
          <label>Moderate</label>
          <input type="text" id="rating_moderate" name="rating_moderate" class="form-control" readonly>
        </div>
        <div class="col-md-2">
          <label>Severe</label>
          <input type="text" id="rating_severe" name="rating_severe" class="form-control" readonly>
        </div>
        <div class="col-md-2">
          <label>Complete</label>
          <input type="text" id="rating_complete" name="rating_complete" class="form-control" readonly>
        </div>
      </div>

       <div class="row mb-4">

              <div class="col-md-6">
                <label>Conclusion</label>
                <select name="conclusion_duration" class="form-select">
                  <option value="">Select</option>
                  <option value="Temporary">Temporary</option>
                  <option value="Permanent">Permanent</option>
                </select>
              </div>

               <div class="col-md-6">
              <label>Upload Supporting Document</label>
              <input type="file" name="supporting_document" class="form-control">
            </div>

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
