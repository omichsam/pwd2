<?php
include 'files/header.php';
include '../files/db_connect.php';


// Handle activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['official_id'], $_POST['hospital_id'])) {
    $official_id = intval($_POST['official_id']);
    $hospital_id = intval($_POST['hospital_id']);

    $sql = "UPDATE officials SET hospital_id = ?, active = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $hospital_id, $official_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: Approve_officer.php?success=1");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch doctors
$sql = "SELECT * FROM officials WHERE active = 0 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Fetch hospitals
$hospitals = [];
$hospital_query = "SELECT id, name FROM hospitals";
$hospital_result = mysqli_query($conn, $hospital_query);
while ($row = mysqli_fetch_assoc($hospital_result)) {
    $hospitals[$row['id']] = $row['name'];
}
?>

<body>
    <style>
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 80px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .custom-modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .custom-modal-content h3 {
            margin-top: 0;
        }

        .close {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 24px;
            color: #888;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <?php include 'files/nav.php'; ?>
            <?php include 'files/sidebar.php'; ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Doctor Activation List</h1>
                    </div>

                    <div class="section-body">
                        <div class="card">
                            <div class="card-header">
                                <h4>List of Registered Doctors</h4>
                            </div>
                            <div class="card-body">
                                <!-- <table class="table table-bordered table-striped" id="doctorTable"> -->
                                <table class="table table-bordered table-striped" id="doctorTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>License ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile Number</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Hospital</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $modalId = "modal" . $row['id']; ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= htmlspecialchars($row['license_id']) ?></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                                                <td><?= htmlspecialchars($row['type']) ?></td>
                                                <td><span class="badge badge-warning">Inactive</span></td>
                                                <td><?= isset($hospitals[$row['hospital_id']]) ? $hospitals[$row['hospital_id']] : 'Not Assigned' ?>
                                                </td>
                                                <td><?= $row['created_at'] ?></td>
                                                <td><button onclick="openModal('<?= $modalId ?>')">Activate</button></td>
                                            </tr>

                                            <!-- Modal -->
                                            <div id="<?= $modalId ?>" class="custom-modal">
                                                <div class="custom-modal-content">
                                                    <span class="close"
                                                        onclick="closeModal('<?= $modalId ?>')">&times;</span>
                                                    <h3>Activate Doctor</h3>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="official_id" value="<?= $row['id'] ?>">
                                                        <label>Select Hospital:</label>
                                                        <select name="hospital_id" required>
                                                            <option value="" disabled selected>Select hospital</option>
                                                            <?php foreach ($hospitals as $id => $name): ?>
                                                                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <br><br>
                                                        <button type="submit">Activate</button>
                                                        <button type="button"
                                                            onclick="closeModal('<?= $modalId ?>')">Cancel</button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }
        window.onclick = function (event) {
            let modals = document.getElementsByClassName('custom-modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        };
    </script>

    <script>
        $(document).ready(function () {
            $('#doctorTable').DataTable({
                "pageLength": 5,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true
            });
        });
    </script>

    <?php include 'files/footer.php'; ?>