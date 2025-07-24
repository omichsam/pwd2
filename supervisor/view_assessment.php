<?php
include 'files/header.php';
include 'files/nav.php';
include 'files/sidebar.php';

// Start variables for SweetAlert logic
$showPopup = false;
$popupStatus = '';
$popupMessage = '';

// Form handling logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approval'])) {
    $assessment_id = $_POST['assessment_id'];
    $health_officer_id = $_POST['health_officer_id'];
    $decision = $_POST['decision'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    if ($decision === 'reject' && empty($comment)) {
        $showPopup = true;
        $popupStatus = 'warning';
        $popupMessage = 'Please provide a comment when rejecting the assessment.';
    } else {
        $status = ($decision === 'approve') ? 'approved_by_county_officer' : 'rejected';
        if ($decision === 'approve') $comment = '';

        $query = "UPDATE assessments SET status = ?, comment = ?, county_officer_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $status, $comment, $health_officer_id, $assessment_id);

        if (mysqli_stmt_execute($stmt)) {
            $showPopup = true;
            $popupStatus = 'success';
            $popupMessage = 'The assessment has been updated.';
        } else {
            $showPopup = true;
            $popupStatus = 'error';
            $popupMessage = 'There was an issue updating the assessment.';
        }

        mysqli_stmt_close($stmt);
    }
}

// Get user and view info
$user_id = intval($_GET['user_id']);
$disability_type = $_GET['type'];
@$id = $_GET['id'];

$sql = "SELECT name AS user_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data not found.";
    exit;
}
?>

<body>
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>

        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Approval</h1>
                </div>

                <div class="section-body">
                    <div class="row">
                        <div class="col-8">
                            <h2 class="section-title">Hi,
                                <?php echo htmlspecialchars($pwdUser['name']); ?>!
                            </h2>
                            <p class="section-lead">View information about Assessment for
                                <?php echo htmlspecialchars($data['user_name']); ?>.
                            </p>
                        </div>
                        <div class="col-md-4 text-right mt-4">
                            <?php if (isset($_GET['from']) && $_GET['from'] === 'assessment') { ?>
                                <button class="btn btn-primary open-approval-modal shadow-sm" data-id="<?= $id ?>">
                                    Approve/Reject Assessment
                                </button>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="row mt-sm-4">
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
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-light">
                    <h5 class="modal-title" id="approvalModalLabel">Approval/Reject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="approvalForm" method="POST">
                        <input type="hidden" name="assessment_id" value="<?= $id ?>">
                        <input type="hidden" name="health_officer_id" value="<?= $pwdUser['id'] ?>">
                        <div class="mb-3">
                            <label for="decision" class="form-label">Decision</label>
                            <select class="form-select form-control" id="decision" name="decision" required>
                                <option value="">Select an option</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="commentBox">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"
                                      placeholder="Provide a comment for rejection"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="approval">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#decision').on('change', function () {
                if ($(this).val() === 'reject') {
                    $('#commentBox').removeClass('d-none');
                } else {
                    $('#commentBox').addClass('d-none');
                    $('#comment').val('');
                }
            });

            $('.open-approval-modal').on('click', function () {
                const id = $(this).data('id');
                $('#assessment_id').val(id);
                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            });
        });
    </script>

    <!-- SweetAlert Logic -->
    <?php if ($showPopup): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $popupStatus; ?>',
                title: '<?php echo ucfirst($popupStatus); ?>',
                text: '<?php echo $popupMessage; ?>'
            }).then(() => {
                <?php if ($popupStatus === 'success'): ?>
                window.location.href = 'complete_assessment.php';
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>

    <?php include 'files/footer.php'; ?>
</div>
</body>
