<?php
include 'files/header.php';
include 'files/nav.php';
include 'files/sidebar.php';

if (!isset($_GET['user_id']) || !isset($_GET['type'])) {
    echo "User ID and Disability Type are required.";
    exit;
}

$user_id = intval($_GET['user_id']);
$disability_type = $_GET['type'];

?>

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Approval</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Assessment</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="section-title">Hi, <?php echo htmlspecialchars($pwdUser['name']); ?>!</h2>
                    <p class="section-lead">View assessment for the selected user.</p>
                </div>
                <div class="col-md-4 text-right mt-4">
                    <a href="complete_assessment" class="btn btn-primary">&laquo; Back</a>
                </div>
            </div>

            <!-- Assessment content -->
            <div class="row mt-4">
                <div class="col-12">
                    <?php
                    $partial_file = 'partials/' . strtolower($disability_type) . '_view.php';
                    if (file_exists($partial_file)) {
                        include $partial_file;
                    } else {
                        echo "<div class='alert alert-warning'>View not available for this assessment type.</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'files/footer.php'; ?>
