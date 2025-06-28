<div class="container mt-5">
    <h4 class="mb-4">Visual Impairment Assessment</h4>
    <form method="#post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="accordion" id="visualAssessmentAccordion">

            <!-- 1. History & Assistive Device -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingHistory">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseHistory" aria-expanded="true" aria-controls="collapseHistory">
                        1. History & Assistive Device
                    </button>
                </h6>
                <div id="collapseHistory" class="accordion-collapse collapse show" aria-labelledby="headingHistory"
                    data-bs-parent="#visualAssessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Assistive Device</label>
                            <textarea class="form-control" name="assistive_device"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Medical History</label>
                            <textarea class="form-control" name="medical_history"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Ocular History</label>
                            <textarea class="form-control" name="ocular_history"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Distance Visual Acuity -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingAcuity">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseAcuity" aria-expanded="false" aria-controls="collapseAcuity">
                        2. Distance Visual Acuity
                    </button>
                </h6>
                <div id="collapseAcuity" class="accordion-collapse collapse" aria-labelledby="headingAcuity"
                    data-bs-parent="#visualAssessmentAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label>Right Eye - With Correction</label>
                                <input type="text" name="distance_right_eye_with_correction" class="form-control">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label>Right Eye - Without Correction</label>
                                <input type="text" name="distance_right_eye_without_correction" class="form-control">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label>Left Eye - With Correction</label>
                                <input type="text" name="distance_left_eye_with_correction" class="form-control">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label>Left Eye - Without Correction</label>
                                <input type="text" name="distance_left_eye_without_correction" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Near Vision Test</label>
                            <input type="text" class="form-control" name="near_vision_test">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Ophthalmic Examination -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingExam">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseExam" aria-expanded="false" aria-controls="collapseExam">
                        3. Ophthalmic Examination
                    </button>
                </h6>
                <div id="collapseExam" class="accordion-collapse collapse" aria-labelledby="headingExam"
                    data-bs-parent="#visualAssessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Examination Notes (Right & Left Eye)</label>
                            <textarea class="form-control" name="ophthalmic_exam_notes"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Specialized Tests -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingTests">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTests" aria-expanded="false" aria-controls="collapseTests">
                        4. Specialized Tests
                    </button>
                </h6>
                <div id="collapseTests" class="accordion-collapse collapse" aria-labelledby="headingTests"
                    data-bs-parent="#visualAssessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Humphreys Visual Field</label>
                            <input type="text" class="form-control" name="humphreys_visual_field">
                        </div>
                        <div class="mb-3">
                            <label>Colour Vision</label>
                            <input type="text" class="form-control" name="colour_vision">
                        </div>
                        <div class="mb-3">
                            <label>Stereopsis</label>
                            <input type="text" class="form-control" name="stereopsis">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Conclusion -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingConclusion">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseConclusion" aria-expanded="false" aria-controls="collapseConclusion">
                        5. Conclusion
                    </button>
                </h6>
                <div id="collapseConclusion" class="accordion-collapse collapse" aria-labelledby="headingConclusion"
                    data-bs-parent="#visualAssessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Category</label>
                            <select class="form-control" name="category">
                                <option>Normal</option>
                                <option>Mild Impairment</option>
                                <option>Moderate Impairment</option>
                                <option>Severe Impairment</option>
                                <option>Blind</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Cause of Vision Impairment</label>
                            <textarea class="form-control" name="cause_of_vision_impairment"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Percentage Disability</label>
                            <input type="text" class="form-control" name="percentage_disability">
                        </div>
                        <div class="mb-3">
                            <label>Any Possible Intervention</label>
                            <textarea class="form-control" name="possible_intervention"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Near Vision Impairment</label>
                            <textarea class="form-control" name="near_vision_impairment"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Recommendation</label>
                            <select class="form-control" name="recommendation">
                                <option>TEMPORARY</option>
                                <option>PERMANENT</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. File Upload -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingUpload">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseUpload" aria-expanded="false" aria-controls="collapseUpload">
                        6. Supporting Document Upload
                    </button>
                </h6>
                <div id="collapseUpload" class="accordion-collapse collapse" aria-labelledby="headingUpload"
                    data-bs-parent="#visualAssessmentAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Upload Related Files (PDF, Image)</label>
                            <input type="file" class="form-control" name="supporting_document" accept=".pdf,.jpg,.png">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Submit Assessment
            </button>
        </div>
    </form>
</div>