<div class="container mt-5 divider">
    <h4 class="mb-4 text-center">Hearing Impairment Assessment</h4>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="assessmentAccordion">

            <!-- 1. Medical History -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" id="headingOne">
                    <h4>1. Medical History</h4>
                </div>
                <div id="collapseOne" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row mb-3">
                            <div class="col-md-6" hidden>
                                <input type="text" name="hearing_Impairments" value="hearing_Impairments" class="form-control">
                            </div>
                            <div class="col-md-12" hidden>
                                <input type="text" name="assessment_id" value="<?php echo $_GET['assessment_id'] ?? ''?>" class="form-control">
                                <input type="hidden" name="disability_type" value="hearing">
                            </div>
                            <div class="col-md-12" hidden>
                                <input type="text" name="user_id" value="<?php echo $pwdUser['id'] ?? ''?>" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>History of Hearing Loss</label>
                            <textarea name="history_of_hearing_loss" class="form-control"><?php echo $history_of_hearing_loss ?? ''?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>History of Hearing Devices Usage</label>
                            <textarea name="history_of_hearing_devices" class="form-control"><?php echo $history_of_hearing_devices ?? ''?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Hearing Test Results -->
            <div class="accordion-item">
                <div class="accordion-header accordion-button collapsed" role="button" data-toggle="collapse" data-target="#collapseTwo">
                    <h4>2. Hearing Test Results</h4>
                </div>
                <div id="collapseTwo" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Left Ear</h5>
                                <div class="mb-3">
                                    <label>Hearing Loss Type</label>
                                    <input type="text" name="hearing_loss_type_left" class="form-control" value="<?php echo $hearing_loss_type_left ?? ''?>">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Grade</label>
                                    <input type="text" name="hearing_grade_left" class="form-control" value="<?php echo $hearing_grade_left ?? ''?>">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Level (dBHL)</label>
                                    <input type="number" step="0.01" name="hearing_level_dbhl_left" class="form-control" value="<?php echo $hearing_level_dbhl_left ?? ''?>">
                                </div>
                                <div class="mb-3">
                                    <label>Monoaural %</label>
                                    <input type="number" step="0.01" name="monoaural_percent_left" class="form-control" value="<?php echo $monoaural_percent_left ?? ''?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Right Ear</h5>
                                <div class="mb-3">
                                    <label>Hearing Loss Type</label>
                                    <input type="text" name="hearing_loss_type_right" class="form-control" value="<?php echo $hearing_loss_type_right ?? ''?>">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Grade</label>
                                    <input type="text" name="hearing_grade_right" class="form-control" value="<?php echo $hearing_grade_right ?? ''?>">
                                </div>
                                <div class="mb-3">
                                    <label>Hearing Level (dBHL)</label>
                                    <input type="number" step="0.01" name="hearing_level_dbhl_right" class="form-control" value="<?php echo $hearing_level_dbhl_right ?? ''?>">
                                </div>
                                <div class="mb-3">
                                    <label>Monoaural %</label>
                                    <input type="number" step="0.01" name="monoaural_percent_right" class="form-control" value="<?php echo $monoaural_percent_right ?? ''?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Binaural %</label>
                            <input type="number" step="0.01" name="binaural_percent" class="form-control" readonly value="<?php echo $binaural_percent ?? ''?>">
                        </div>
                        <div class="mb-3">
                            <label>Hearing Disability Conclusion</label>
                            <select name="hearing_disability_conclusion" class="form-control">
                                <option value="">Select</option>
                                <option value="Temporary">Temporary</option>
                                <option value="Permanent">Permanent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Recommendations -->
            <div class="accordion-item">
                <div class="accordion-header accordion-button collapsed" role="button" data-toggle="collapse" data-target="#collapseThree">
                    <h4>3. Recommendations & Services</h4>
                </div>
                <div id="collapseThree" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label>Recommended Assistive Product(s)</label>
                            <textarea name="recommended_assistive_products" class="form-control" rows="3"><?php echo $recommended_assistive_products ?? ''?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Required Services</label>
                            <textarea name="required_services" class="form-control" rows="3"><?php echo $required_services ?? ''?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Upload Supporting Documents (PDF, JPG, PNG)</label>
                            <input type="file" name="supporting_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary" name="hearing">Submit Assessment</button>
            </div>
        </div>
    </form>
</div>
