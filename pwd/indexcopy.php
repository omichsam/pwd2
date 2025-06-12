<?php include 'files/header.php';

// Access user data from session
$user_id = $pwdUser['id'] ?? null;
$user_county_id = $pwdUser['county_id'] ?? null;
$user_type = $pwdUser['type'] ?? 'user';

// Function to safely fetch data with proper error handling
function fetchData($conn, $sql, $params = [], $single = false) {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return null;
    }
    
    if (!empty($params)) {
        $types = '';
        $bind_params = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $bind_params[] = $param;
        }
        
        array_unshift($bind_params, $types);
        
        if (!call_user_func_array([$stmt, 'bind_param'], refValues($bind_params))) {
            error_log("Bind param failed: " . mysqli_error($conn));
            mysqli_stmt_close($stmt);
            return null;
        }
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return null;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        error_log("Get result failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return null;
    }
    
    if ($single) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    return $data;
}

function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

// Fetch user-specific data
$user_profile = [];
$assessments = [];
$assessment_stats = [
    'total' => 0,
    'approved' => 0,
    'pending' => 0,
    'rejected' => 0,
    'latest_status' => 'No assessments yet'
];

if ($user_id) {
    $user_profile = fetchData($conn, 
        "SELECT u.*, c.county_name, c.id as county_id 
         FROM users u 
         LEFT JOIN counties c ON u.county_id = c.id 
         WHERE u.id = ?", 
        [$user_id], true);

    // Update county_id from profile if available
    if ($user_profile && isset($user_profile['county_id'])) {
        $user_county_id = $user_profile['county_id'];
    }

    $assessments = fetchData($conn,
        "SELECT a.*, h.name as hospital_name, 
                c.county_name as hospital_county,
                mo.name as medical_officer_name,
                ho.name as health_officer_name,
                co.name as county_officer_name
         FROM assessments a
         LEFT JOIN hospitals h ON a.hospital_id = h.id
         LEFT JOIN counties c ON h.county_id = c.id
         LEFT JOIN officials mo ON a.medical_officer_id = mo.id
         LEFT JOIN officials ho ON a.health_officer_id = ho.id
         LEFT JOIN officials co ON a.county_officer_id = co.id
         WHERE a.user_id = ?
         ORDER BY a.assessment_date DESC",
        [$user_id]);

    if ($assessments) {
        $assessment_stats['total'] = count($assessments);
        foreach ($assessments as $assessment) {
            if ($assessment['status'] == 'approved_by_county_officer') {
                $assessment_stats['approved']++;
            } elseif ($assessment['status'] == 'rejected') {
                $assessment_stats['rejected']++;
            } else {
                $assessment_stats['pending']++;
            }
        }
        $assessment_stats['latest_status'] = $assessments[0]['status'];
    }
}

// Enhanced County Hospital Data
$county_data = [];
$county_hospitals = [];
$hospital_stats = [];

if ($user_county_id) {
    // Get basic county info
    $county_data = fetchData($conn,
        "SELECT c.*, 
                (SELECT COUNT(*) FROM hospitals WHERE county_id = c.id) as hospital_count,
                (SELECT COUNT(*) FROM officials WHERE county_id = c.id) as official_count
         FROM counties c
         WHERE c.id = ?",
        [$user_county_id], true);

    // Get detailed hospital info with assessment stats
    $county_hospitals = fetchData($conn,
        "SELECT h.*, 
                (SELECT COUNT(*) FROM assessments WHERE hospital_id = h.id AND status = 'approved_by_county_officer') as approved_assessments,
                (SELECT COUNT(*) FROM assessments WHERE hospital_id = h.id) as total_assessments
         FROM hospitals h
         WHERE h.county_id = ?
         ORDER BY h.name ASC",
        [$user_county_id]);

    // Calculate county-wide hospital statistics
    if ($county_hospitals) {
        $hospital_stats = [
            'total_hospitals' => count($county_hospitals),
            'total_assessments' => 0,
            'approved_assessments' => 0,
            'busiest_hospital' => null,
            'most_approved_hospital' => null
        ];

        $max_assessments = 0;
        $max_approvals = 0;

        foreach ($county_hospitals as $hospital) {
            $hospital_stats['total_assessments'] += $hospital['total_assessments'];
            $hospital_stats['approved_assessments'] += $hospital['approved_assessments'];

            if ($hospital['total_assessments'] > $max_assessments) {
                $max_assessments = $hospital['total_assessments'];
                $hospital_stats['busiest_hospital'] = $hospital['name'];
            }

            if ($hospital['approved_assessments'] > $max_approvals) {
                $max_approvals = $hospital['approved_assessments'];
                $hospital_stats['most_approved_hospital'] = $hospital['name'];
            }
        }
    }
}

// Calculate completion percentage
$completion_percentage = 0;
if (!empty($assessments)) {
    switch ($assessments[0]['status']) {
        case 'approved_by_county_officer': $completion_percentage = 100; break;
        case 'approved_by_health_officer': $completion_percentage = 75; break;
        case 'checked': $completion_percentage = 50; break;
        case 'pending': $completion_percentage = 25; break;
    }
}

// Gamification elements
$badges = [];
$user_level = 1;
$user_points = $assessment_stats['total'] * 10;

if ($assessment_stats['total'] > 0) {
    $badges[] = ['name' => 'First Assessment', 'icon' => 'fa-star'];
}
if ($assessment_stats['total'] >= 3) {
    $badges[] = ['name' => 'Frequent User', 'icon' => 'fa-award'];
    $user_level = 2;
}
if ($assessment_stats['approved'] > 0) {
    $badges[] = ['name' => 'Certified', 'icon' => 'fa-check-circle'];
    $user_level = 3;
    $user_points += 50;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Disability Assessment System</title>
    
    <!-- General CSS Files -->
    <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
    <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons.min.css">
    <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons-wind.min.css">
    <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components.css">

    <!-- SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
    
    <style>
        .hospital-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }
        .hospital-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .hospital-icon {
            font-size: 2rem;
            color: #6777ef;
            margin-bottom: 10px;
        }
        .hospital-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-weight: bold;
            font-size: 1.2rem;
        }
        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .county-stats-card {
            background: linear-gradient(135deg, #6777ef 0%, #5a67d8 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .county-stats-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .county-stats-value {
            font-size: 2rem;
            font-weight: 700;
        }
        .county-stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>

            <!-- Top Navigation -->
            <?php include 'files/nav.php'; ?>

            <!-- Sidebar -->
            <?php include 'files/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <!-- Dashboard Header with User Profile -->
                    <div class="section-header">
                        <h1>Dashboard</h1>
                        <div class="section-header-breadcrumb">
                            <div class="badge badge-primary">
                                <i class="fas fa-level-up-alt"></i> Level <?= $user_level ?>
                            </div>
                            <div class="badge badge-success ml-2">
                                <i class="fas fa-coins"></i> <?= $user_points ?> Points
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Summary -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card profile-widget">
                                <div class="profile-widget-header">
                                    <div class="profile-widget-items">
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label">Assessments</div>
                                            <div class="profile-widget-item-value"><?= $assessment_stats['total'] ?></div>
                                        </div>
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label">Approved</div>
                                            <div class="profile-widget-item-value"><?= $assessment_stats['approved'] ?></div>
                                        </div>
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label">County</div>
                                            <div class="profile-widget-item-value">
                                                <?= htmlspecialchars($user_profile['county_name'] ?? 'Not specified') ?>
                                            </div>
                                        </div>
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label">Hospitals</div>
                                            <div class="profile-widget-item-value">
                                                <?= $county_data['hospital_count'] ?? '0' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="profile-widget-description">
                                    <div class="profile-widget-name">
                                        <?= htmlspecialchars($user_profile['name'] ?? 'User') ?>
                                        <div class="text-muted d-inline font-weight-normal ml-2">
                                            (@<?= htmlspecialchars($user_profile['id_number'] ?? 'user') ?>)
                                        </div>
                                    </div>
                                    <?php if (!empty($badges)): ?>
                                        <div class="badge-section mt-2">
                                            <?php foreach ($badges as $badge): ?>
                                                <div class="badge badge-info mr-1">
                                                    <i class="fas <?= $badge['icon'] ?> mr-1"></i>
                                                    <?= $badge['name'] ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Dashboard Content -->
                    <div class="row">
                        <!-- Assessment Progress -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Assessment Progress</h4>
                                    <?php if ($assessment_stats['total'] > 0): ?>
                                        <div class="card-header-action">
                                            <a href="assessments.php" class="btn btn-sm btn-outline-primary">
                                                View All <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if ($assessment_stats['total'] > 0): ?>
                                        <div class="progress-container mb-4">
                                            <div class="progress-info">
                                                <h6 class="progress-label">
                                                    Current Status: <?= ucwords(str_replace('_', ' ', $assessments[0]['status'])) ?>
                                                </h6>
                                                <h6 class="progress-percentage"><?= $completion_percentage ?>%</h6>
                                            </div>
                                            <div class="progress" data-height="10">
                                                <div class="progress-bar" data-width="<?= $completion_percentage ?>%"></div>
                                            </div>
                                        </div>

                                        <ul class="list-unstyled list-unstyled-border">
                                            <li class="media">
                                                <div class="media-icon">
                                                    <i class="far fa-file-alt"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6>Application</h6>
                                                    <div class="text-small text-muted">
                                                        <?= date('M d, Y', strtotime($assessments[0]['created_at'])) ?>
                                                    </div>
                                                </div>
                                                <div class="media-cta">
                                                    <?php if ($assessments[0]['status'] == 'pending'): ?>
                                                        <span class="badge badge-warning">Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Completed</span>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-icon">
                                                    <i class="fas fa-user-md"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6>Medical Evaluation</h6>
                                                    <div class="text-small text-muted">
                                                        <?= $assessments[0]['medical_officer_name'] ?? 'Not assigned' ?>
                                                    </div>
                                                </div>
                                                <div class="media-cta">
                                                    <?php if (in_array($assessments[0]['status'], ['checked', 'approved_by_health_officer', 'approved_by_county_officer'])): ?>
                                                        <span class="badge badge-success">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-icon">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6>Health Officer Review</h6>
                                                    <div class="text-small text-muted">
                                                        <?= $assessments[0]['health_officer_name'] ?? 'Not assigned' ?>
                                                    </div>
                                                </div>
                                                <div class="media-cta">
                                                    <?php if (in_array($assessments[0]['status'], ['approved_by_health_officer', 'approved_by_county_officer'])): ?>
                                                        <span class="badge badge-success">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-icon">
                                                    <i class="fas fa-stamp"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6>County Approval</h6>
                                                    <div class="text-small text-muted">
                                                        <?= $assessments[0]['county_officer_name'] ?? 'Not assigned' ?>
                                                    </div>
                                                </div>
                                                <div class="media-cta">
                                                    <?php if ($assessments[0]['status'] == 'approved_by_county_officer'): ?>
                                                        <span class="badge badge-success">Approved</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        </ul>
                                    <?php else: ?>
                                        <div class="empty-state" data-height="300">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-file-medical"></i>
                                            </div>
                                            <h2>No Assessments Found</h2>
                                            <p class="lead">
                                                You haven't started any disability assessments yet.
                                            </p>
                                            <a href="new_assessment.php" class="btn btn-primary mt-4">
                                                Start New Assessment
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- County Information and Hospitals -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>County Information</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($county_data)): ?>
                                        <div class="county-stats-card">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="county-stats-title">Hospitals in County</div>
                                                    <div class="county-stats-value"><?= $county_data['hospital_count'] ?></div>
                                                    <div class="county-stats-label">Total assessment centers</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="county-stats-title">Medical Officials</div>
                                                    <div class="county-stats-value"><?= $county_data['official_count'] ?></div>
                                                    <div class="county-stats-label">Available professionals</div>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="mt-4">Hospital Statistics</h5>
                                        <?php if (!empty($hospital_stats)): ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card card-statistic-1">
                                                        <div class="card-icon bg-primary">
                                                            <i class="fas fa-procedures"></i>
                                                        </div>
                                                        <div class="card-wrap">
                                                            <div class="card-header">
                                                                <h4>Total Assessments</h4>
                                                            </div>
                                                            <div class="card-body">
                                                                <?= $hospital_stats['total_assessments'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-statistic-1">
                                                        <div class="card-icon bg-success">
                                                            <i class="fas fa-check-circle"></i>
                                                        </div>
                                                        <div class="card-wrap">
                                                            <div class="card-header">
                                                                <h4>Approved</h4>
                                                            </div>
                                                            <div class="card-body">
                                                                <?= $hospital_stats['approved_assessments'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <h5 class="mt-4">Hospitals in <?= htmlspecialchars($user_profile['county_name'] ?? 'Your County') ?></h5>
                                        <?php if (!empty($county_hospitals)): ?>
                                            <div class="owl-carousel owl-theme" id="hospitals-carousel">
                                                <?php foreach ($county_hospitals as $hospital): ?>
                                                    <div class="item">
                                                        <div class="card hospital-card">
                                                            <div class="card-body text-center">
                                                                <div class="hospital-icon">
                                                                    <i class="fas fa-hospital"></i>
                                                                </div>
                                                                <h5><?= htmlspecialchars($hospital['name']) ?></h5>
                                                                <p class="text-muted"><?= htmlspecialchars($hospital['address']) ?></p>
                                                                
                                                                <div class="hospital-stats">
                                                                    <div class="stat-item">
                                                                        <div class="stat-value"><?= $hospital['total_assessments'] ?></div>
                                                                        <div class="stat-label">Assessments</div>
                                                                    </div>
                                                                    <div class="stat-item">
                                                                        <div class="stat-value"><?= $hospital['approved_assessments'] ?></div>
                                                                        <div class="stat-label">Approved</div>
                                                                    </div>
                                                                    <div class="stat-item">
                                                                        <div class="stat-value">
                                                                            <?= $hospital['total_assessments'] > 0 ? 
                                                                                round(($hospital['approved_assessments'] / $hospital['total_assessments']) * 100) : 0 ?>%
                                                                        </div>
                                                                        <div class="stat-label">Success Rate</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <a href="hospitals.php?county=<?= $user_county_id ?>" class="btn btn-outline-primary btn-block mt-3">
                                                View All Hospitals in County
                                            </a>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                No hospitals found in your county
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            County information not available. Please update your profile.
                                        </div>
                                        <a href="profile.php" class="btn btn-primary btn-block">
                                            Update Profile
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity and Quick Actions -->
                    <div class="row">
                        <!-- Recent Activity -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Recent Activity</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($assessments)): ?>
                                        <ul class="list-unstyled user-progress list-unstyled-border">
                                            <?php foreach (array_slice($assessments, 0, 3) as $assessment): ?>
                                                <li class="media">
                                                    <div class="media-icon">
                                                        <?php switch($assessment['status']) {
                                                            case 'approved_by_county_officer': 
                                                                echo '<i class="fas fa-check-circle text-success"></i>'; 
                                                                break;
                                                            case 'rejected': 
                                                                echo '<i class="fas fa-times-circle text-danger"></i>'; 
                                                                break;
                                                            default: 
                                                                echo '<i class="fas fa-clock text-warning"></i>';
                                                        } ?>
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="media-title">
                                                            Assessment #<?= $assessment['id'] ?>
                                                            <span class="text-small text-muted">
                                                                at <?= htmlspecialchars($assessment['hospital_name'] ?? 'Unknown Hospital') ?>
                                                            </span>
                                                        </div>
                                                        <div class="text-small text-muted">
                                                            <?= date('M d, Y', strtotime($assessment['assessment_date'])) ?>
                                                            <div class="bullet"></div>
                                                            <?= ucwords(str_replace('_', ' ', $assessment['status'])) ?>
                                                        </div>
                                                    </div>
                                                    <div class="media-cta">
                                                        <a href="assessment_details.php?id=<?= $assessment['id'] ?>" 
                                                           class="btn btn-outline-primary">Details</a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <a href="assessments.php" class="btn btn-outline-secondary btn-block">
                                            View All Activities
                                        </a>
                                    <?php else: ?>
                                        <div class="empty-state" data-height="200">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-history"></i>
                                            </div>
                                            <h2>No Recent Activity</h2>
                                            <p class="lead">
                                                Your activities will appear here once you start assessments.
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Quick Actions</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row quick-actions">
                                        <div class="col-6 col-md-12 col-lg-6">
                                            <a href="new_assessment.php" class="quick-action-item">
                                                <div class="quick-action-icon bg-primary">
                                                    <i class="fas fa-file-medical"></i>
                                                </div>
                                                <h6>New Assessment</h6>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-12 col-lg-6">
                                            <a href="hospitals.php" class="quick-action-item">
                                                <div class="quick-action-icon bg-info">
                                                    <i class="fas fa-hospital"></i>
                                                </div>
                                                <h6>Find Hospitals</h6>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-12 col-lg-6">
                                            <a href="profile.php" class="quick-action-item">
                                                <div class="quick-action-icon bg-warning">
                                                    <i class="fas fa-user-edit"></i>
                                                </div>
                                                <h6>Update Profile</h6>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-12 col-lg-6">
                                            <a href="documents.php" class="quick-action-item">
                                                <div class="quick-action-icon bg-success">
                                                    <i class="fas fa-file-upload"></i>
                                                </div>
                                                <h6>Upload Documents</h6>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Summary -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h4>Your Progress</h4>
                                </div>
                                <div class="card-body">
                                    <div class="progress-summary">
                                        <div class="progress-item">
                                            <div class="progress-info">
                                                <h6>Completed Assessments</h6>
                                                <h6><?= $assessment_stats['approved'] ?></h6>
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-success" 
                                                     data-width="<?= $assessment_stats['total'] > 0 ? ($assessment_stats['approved'] / $assessment_stats['total'] * 100) : 0 ?>%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-info">
                                                <h6>Pending Assessments</h6>
                                                <h6><?= $assessment_stats['pending'] ?></h6>
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-warning" 
                                                     data-width="<?= $assessment_stats['total'] > 0 ? ($assessment_stats['pending'] / $assessment_stats['total'] * 100) : 0 ?>%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-info">
                                                <h6>Rejected Assessments</h6>
                                                <h6><?= $assessment_stats['rejected'] ?></h6>
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger" 
                                                     data-width="<?= $assessment_stats['total'] > 0 ? ($assessment_stats['rejected'] / $assessment_stats['total'] * 100) : 0 ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <?php include 'files/footer.php'; ?>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="../assets/modules/jquery.min.js"></script>
    <script src="../assets/modules/popper.js"></script>
    <script src="../assets/modules/tooltip.js"></script>
    <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="../assets/modules/moment.min.js"></script>
    <script src="../assets/js/stisla.js"></script>
    
    <!-- JS Libraries -->
    <script src="../assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
    <script src="../assets/modules/sweetalert/sweetalert.min.js"></script>
    
    <!-- Template JS File -->
    <script src="../assets/js/scripts.js"></script>
    <script src="../assets/js/custom.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.min.js"></script>

    <script>
        // Initialize hospital carousel
        $(document).ready(function() {
            $('#hospitals-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: false,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 1 }
                }
            });
        });
    </script>
</body>
</html>