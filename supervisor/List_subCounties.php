<?php
include 'files/header.php';
include '../files/db_connect.php';

// Get and validate county_id
$county_id = isset($_GET['county_id']) ? (int) $_GET['county_id'] : 0;

// Fetch county details with prepared statement
$county_query = "SELECT * FROM counties WHERE id = ?";
$stmt = mysqli_prepare($conn, $county_query);
mysqli_stmt_bind_param($stmt, "i", $county_id);
mysqli_stmt_execute($stmt);
$county_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($county_result) === 0) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'County Not Found',
            text: 'The requested county does not exist.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'counties.php';
        });
    </script>";
    exit;
}

$county = mysqli_fetch_assoc($county_result);

// Fetch sub-counties with pagination
$subcounty_query = "SELECT * FROM sub_county WHERE county_id = ? ORDER BY sub_county";
$stmt_subcounty = mysqli_prepare($conn, $subcounty_query);
mysqli_stmt_bind_param($stmt_subcounty, "i", $county_id);
mysqli_stmt_execute($stmt_subcounty);
$subcounty_result = mysqli_stmt_get_result($stmt_subcounty);
?>


    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
        }

        .btn-back {
            background: var(--light-color);
            color: var(--dark-color);
        }

        .btn-add {
            background: var(--success-color);
        }
    </style> 

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <?php include 'files/nav.php'; ?>
            <?php include 'files/sidebar.php'; ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>County Details</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item"><a href="counties.php">Counties</a></div>
                            <div class="breadcrumb-item active"><?php echo htmlspecialchars($county['county_name']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-12 col-md-8 col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($county['county_name']); ?>
                                            Sub-Counties
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <?php if (mysqli_num_rows($subcounty_result) > 0): ?>

                                            <div class="table-responsive text-center">

                                                <table class="table table-sm table-responsiv table-striped text-center" id="table-1">
                                                    <thead class="text-center">
                                                        <tr class="text-center">
                                                            <!-- <th>#</th> -->
                                                            <th class="text-center">ID</th>
                                                            <th class="text-center">Sub County</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($subcounty = mysqli_fetch_assoc($subcounty_result)): ?>
                                                            <tr>
                                                                <!-- <td>< ?php echo htmlspecialchars($subcounty['id']); ?></td> -->
                                                                <td><?php echo htmlspecialchars($subcounty['id']); ?></td>
                                                                <td><?php echo htmlspecialchars($subcounty['sub_county']); ?>
                                                                </td>

                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                                <!-- < ?php endif; ?> -->
                                            </div>

                                        <?php else: ?>
                                            <div class="empty-state">
                                                <i class="fas fa-map fa-3x"></i>
                                                <h4>No Sub-Counties Found</h4>
                                                <p>This county currently has no sub-counties registered.</p>
                                                <a href="add_subcounty.php?county_id=<?php echo $county_id; ?>"
                                                    class="btn btn-primary mt-3">
                                                    <i class="fas fa-plus"></i> Add Sub-County
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><i class="fas fa-info-circle"></i> County Information</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h6>County Code</h6>
                                            <p><?php echo htmlspecialchars($county['id']); ?></p>
                                        </div>
                                        <div class="mb-4">
                                            <h6>Total Sub-Counties</h6>
                                            <p><?php echo mysqli_num_rows($subcounty_result); ?></p>
                                        </div>
                                        <div>
                                            <h6>Last Updated</h6>
                                            <p><?php echo date('M d, Y', strtotime($county['updated_at'])); ?></p>
                                        </div>

                                        <div class="action-buttons">
                                            <a href="List_Counties.php" class="btn btn-dark">
                                                <i class="fas fa-arrow-left"></i> Back to Counties
                                            </a>
                                            <a href="add_subcounty.php?county_id=<?php echo $county_id; ?>"
                                                class="btn btn-success">
                                                <i class="fas fa-plus"></i> Add Sub-County
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle any status messages from previous actions
        <?php if (isset($_GET['status'])): ?>
            const status = '<?php echo $_GET["status"]; ?>';
            const message = status === 'success'
                ? 'Operation completed successfully!'
                : 'An error occurred. Please try again.';

            Swal.fire({
                icon: status,
                title: message,
                showConfirmButton: false,
                timer: 2000
            });
        <?php endif; ?>
    </script>

    <?php include 'files/footer.php'; ?>