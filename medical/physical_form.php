<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->

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
        <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseOne"
          aria-expanded="true" id="headingOne">
          <h4 class=""> 1. Summary Findings</h4>
        </div>
        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionPhysical">
          <div class="accordion-body py-2 px-3">
            <input type="hidden" name="disability_type" value="physical">
            <input type="hidden" name="assessment_id" value="<?php echo $_GET['assessment_id'] ?? '' ?>">
            <input type="hidden" name="user_id" value="<?php echo $pwdUser['id'] ?? '' ?>">

            <h5 class="">Medical History</h5>

            <div class="row g-2">

              <!-- <div class="col-md-6">
                <label class="form-label small">Medical History</label>
                <textarea name="medical_history" class="form-control form-control-sm" rows="2"></textarea>
              </div> -->
              <div class="col-md-3">
                <label class="form-label small">Injury/Onset</label>
                <input type="date" name="onset_date" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label small">Last Intervention</label>
                <input type="date" name="last_intervention_date" class="form-control">
              </div>
              <div class="col-md">
                <label class="form-label small">Cause of Disability</label>
                <textarea name="cause_of_disability" class="form-control " rows="2"></textarea>
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

        <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseStructural"
          aria-expanded="true" id="headingStructural">
          <h4 class=""> 2. Structural Impairments</h4>
        </div>

        <div id="collapseStructural" class="accordion-collapse collapse"
          data-bs-parent="#disabilityAssessmentAccordion">
          <div class="accordion-body">

            <!-- Region Checkboxes -->
            <label class="fw-bold mb-2">Tick Region(s) Being Assessed</label>
            <div class="row g-2 mb-3">
              <?php
              $regions = [
                's710_head_neck' => 's710 Head & Neck Region',
                's720_shoulder' => 's720 Shoulder Region',
                's730_upper_extremity' => 's730 Upper Extremity (arm, hand)',
                's740_pelvis' => 's740 Pelvis',
                's750_lower_extremity' => 's750 Lower Extremity (leg, foot)',
                's760_trunk' => 's760 Trunk',
                's8_skin_other' => 's8 Skin and Related Structures Any other body structures',
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
            <h5 class="mb-3">Physical / Structural Assessment Summary</h5>

            <div class="table-responsive mb-4 bg-light">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width: 40%;">Assessment Area - Impairment Level</th>
                    <th>Finding / Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $areas = [
                    'muscle_power' => 'Muscle Power of Affected Muscle Groups',
                    'joint_motion' => 'Range of Motion of Joints Affected',
                    'structural_deviation' => 'Degree of Structural Angulation / Deviation',
                    'limb_amputation' => 'Level of Limb Amputation',
                    'limb_length' => 'Bilateral Lower Limb Length',
                    'balance_coordination' => 'Balance and Coordination',
                    'other_impairments' => 'Other Physical Impairments (Specify)',
                  ];

                  foreach ($areas as $field => $label): ?>
                    <tr>
                      <td><strong><?php echo $label; ?></strong>
                        <select name="impairment_score_<?php echo $field; ?>"
                          class="form-select form-control structural-score">
                          <option value="">Select Impairment Level</option>
                          <option value="none">No Impairment</option>
                          <option value="mild">Mild</option>
                          <option value="moderate">Moderate</option>
                          <option value="severe">Severe</option>
                          <option value="complete">Complete</option>
                        </select>
                      </td>
                      <td class="pt-3">
                        <textarea name="remark_<?php echo $field; ?>" class="form-control p-2" rows="2"
                          placeholder="Enter findings or remarks"></textarea>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>


            <!-- Score for Impairments -->
            <div class="border p-3 rounded bg-light mb-3">
              <label class="fw-bold mb-2 d-block">SCORE FOR IMPAIRMENTS</label>
              <select class="form-select form-control" name="impairment_rating">
                <option value="">Select</option>
                <option value="no_difficulty">No Difficulty</option>
                <option value="mild">Mild</option>
                <option value="moderate">Moderate</option>
                <option value="severe">Severe</option>
                <option value="complete">Complete</option>
              </select>

            </div>
          </div>

        </div>
      </div>
      <!-- </div> -->



      <!-- Functional Impairments -->
      <div class="accordion-item">
        <div class="accordion-header " role="button" data-toggle="collapse" data-target="#collapseThree"
          aria-expanded="true" id="headingStructural">
          <h4 class="">3. Functional & Participation</h4>
        </div>
        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionPhysical">
          <div class="accordion-body py-2 px-3">
            <div class="row g-2">
              <h5 class="mb-3">Function and Participation Restriction Assessment</h5>

              <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 40%;">Functional Area - Difficulty Level</th>
                      <th>Finding / Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $functions = [
                      'mobility' => 'Mobility',
                      'hand_use' => 'Domestic Life',
                      'grip_strength' => 'Community Social Civic',
                      'selfcare' => 'Self-care',
                      'daily_life' => 'Major Life Areas',
                      'work' => 'Score for Function and Participation Restriction',
                    ];

                    foreach ($functions as $key => $label): ?>
                      <tr>
                        <td><strong><?php echo $label; ?></strong>
                          <select name="function_<?php echo $key; ?>" class="form-select form-control functional-score">
                            <option value="">Select</option>
                            <option value="no_difficulty">No Difficulty</option>
                            <option value="mild">Mild</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                            <option value="complete">Complete</option>
                          </select>
                        </td>
                        <td class="pt-3">
                          <textarea name="remark_<?php echo $key; ?>" class="form-control" rows="2"
                            placeholder="Enter findings or remarks"></textarea>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

            </div>


            <!-- Disability rating -->

            <div class="border p-3 rounded bg-light mb-3">
              <label class="fw-bold mb-2 d-block">DISABILITY RATING</label>
              <select class="form-select form-control" name="disability_rating">
                <option value="">Select</option>
                <option value="no_difficulty">No Difficulty</option>
                <option value="mild">Mild</option>
                <option value="moderate">Moderate</option>
                <option value="severe">Severe</option>
                <option value="complete">Complete</option>
              </select>

            </div>



            <div class="row g-2 mt-2">
              <div class="col-md-6">
                <label class="form-label small">Recommended Assistive Products</label>
                <input type="text" name="assistive_products" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Other Required Services</label>
                <input type="text" name="other_services" class="form-control ">
              </div>
            </div>

            <!-- Ratings and Summary -->
            <div class="row g-2 mt-2">
              <div class="col-md-6">
                <label class="form-label small">Conclusion</label>
                <select name="conclusion_decision" class="form-select form-control">
                  <option value="">Select</option>
                  <option value="Temporary">Temporary</option>
                  <option value="Permanent">Permanent</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Upload Document</label>
                <input type="file" name="supporting_document" class=" form-control">
              </div>
            </div>


          </div>
        </div>
      </div>

    </div>

    <!-- Submit -->
    <div class="text-center mt-3">
      <button type="submit" class="btn btn-primary px-5">Submit</button>
    </div>
  </form>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->