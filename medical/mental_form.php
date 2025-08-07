<div class="container mt-5">
    <h4 class="mb-4 text-center">Assessment Form for Mental / Intellectual / Autism Spectrum Disorders</h4>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="mentalAssessmentAccordion">

            <!-- 1. Clinical History & Mental Status -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" id="headingOne">
                    <h4>1. Clinical History & Mental Status</h4>
                </div>
                <div id="collapseOne" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row mb-3">
                            <div class="col-md-6" hidden>
                                <input type="text" name="mental_disability" value="mental_disability"
                                    class="form-control">
                            </div>
                            <div class="col-md-12" hidden>
                                <input type="text" name="assessment_id"
                                    value="<?php echo $_GET['assessment_id'] ?? '' ?>" class="form-control">
                                <input type="hidden" name="disability_type" value="mental">
                            </div>
                            <div class="col-md-12" hidden>
                                <input type="text" name="user_id" value="<?php echo $pwdUser['id'] ?? '' ?>"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Brief Clinical History (Past and Present Medical History)</label>
                            <textarea name="clinical_history" class="form-control"
                                rows="3"><?php echo $clinical_history ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Mental Status Evaluation</label>
                            <textarea name="mental_status_evaluation" class="form-control"
                                rows="2"><?php echo $mental_status_evaluation ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Functional Assessment -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="false" id="headingTwo">
                    <h4>2. Functional Assessment</h4>
                </div>
                <div id="collapseTwo" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <h5 class="fw-bolder">Complete the Assessment Tool Below by Scoring Appropriately</h5>
                        <h6 class="fst-italic fw-boldd text-sm text-dark py-2">Knows how and when to feed, toilet or
                            groom self</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Feeding</label>
                                <select name="feeding_score" class="form-control">
                                    <option value="0">0 - Completely Independent</option>
                                    <option value="1">1 - Partial</option>
                                    <option value="2">2 - Minimal</option>
                                    <option value="3">3 - None (Dependent)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Toileting</label>
                                <select name="toileting_score" class="form-control">
                                    <option value="0">0 - Completely Independent</option>
                                    <option value="1">1 - Partial</option>
                                    <option value="2">2 - Minimal</option>
                                    <option value="3">3 - None (Dependent)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Grooming</label>
                                <select name="grooming_score" class="form-control">
                                    <option value="0">0 - Completely Independent</option>
                                    <option value="1">1 - Partial</option>
                                    <option value="2">2 - Minimal</option>
                                    <option value="3">3 - None (Dependent)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Physical/Daytime Disability</label>
                                <select name="physical_disability_score" class="form-control">
                                    <option value="0">0 - Completely Independent</option>
                                    <option value="1">1 - Independent in special environment</option>
                                    <option value="2">2 - Mildly Dependent (Limited Assistance)</option>
                                    <option value="3">3 - Moderately Dependent (Moderate Assistance)</option>
                                    <option value="4">4 - Heavily Dependent (Assistance with major activities)</option>
                                    <option value="5">5 - Totally Dependent</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Employability / Schooling</label>
                                <select name="employability_score" class="form-control">
                                    <option value="0">0 - No Restriction</option>
                                    <option value="1">1 - Restricted job, competitive</option>
                                    <option value="2">2 - Non-competitive workshop, non-competitive</option>
                                    <option value="3">3 - Not Employable / Not in school</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Summary & Upload -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="false" id="headingThree">
                    <h4>3. Summary & Upload</h4>
                </div>
                <div id="collapseThree" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Duration of Illness</label>
                            <input type="text" name="duration_of_illness" class="form-control"
                                value="<?php echo $duration_of_illness ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label>Major Cause of Disability</label>
                            <input type="text" name="major_cause_disability" class="form-control"
                                value="<?php echo $major_cause_disability ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label>Recommended Assistive Product(s)</label>
                            <input type="text" name="recommended_assistive_products" class="form-control"
                                value="<?php echo $recommended_assistive_products ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label>Other Required Services</label>
                            <input type="text" name="other_services_required" class="form-control"
                                value="<?php echo $other_services_required ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label>Upload Supporting Document</label>
                            <input type="file" name="supporting_document" class="form-control-file">
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