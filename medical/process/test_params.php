<?php 
@$params = [
    $assessment_id, $user_id, $onset_date, $last_intervention_date, $interventions,
    $cause_of_disability, $structural_impairments, $regions_affected,
    $score_cardiovascular, $remark_cardiovascular, $score_respiratory, $remark_respiratory,
    $score_cancer, $remark_cancer, $score_musculoskeletal, $remark_musculoskeletal,
    $score_neurological, $remark_neurological, $score_gastrointestinal, $remark_gastrointestinal,
    $score_dermatological, $remark_dermatological, $score_hematologic, $remark_hematologic,
    $score_lymphatic, $remark_lymphatic, $score_genitourinary, $remark_genitourinary,
    $score_frailty, $remark_frailty, $score_other, $remark_other, $difficulty_mobility,
    $remark_mobility, $difficulty_selfcare, $remark_selfcare, $difficulty_domestic,
    $remark_domestic, $difficulty_majorlife, $remark_majorlife, $difficulty_community,
    $remark_community, $disability_rating, $recommended_assistive_products, $other_services_required,
    $conclusion_decision, $file_path
];

echo "Number of parameters: " . count($params); // Should be 46
echo "Number of type characters: " . strlen("iisssssssssssssssssssssssssssssssssssssssssssss"); // Should be 46
echo "count questions marks" .strlen("???????????????????????????????????????????????");