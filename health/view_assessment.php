<?php

include 'files/header.php';
// include 'files/nav.php';

?>



<!-- navigation -->
<?php include 'files/sidebar.php';



if (!isset($_GET['user_id']) || !isset($_GET['type'])) {
    echo "<script>alert('User ID and Disability Type are required')</script>";
    exit;
}

$user_id = intval($_GET['user_id']);
$disability_type = $_GET['type'];
@$id = $_GET['id'];
?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approval'])) {
    // Get the form data
    // assessment_id=1&health_officer_id+=1&decision=approve&comment=
    $assessment_id = $_POST['assessment_id'];
    $health_officer_id = $_POST['health_officer_id'];
    $decision = $_POST['decision'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    // Validate input
    if ($decision === 'reject' && empty($comment)) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Rejection Reason Required',
                    text: 'Please provide a comment when rejecting the assessment.'
                });
              </script>";
        exit;
    }

    // Prepare the SQL query
    if ($decision === 'approve') {
        $status = 'approved_by_health_officer';
        $comment = ''; // No comment needed for approval
    } else {
        $status = 'rejected';
    }

    // Update the assessment status and health officer ID in the database
    $query = "UPDATE assessments SET status = ?, comment = ?, health_officer_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssii", $status, $comment, $health_officer_id, $assessment_id);

    if (mysqli_stmt_execute($stmt)) {
        // Success
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'The assessment has been updated.'
                }).then(() => {
                    window.location.href = 'complete_assessment'; // Redirect or reload
                });
              </script>";
    } else {
        // Error
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'There was an issue updating the assessment.'
                });
              </script>";
    }

    mysqli_stmt_close($stmt);

}
?>




<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>


            <!-- top navigation  -->
            <?php include 'files/nav.php'; ?>


            <!-- navigation -->
            <?php include 'files/sidebar.php'; ?>

            <!-- Main Content -->



            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Approval</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                            <div class="breadcrumb-item">Single Assessment</div>
                        </div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-8 col-md-8 col-lg-8 text-left">
                                <h2 class="section-title">Hi,
                                    <?php echo htmlspecialchars($pwdUser['name']); ?>!
                                </h2>
                                <p class="section-lead">View information about Assessment for Patient id(
                                    <?php echo $user_id; ?>)
                                    <!-- < ?php echo htmlspecialchars($data['user_name']); ?>. -->
                                </p>
                            </div>
                            <!-- <div class="col-md-4"></div> -->
                            <div class="col-md-4 text-right mt-4">
                                <!-- <a href="complete_assessment" class="btn btn-primary shadow-sm text-right approve-btn"
                                    data-id="123">
                                    << Approve </a> -->

                                <!-- <a class="btn btn-primary shadow-sm text-right approve-btn" data-id="123">
                                    << Approve </a>  -->

                                <?php if (isset($_GET['from']) && $_GET['from'] === 'assessment') { ?>
                                    <button class="btn btn-primary open-approval-modal approve-btn shadow-sm text-right"
                                        data-id="<?php echo $id; ?>">
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


        <!-- Modal -->
        <!-- Approval Modal -->
        <!-- Modal -->
        <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title" id="approvalModalLabel">Approval/Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="approvalForm" action="" method="POST">
                            <input type="text" class="form-control" name="assessment_id" id="assessment_id"
                                value="<?php echo $id; ?>" hidden>
                            <input type="hidden" name="health_officer_id" id="health_officer_id"
                                value="<?php echo $pwdUser['id'] ?>">
                            <div class="mb-3">
                                <label for="decision" class="form-label">Decision</label>
                                <select class="form-select form-control" id="decision" name="decision" required>
                                    <option value="">Select an option</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                </select>
                            </div>
                            <div class="mb-3" id="commentBox" class="d-none">
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


        <!-- Bootstrap Bundle + jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function () {
                // Show/hide comment box based on rejection decision
                $('#decision').on('change', function () {
                    if ($(this).val() === 'reject') {
                        $('#commentBox').removeClass('d-none');
                    } else {
                        $('#commentBox').addClass('d-none');
                        $('#comment').val('');
                    }
                });

                // Show modal and assign assessment ID
                $('.open-approval-modal').on('click', function () {
                    const assessmentId = $(this).data('id');
                    $('#assessment_id').val(assessmentId);
                    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                    modal.show();
                });
            });

        </script>


        <?php include 'files/footer.php'; ?>