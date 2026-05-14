<?php include 'files/header.php'; ?>

<body>
<div id="app">
<div class="main-wrapper main-wrapper-1">
<div class="navbar-bg"></div>
<?php include 'files/nav.php'; ?>
<?php include 'files/sidebar.php'; ?>

<?php
$hospitals = mysqli_fetch_all(mysqli_query($conn,
    "SELECT h.id, h.name, h.subcounty, h.address, c.county_name,
        COUNT(a.id) AS total,
        SUM(a.status='pending') AS pending,
        SUM(a.status='approved_by_county_officer') AS certified,
        COUNT(DISTINCT CASE WHEN o.type='medical_officer' AND o.active=1 THEN o.id END) AS medical_officers,
        COUNT(DISTINCT CASE WHEN o.type='health_officer' AND o.active=1 THEN o.id END) AS health_officers
     FROM hospitals h
     LEFT JOIN counties c ON h.county_id = c.id
     LEFT JOIN assessments a ON a.hospital_id = h.id
     LEFT JOIN officials o ON o.hospital_id = h.id
     GROUP BY h.id
     ORDER BY c.county_name, h.name"
), MYSQLI_ASSOC);
?>

<div class="main-content">
<section class="section">
    <div class="section-header">
        <h1>All Hospitals</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="index">Dashboard</a></div>
            <div class="breadcrumb-item active">Hospitals</div>
        </div>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-header"><h4>Hospital Directory — Nationwide</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="hospitalsTable">
                        <thead><tr>
                            <th>Hospital</th>
                            <th>County</th>
                            <th>Sub-County</th>
                            <th>Medical Officers</th>
                            <th>Health Officers</th>
                            <th>Total Assessments</th>
                            <th>Pending</th>
                            <th>Certified</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($hospitals as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['name']) ?></td>
                                <td><?= htmlspecialchars($h['county_name'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($h['subcounty'] ?? '—') ?></td>
                                <td><?= $h['medical_officers'] ?></td>
                                <td><?= $h['health_officers'] ?></td>
                                <td><?= $h['total'] ?></td>
                                <td><span class="badge badge-warning"><?= $h['pending'] ?></span></td>
                                <td><span class="badge badge-success"><?= $h['certified'] ?></span></td>
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

<script>$(document).ready(function(){ $('#hospitalsTable').DataTable({ pageLength: 20 }); });</script>

<?php include 'files/footer.php'; ?>
