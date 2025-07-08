<form method="POST" class="needs-validation p-3" novalidate>
    <div class="card">
        <div class="card-header">
            <h4>Maxillofacial Disability Assessment</h4>
        </div>
        <div class="card-body">

            <div class="accordion" id="assessmentAccordion">

                <!-- Section 1: Health Facility & Date -->
                <div class="card">
                    <div class="card-header" id="headingFacility">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                data-target="#collapseFacility">
                                Facility Information
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFacility" class="collapse show" data-parent="#assessmentAccordion">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="facility_name">Health Facility Name</label>
                                <input type="text" class="form-control" id="facility_name" name="health_facility_name"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="assessment_date">Date of Assessment</label>
                                <input type="date" class="form-control" id="assessment_date" name="assessment_date"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Medical History -->
                <div class="card">
                    <div class="card-header" id="headingMedical">
                        <h2 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseMedical">
                                Medical & Dental History
                            </button>
                        </h2>
                    </div>
                    <div id="collapseMedical" class="collapse" data-parent="#assessmentAccordion">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="medical_history">Medical History</label>
                                <textarea class="form-control" id="medical_history" name="medical_history" rows="3"
                                    required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="dental_history">Dental History</label>
                                <textarea class="form-control" id="dental_history" name="dental_history" rows="3"
                                    required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Dental Assessment -->
                <div class="card">
                    <div class="card-header" id="headingAssessment">
                        <h2 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseAssessment">
                                Dental Assessment
                            </button>
                        </h2>
                    </div>
                    <div id="collapseAssessment" class="collapse" data-parent="#assessmentAccordion">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="dental_assessment">Dental Assessment</label>
                                <textarea class="form-control" id="dental_assessment" name="dental_assessment" rows="4"
                                    required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Conclusion -->
                <div class="card">
                    <div class="card-header" id="headingConclusion">
                        <h2 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseConclusion">
                                Conclusion
                            </button>
                        </h2>
                    </div>
                    <div id="collapseConclusion" class="collapse" data-parent="#assessmentAccordion">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Type of Disability</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="disability_type" id="temporary"
                                        value="Temporary" required>
                                    <label class="form-check-label" for="temporary">Temporary</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="disability_type" id="permanent"
                                        value="Permanent">
                                    <label class="form-check-label" for="permanent">Permanent</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Recommendations -->
                <div class="card">
                    <div class="card-header" id="headingRecommendation">
                        <h2 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseRecommendation">
                                Recommendations
                            </button>
                        </h2>
                    </div>
                    <div id="collapseRecommendation" class="collapse" data-parent="#assessmentAccordion">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="assistive_products">Recommended Assistive Products</label>
                                <textarea class="form-control" id="assistive_products" name="assistive_products"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="required_services">Other Services Required</label>
                                <textarea class="form-control" id="required_services" name="required_services"
                                    rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit -->
            <div class="card-footer text-right">
                <button class="btn btn-primary" type="submit">Submit Assessment</button>
            </div>

        </div>
    </div>
</form>