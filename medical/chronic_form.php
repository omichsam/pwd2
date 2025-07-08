<form method="POST" class="needs-validation p-3" enctype="multipart/form-data" novalidate>
    <div class="card-header">
        <h4>Progressive Chronic Disorder Assessment</h4>
    </div>

    <div id="accordion">

        <!-- Summary Findings -->
        <div class="card">
            <div class="card-header" id="headingSummary">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseSummary">
                        1. Summary Findings
                    </button>
                </h5>
            </div>
            <div id="collapseSummary" class="collapse show" data-parent="#accordion">
                <div class="card-body">
                    <div class="form-group">
                        <label>Medical History (brief)</label>
                        <textarea class="form-control" name="medical_history"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Date of Injury/Onset of Illness</label>
                        <input type="date" class="form-control" name="injury_date">
                    </div>
                    <div class="form-group">
                        <label>Date of Last Intervention</label>
                        <input type="date" class="form-control" name="last_intervention_date">
                    </div>
                    <div class="form-group">
                        <label>Past and Ongoing Interventions</label>
                        <textarea class="form-control" name="past_interventions"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Cause of Disability</label>
                        <input type="text" class="form-control" name="cause_of_disability">
                    </div>
                </div>
            </div>
        </div>

        <!-- Structural Impairments -->
        <div class="card">
            <div class="card-header" id="headingImpairment">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseImpairment">
                        2. Structural Impairments
                    </button>
                </h5>
            </div>
            <div id="collapseImpairment" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    <div class="form-group">
                        <label>Structural Impairments</label>
                        <textarea class="form-control" name="structural_impairments"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Regions Affected</label>
                        <textarea class="form-control" name="regions_affected"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Findings and Diagnostic Tests</label>
                        <textarea class="form-control" name="diagnostic_tests"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Impairment Score</label>
                        <input type="text" class="form-control" name="impairment_score">
                    </div>
                    <div class="form-group">
                        <label>General Remark</label>
                        <textarea class="form-control" name="impairment_remark"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Functional Restriction -->
        <div class="card">
            <div class="card-header" id="headingFunction">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFunction">
                        3. Function and Participation Restriction
                    </button>
                </h5>
            </div>
            <div id="collapseFunction" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    <div class="form-group">
                        <label>Functional Restriction Score</label>
                        <input type="text" class="form-control" name="functional_score">
                    </div>
                    <div class="form-group">
                        <label>General Remark</label>
                        <textarea class="form-control" name="function_remark"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendation -->
        <div class="card">
            <div class="card-header" id="headingRecom">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseRecom">
                        4. Conclusion and Recommendation
                    </button>
                </h5>
            </div>
            <div id="collapseRecom" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    <div class="form-group">
                        <label>Disability Rating</label>
                        <select class="form-control" name="disability_rating">
                            <option>No disability</option>
                            <option>Mild</option>
                            <option>Moderate</option>
                            <option>Severe</option>
                            <option>Complete</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Conclusion Type</label>
                        <select class="form-control" name="conclusion_type">
                            <option>Temporary</option>
                            <option>Permanent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Recommended Assistive Products</label>
                        <textarea class="form-control" name="assistive_products"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Other Required Services</label>
                        <textarea class="form-control" name="required_services"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Upload Supporting Documents</label>
                        <input type="file" class="form-control-file" name="supporting_docs">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card-footer text-right">
        <button type="submit" class="btn btn-success">Submit Assessment</button>
    </div>
</form>