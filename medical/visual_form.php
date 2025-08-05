<style>
     body {
        background: #f8fafb;
    }
    .accordion-item {
        margin-bottom: 1.5rem;
        border-left: 6px solid #009688; /* Teal */
        background: #fff;
        border-radius: 0.7rem;
        box-shadow: 0 2px 10px rgba(0, 150, 136, 0.07);
    }
    h4:active {
        color: #f8fafb !important;
    }
    .accordion-header h4 {
        color: #000;
        font-size: 1.3rem;
        font-weight: 700;
        padding: 0.85rem 0 0.4rem 0.7rem;
        margin-left: 4px;
    }
    .accordion-body label,
    .accordion-body .eye-section-label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #156067;
    }
    .eye-section-label {
        background: #e0f2f1;
        padding: 7px 0;
        border-radius: 0.4rem;
        margin-bottom: 1.3rem;
        text-align: center;
        font-size: 1.05rem;
        color: #00897b;
    }
    .form-control, .form-control-file, select {
        background: #f6fefd !important;
        border-radius: 0.6rem !important;
        margin-bottom: 22px !important;
        border: 1px solid #b2dfdb !important;
        font-size: 1rem !important;
        color: #14414c !important;
        box-shadow: none !important;
    }
    .fw-bold, .display-5 {
        color: #00695c !important;
        font-weight: 700 !important;
    }
    .btn-primary {
        background: #009688 !important;
        border-color: #009688 !important;
        padding: 10px 38px;
        font-size: 1.13rem;
        border-radius: 1.6rem;
        box-shadow: 0 3px 12px rgba(0,150,136,0.10);
        font-weight: 600;
    }
    .btn-primary:hover, .btn-primary:focus {
        background: #00897b !important;
        border-color: #00897b !important;
        color: #f6fefd !important;
    }
    .vr {
        background-color: #009688 !important;
        width: 3px;
        opacity: 0.19;
    }
    @media (max-width: 576px) {
        .col-6, .col-md-6, .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .accordion-header h4 {
            font-size: 1.02rem;
            padding: 0.55rem 0 0.25rem 0.5rem;
        }
    }
</style>

<div class="container mt-5">
    <h6 class="mb-4 text-center fw-bold display-66">Assessment Form for Vision / Visual Impairment</h6>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="accordion" id="visualAssessmentAccordion">

            <!-- 1. History -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" id="headingOne">
                    <h4>1. History & Background</h4>
                </div>
                <div id="collapseOne" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <input type="hidden" name="visual_disability" value="visual_disability">
                        <input type="hidden" name="assessment_id" value="<?php echo $_GET['assessment_id'] ?? '' ?>">
                        <input type="hidden" name="disability_type" value="visual">
                        <input type="hidden" name="user_id" value="<?php echo $pwdUser['id'] ?? '' ?>">
                        <!-- <label>History</label>
                        <textarea name="history" class="form-control" rows="2"></textarea> -->
                        <label>Assistive Device</label>
                        <input type="text" name="assistive_device" class="form-control">
                        <label>Medical History</label>
                        <textarea name="medical_history" class="form-control" rows="2"></textarea>
                        <label>Ocular History</label>
                        <textarea name="ocular_history" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Vision & Eye Examination -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse"
                     data-target="#collapseTwo" aria-expanded="false" id="headingTwo">
                    <h4>2. Vision & Eye Examination</h4>
                </div>


                <div id="collapseTwo" class="accordion-collapse collapse">
                     <div class="accordion-body">
                        <div class="fw-bold mb-3">Distance Visual Acuity</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="eye-section-label">Right Eye</div>
                                <label>With Correction</label>
                                <input type="text" name="right_eye_with_correction" class="form-control">
                                <label>Without Correction</label>
                                <input type="text" name="right_eye_without_correction" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <div class="eye-section-label">Left Eye</div>
                                <label>With Correction</label>
                                <input type="text" name="left_eye_with_correction" class="form-control">
                                <label>Without Correction</label>
                                <input type="text" name="left_eye_without_correction" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <div class="eye-section-label">Near Vision Test</div>
                                <label>With Correction</label>
                                <input type="text" name="near_vision_with_correction" class="form-control">
                                <label>Without Correction</label>
                                <input type="text" name="near_vision_without_correction" class="form-control">
                            </div>
                        </div>

                        <div class="fw-bold mb-3">Ophthalmic Examination</div>
                        <div class="row">
                            <!-- Right Eye -->
                            <div class="col-md-6">
                                <div class="eye-section-label">Right Eye</div>
                                <label>Present Eyeball</label>
                                <input type="text" name="present_eyeball_right" class="form-control">
                                <label>Squint</label>
                                <input type="text" name="squint_right" class="form-control">
                                <label>Nystagmus</label>
                                <input type="text" name="nystagmus_right" class="form-control">
                                <label>Tearing</label>
                                <input type="text" name="tearing_right" class="form-control">
                                <label>Lids</label>
                                <input type="text" name="lids_right" class="form-control">
                                <label>Conjunctiva</label>
                                <input type="text" name="conjunctiva_right" class="form-control">
                                <label>Cornea</label>
                                <input type="text" name="cornea_right" class="form-control">
                                <label>Anterior Chamber</label>
                                <input type="text" name="anterior_chamber_right" class="form-control">
                                <label>Iris</label>
                                <input type="text" name="iris_right" class="form-control">
                                <label>Pupil</label>
                                <input type="text" name="pupil_right" class="form-control">
                                <label>Lens</label>
                                <input type="text" name="lens_right" class="form-control">
                                <label>Fundus</label>
                                <input type="text" name="fundus_right" class="form-control">
                            </div>
                            <!-- Divider for desktop -->
                            <div class="d-none d-md-flex align-items-stretch justify-content-center col-md-1 px-0">
                                <div class="vr mx-auto" style="height: 100%;"></div>
                            </div>
                            <!-- Left Eye -->
                            <div class="col-md-5">
                                <div class="eye-section-label">Left Eye</div>
                                <label>Present Eyeball</label>
                                <input type="text" name="present_eyeball_left" class="form-control">
                                <label>Squint</label>
                                <input type="text" name="squint_left" class="form-control">
                                <label>Nystagmus</label>
                                <input type="text" name="nystagmus_left" class="form-control">
                                <label>Tearing</label>
                                <input type="text" name="tearing_left" class="form-control">
                                <label>Lids</label>
                                <input type="text" name="lids_left" class="form-control">
                                <label>Conjunctiva</label>
                                <input type="text" name="conjunctiva_left" class="form-control">
                                <label>Cornea</label>
                                <input type="text" name="cornea_left" class="form-control">
                                <label>Anterior Chamber</label>
                                <input type="text" name="anterior_chamber_left" class="form-control">
                                <label>Iris</label>
                                <input type="text" name="iris_left" class="form-control">
                                <label>Pupil</label>
                                <input type="text" name="pupil_left" class="form-control">
                                <label>Lens</label>
                                <input type="text" name="lens_left" class="form-control">
                                <label>Fundus</label>
                                <input type="text" name="fundus_left" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Specialized Tests & Conclusion -->
            <div class="accordion-item">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" id="headingThree">
                    <h4>3. Specialized Tests & Conclusion</h4>
                </div>
                <div id="collapseThree" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Humphreys Visual Field</label>
                                <input type="text" name="hvf" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Colour Vision</label>
                                <input type="text" name="colour_vision" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Stereopsis</label>
                                <input type="text" name="stereopsis" class="form-control">
                            </div>
                        </div>
                        <div class="fw-bold mt-3 mb-2">Conclusion</div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Category</label>
                                <select name="category" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Mild Impairment">Mild Impairment</option>
                                    <option value="Moderate Impairment">Moderate Impairment</option>
                                    <option value="Severe Impairment">Severe Impairment</option>
                                    <option value="Blind">Blind</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Cause of Vision Loss</label>
                                <input type="text" name="cause_of_vision" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Percentage Disability</label>
                                <input type="text" name="percentage_disability" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Any Possible Intervention</label>
                                <select name="possible_intervention" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Recommendation</label>
                                <input type="text" name="recommendation" class="form-control">
                            </div>
                        </div>
                        <label>Upload Supporting Document</label>
                        <input type="file" name="supporting_document" class="form-control-file">
                        <label class="mt-2">Visual Disability Conclusion</label>
                        <select name="conclusion_duration" class="form-control" style="max-width:200px;">
                            <option value="">Select</option>
                            <option value="Temporary">Temporary</option>
                            <option value="Permanent">Permanent</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 mb-5">
            <button type="submit" class="btn btn-primary">Submit Assessment</button>
        </div>
    </form>
</div>
