

<div class="container mt-5 divider">
    <h4 class="mb-4 text-center">Physical Disability Assessment</h4>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="assessmentAccordion">

            <!-- 1. Medical History --> 
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" id="headingOne">
                    <h4>1. Medical History</h4>
                </div>

                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="medical_history">Medical History</label>
                                <textarea name="medical_history" id="medical_history" class="form-control" rows="3"
                                    placeholder="Describe medical history..."></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="injury_date">Date of Injury</label>
                                <input type="date" name="injury_date" id="injury_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_intervention_date">Last Intervention Date</label>
                                <input type="date" name="last_intervention_date" id="last_intervention_date"
                                    class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cause_of_disability">Cause of Disability</label>
                                <input type="text" name="cause_of_disability" id="cause_of_disability"
                                    class="form-control" placeholder="E.g., accident, illness...">
                            </div>

                            <div class="col-md-6 mb-3" hidden>
                                <input type="text" name="medical_officer_id" value="<?= $pwdUser['id'] ?>"
                                    class="form-control">
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Impairment Scores -->
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" id="headingTwo">
                    <h4>2. Impairments Assessments</h4>
                </div>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="muscle_power_score">Muscle Power</label>
                                <select name="muscle_power_score" id="muscle_power_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joint_range_score">Joint Range</label>
                                <select name="joint_range_score" id="joint_range_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="angulation_score">Angulation</label>
                                <select name="angulation_score" id="angulation_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="amputation_score">Amputation</label>
                                <select name="amputation_score" id="amputation_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Impairment</option>
                                    <option>Mild Impairment</option>
                                    <option>Moderate Impairment</option>
                                    <option>Severe Impairment</option>
                                    <option>Complete Impairment</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="impairments_remark">Overall Remarks / Findings</label>
                                <textarea name="impairments_remark" id="impairments_remark" class="form-control"
                                    rows="3" placeholder="Any relevant findings or remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Restrictions -->
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" id="headingThree">
                    <h4> 3. Activity & Participation Restrictions</h4>
                </div>



                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mobility_score">Mobility</label>
                                <select name="mobility_score" id="mobility_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Difficulty</option>
                                    <option>Mild Difficulty</option>
                                    <option>Moderate Difficulty</option>
                                    <option>Severe Difficulty</option>
                                    <option>Complete Difficulty</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="self_care_score">Self Care</label>
                                <select name="self_care_score" id="self_care_score" class="form-control">
                                    <option value="">Select</option>
                                    <option>No Difficulty</option>
                                    <option>Mild Difficulty</option>
                                    <option>Moderate Difficulty</option>
                                    <option>Severe Difficulty</option>
                                    <option>Complete Difficulty</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="restrictions_remark">Overall Remarks / Findings</label>
                                <textarea name="restrictions_remark" id="restrictions_remark" class="form-control"
                                    rows="3" placeholder="Any relevant restrictions or remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Final Summary -->
            <div class="accordion-item my-2">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseFour"
                    aria-expanded="true" id="headingFour">
                    <h4> 4. Summary & Recommendation</h4>
                </div>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="disability_rating">Overall Disability Rating</label>
                                <select name="disability_rating" id="disability_rating" class="form-control">
                                    <option value="">Select</option>
                                    <option>No</option>
                                    <option>Mild</option>
                                    <option>Moderate</option>
                                    <option>Severe</option>
                                    <option>Complete</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="conclusion_type">Conclusion Type</label>
                                <select name="conclusion_type" id="conclusion_type" class="form-control">
                                    <option value="">Select</option>
                                    <option>Temporary</option>
                                    <option>Permanent</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="assistive_products">Assistive Products</label>
                                <textarea name="assistive_products" id="assistive_products" class="form-control"
                                    rows="3" placeholder="Specify any assistive products..."></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="required_services">Required Services</label>
                                <textarea name="required_services" id="required_services" class="form-control" rows="3"
                                    placeholder="Specify any required services..."></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="supporting_file">Upload Supporting Documents (PDF, JPG, PNG)</label>
                                <input type="file" name="supporting_file" id="supporting_file" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary" name="submit_assessment">Submit Assessment</button>
            </div>

        </div>
    </form>
</div>