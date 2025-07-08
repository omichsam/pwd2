<form method="post" enctype="multipart/form-data" class="needs-validation p-2" novalidate>
    <div class="accordion" id="assessmentAccordion">

        <!-- 1. Clinical History -->
        <div class="card mb-2">
            <div class="card-header py-2">
                <h6 class="mb-0">
                    <button class="btn btn-link btn-sm" type="button" data-toggle="collapse"
                        data-target="#collapseHistory">
                        1. Brief Clinical History
                    </button>
                </h6>
            </div>
            <div id="collapseHistory" class="collapse show" data-parent="#assessmentAccordion">
                <div class="card-body p-2">
                    <textarea class="form-control form-control-sm" name="brief_clinical_history" rows="2"
                        placeholder="Medical history..." required></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Mental Status -->
        <div class="card mb-2">
            <div class="card-header py-2">
                <h6 class="mb-0">
                    <button class="btn btn-link btn-sm collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseEvaluation">
                        2. Mental Status Evaluation
                    </button>
                </h6>
            </div>
            <div id="collapseEvaluation" class="collapse" data-parent="#assessmentAccordion">
                <div class="card-body p-2">
                    <textarea class="form-control form-control-sm" name="mental_status_evaluation" rows="2"
                        placeholder="Mental status..."></textarea>
                </div>
            </div>
        </div>

        <!-- 3. Functional Scores -->
        <div class="card mb-2">
            <div class="card-header py-2">
                <h6 class="mb-0">
                    <button class="btn btn-link btn-sm collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseScores">
                        3. Functional Scores
                    </button>
                </h6>
            </div>
            <div id="collapseScores" class="collapse" data-parent="#assessmentAccordion">
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Feeding</label>
                            <select class="form-control form-control-sm" name="feeding_score">
                                <option value="0">0 - Independent</option>
                                <option value="1">1 - Partial</option>
                                <option value="2">2 - Minimal</option>
                                <option value="3">3 - Dependent</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Toileting</label>
                            <select class="form-control form-control-sm" name="toileting_score">
                                <option value="0">0 - Independent</option>
                                <option value="1">1 - Partial</option>
                                <option value="2">2 - Minimal</option>
                                <option value="3">3 - Dependent</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Grooming</label>
                            <select class="form-control form-control-sm" name="grooming_score">
                                <option value="0">0 - Independent</option>
                                <option value="1">1 - Partial</option>
                                <option value="2">2 - Minimal</option>
                                <option value="3">3 - Dependent</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Dependence on Others</label>
                            <input type="text" class="form-control form-control-sm" name="dependence_on_others">
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Psychosocial Adaptability</label>
                            <input type="text" class="form-control form-control-sm" name="psychosocial_adaptability">
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Level of Functioning</label>
                            <input type="text" class="form-control form-control-sm" name="level_of_functioning">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Total Score</label>
                        <input type="text" class="form-control form-control-sm" name="total_score" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Conclusion -->
        <div class="card mb-2">
            <div class="card-header py-2">
                <h6 class="mb-0">
                    <button class="btn btn-link btn-sm collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseConclusion">
                        4. Conclusion & Recommendations
                    </button>
                </h6>
            </div>
            <div id="collapseConclusion" class="collapse" data-parent="#assessmentAccordion">
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Duration of Illness</label>
                            <input type="text" class="form-control form-control-sm" name="duration_of_illness">
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label class="small">Major Cause</label>
                            <input type="text" class="form-control form-control-sm" name="major_cause_of_disability">
                        </div>