<div class="container mt-5 divider">
    <h4 class="mb-4 text-center">Maxillofacial Disabilities Assessment</h4>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="assessmentAccordion">
            <!-- 1. History -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="true" id="headingHistory">
                    <h4>1. History</h4>
                </div>
                <div id="collapseHistory" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="form-group mb-3">
                            <label>Medical History</label>
                            <textarea name="medical_history" class="form-control" rows="3" placeholder="Enter medical history"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Dental History</label>
                            <textarea name="dental_history" class="form-control" rows="3" placeholder="Enter dental history"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 2. Assessment -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseAssessment" aria-expanded="false" id="headingAssessment">
                    <h4>2. Assessment</h4>
                </div>
                <div id="collapseAssessment" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="form-group mb-3">
                            <label>Dental Assessment</label>
                            <textarea name="dental_assessment" class="form-control" rows="3" placeholder="Enter dental assessment"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Conclusion:</label>
                            <select name="conclusion" class="form-control">
                                <option value="">Select Conclusion</option>
                                <option value="Temporary">Temporary</option>
                                <option value="Permanent">Permanent</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Recommended Assistive Product(s):</label>
                            <input type="text" name="recommended_assistive_products" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label>Other Services Required:</label>
                            <input type="text" name="other_services_required" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label>Upload Supporting Document:</label>
                            <input type="file" name="supporting_document" class="form-control-file">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hidden Inputs -->
        <input type="hidden" name="assessment_id" value="<?php echo $_GET['assessment_id'] ?? '' ?>">
        <input type="hidden" name="disability_type" value="maxillofacial">

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Submit Assessment</button>
        </div>
    </form>
</div>
