<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Progressive Chronic Disorder Assessment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4 mb-5">
  <h4 class="mb-4 text-center fw-bold display-6">Progressive Chronic Disorder Assessment</h4>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- #region -->

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4 mb-5">
  <h5 class="mb-4 text-center fw-bold">Speech, Language, Communication and Swallowing Assessment</h5>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="accordion" id="assessmentAccordion">

      <!-- A. Reason for Referral -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingReason">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReason" aria-expanded="true">
            A. Reason for Referral
          </button>
        </h2>
        <div id="collapseReason" class="accordion-collapse collapse show" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <textarea class="form-control" name="reason_for_referral" rows="2" placeholder="Reason for referral..."></textarea>
          </div>
        </div>
      </div>

      <!-- B. History -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingHistory">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory">
            B. Medical / Developmental History
          </button>
        </h2>
        <div id="collapseHistory" class="accordion-collapse collapse" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <textarea class="form-control mb-2" name="medical_history" rows="2" placeholder="Medical / developmental history..."></textarea>
            <textarea class="form-control" name="communication_history" rows="2" placeholder="Communication history..."></textarea>
          </div>
        </div>
      </div>

      <!-- C. Language Assessment -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingLanguage">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLanguage">
            C. Language Assessment
          </button>
        </h2>
        <div id="collapseLanguage" class="accordion-collapse collapse" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <label>Language Level:</label>
            <input type="text" class="form-control mb-2" name="language_level" placeholder="e.g. Receptive, Expressive, Pragmatic...">
            <label>Additional Notes:</label>
            <textarea class="form-control" name="language_notes" rows="2"></textarea>
          </div>
        </div>
      </div>

      <!-- D. Speech Assessment -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingSpeech">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpeech">
            D. Speech Assessment
          </button>
        </h2>
        <div id="collapseSpeech" class="accordion-collapse collapse" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <textarea class="form-control" name="speech_assessment" rows="2" placeholder="Describe articulation, phonology, fluency, etc."></textarea>
          </div>
        </div>
      </div>

      <!-- E. Swallowing & Feeding -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingSwallow">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSwallow">
            E. Swallowing / Feeding Assessment
          </button>
        </h2>
        <div id="collapseSwallow" class="accordion-collapse collapse" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <textarea class="form-control" name="swallowing_assessment" rows="2" placeholder="Feeding method, challenges, observed risks, etc."></textarea>
          </div>
        </div>
      </div>

      <!-- F. Assistive / AAC Needs -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingAAC">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAAC">
            F. AAC / Assistive Communication
          </button>
        </h2>
        <div id="collapseAAC" class="accordion-collapse collapse" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <textarea class="form-control" name="aac_needs" rows="2" placeholder="Assistive communication tools used or needed"></textarea>
          </div>
        </div>
      </div>

      <!-- G. Conclusion & Recommendation -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingConclusion">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConclusion">
            G. Conclusion & Recommendation
          </button>
        </h2>
        <div id="collapseConclusion" class="accordion-collapse collapse" data-bs-parent="#assessmentAccordion">
          <div class="accordion-body">
            <div class="mb-3">
              <label class="form-label">Conclusion</label>
              <select name="conclusion_duration" class="form-select">
                <option value="">Select</option>
                <option value="Temporary">Temporary</option>
                <option value="Permanent">Permanent</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Recommended Assistive Product(s)</label>
              <input type="text" class="form-control" name="recommended_assistive_products" placeholder="List assistive products...">
            </div>

            <div class="mb-3">
              <label class="form-label">Other Required Services</label>
              <input type="text" class="form-control" name="other_services_required" placeholder="e.g. therapy, specialist referral...">
            </div>

            <div class="mb-3">
              <label class="form-label">Upload Supporting Document</label>
              <input type="file" name="supporting_document" class="form-control">
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary">Submit Assessment</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



      <label class="badge bg-success text-wrap mb-2">Total Disability Rating (%)</label>
