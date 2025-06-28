<div class="container mt-5 divider">
    <h4 class="mb-4">Physical Disability Assessment</h4>
    <form action="submit_physical_assessment.php" method="POST">
        <div class="accordion" id="assessmentAccordion">

            <!-- 1. Medical History -->
            <div class="accordion-item">

                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" id="headingOne">
                    <h4>1. Medical History</h4>
                </div>

                <!-- <h2 class="accordion-header accordion-button " id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        1. Medical History
                    </button>
                </h2> -->
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Medical History</label>
                            <textarea name="medical_history" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Date of Injury</label>
                            <input type="date" name="injury_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Last Intervention Date</label>
                            <input type="date" name="last_intervention_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Cause of Disability</label>
                            <input type="text" name="cause_of_disability" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Impairment Scores (Unified Option List) -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        2. Impairment Assessments
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Muscle Power</label>
                            <select name="muscle_power_score" class="form-control">
                                <option value="">Select</option>
                                <option>No Impairment</option>
                                <option>Mild Impairment</option>
                                <option>Moderate Impairment</option>
                                <option>Severe Impairment</option>
                                <option>Complete Impairment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Joint Range</label>
                            <select name="joint_range_score" class="form-control">
                                <option value="">Select</option>
                                <option>No Impairment</option>
                                <option>Mild Impairment</option>
                                <option>Moderate Impairment</option>
                                <option>Severe Impairment</option>
                                <option>Complete Impairment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Angulation</label>
                            <select name="angulation_score" class="form-control">
                                <option value="">Select</option>
                                <option>No Impairment</option>
                                <option>Mild Impairment</option>
                                <option>Moderate Impairment</option>
                                <option>Severe Impairment</option>
                                <option>Complete Impairment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Amputation</label>
                            <select name="amputation_score" class="form-control">
                                <option value="">Select</option>
                                <option>No Impairment</option>
                                <option>Mild Impairment</option>
                                <option>Moderate Impairment</option>
                                <option>Severe Impairment</option>
                                <option>Complete Impairment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Overall Remarks / Findings</label>
                            <textarea name="impairments_remark" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Restrictions (Unified Options + Remarks) -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        3. Activity & Participation Restrictions
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Mobility</label>
                            <select name="mobility_score" class="form-control">
                                <option value="">Select</option>
                                <option>No Difficulty</option>
                                <option>Mild Difficulty</option>
                                <option>Moderate Difficulty</option>
                                <option>Severe Difficulty</option>
                                <option>Complete Difficulty</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Self Care</label>
                            <select name="self_care_score" class="form-control">
                                <option value="">Select</option>
                                <option>No Difficulty</option>
                                <option>Mild Difficulty</option>
                                <option>Moderate Difficulty</option>
                                <option>Severe Difficulty</option>
                                <option>Complete Difficulty</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Overall Remarks / Findings</label>
                            <textarea name="restrictions_remark" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Final Summary -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        4. Summary & Recommendation
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                    data-bs-parent="#assessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Overall Disability Rating</label>
                            <select name="disability_rating" class="form-control">
                                <option value="">Select</option>
                                <option>No</option>
                                <option>Mild</option>
                                <option>Moderate</option>
                                <option>Severe</option>
                                <option>Complete</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Conclusion Type</label>
                            <select name="conclusion_type" class="form-control">
                                <option value="">Select</option>
                                <option>Temporary</option>
                                <option>Permanent</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Assistive Products</label>
                            <textarea name="assistive_products" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Required Services</label>
                            <textarea name="required_services" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Upload Supporting Documents (PDF, JPG, PNG)</label>
                            <input type="file" name="supporting_file" class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Submit Assessment</button>
            </div>

        </div>
    </form>
</div>