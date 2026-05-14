<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$data = mysqli_fetch_all(mysqli_query($conn,
    "SELECT c.county_name,
        COUNT(DISTINCT h.id) AS hospitals,
        COUNT(DISTINCT CASE WHEN o.type='medical_officer' THEN o.id END) AS medical_officers,
        COUNT(DISTINCT CASE WHEN o.type='health_officer' THEN o.id END) AS health_officers,
        COUNT(a.id) AS total_assessments,
        SUM(a.status='pending') AS pending,
        SUM(a.status='checked') AS medical_done,
        SUM(a.status='approved_by_health_officer') AS health_approved,
        SUM(a.status='approved_by_county_officer') AS certified,
        SUM(a.status='rejected') AS rejected
     FROM counties c
     LEFT JOIN hospitals h ON h.county_id = c.id
     LEFT JOIN officials o ON o.county_id = c.id
     LEFT JOIN assessments a ON a.hospital_id = h.id
     GROUP BY c.id, c.county_name
     ORDER BY c.county_name"
), MYSQLI_ASSOC);
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>County Breakdown</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="index">Dashboard</a></div>
            <div class="breadcrumb-item active">County Breakdown</div>
        </div>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-header"><h4>All Counties — Assessment Summary</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="countiesTable">
                        <thead><tr>
                            <th>County</th>
                            <th>Hospitals</th>
                            <th>Medical Officers</th>
                            <th>Health Officers</th>
                            <th>Total Assessments</th>
                            <th>Pending</th>
                            <th>Medical Done</th>
                            <th>Health Approved</th>
                            <th>Certified</th>
                            <th>Rejected</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['county_name']) ?></td>
                                <td><?= $row['hospitals'] ?></td>
                                <td><?= $row['medical_officers'] ?></td>
                                <td><?= $row['health_officers'] ?></td>
                                <td><?= $row['total_assessments'] ?></td>
                                <td><span class="badge badge-warning"><?= $row['pending'] ?></span></td>
                                <td><span class="badge badge-info"><?= $row['medical_done'] ?></span></td>
                                <td><span class="badge badge-primary"><?= $row['health_approved'] ?></span></td>
                                <td><span class="badge badge-success"><?= $row['certified'] ?></span></td>
                                <td><span class="badge badge-danger"><?= $row['rejected'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script>$(document).ready(function(){ $('#countiesTable').DataTable({ pageLength: 20 }); });</script>

<?php include 'files/footer.php'; ?>
