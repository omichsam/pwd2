<?php
include 'files/header.php';
include '../files/db_connect.php';

// Fetch activated users
$sql = "SELECT * FROM officials WHERE active = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<style>
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        padding-top: 80px;
    }

    .custom-modal-content {
        background: #fff;
        margin: auto;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        position: relative;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .custom-modal-content h3 {
        margin-top: 0;
    }

    .close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        color: #888;
        cursor: pointer;
    }

    .close:hover {
        color: #000;
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
                        <h1>Activated Members</h1>
                    </div>

                    <div class="section-body">
                        <div class="card">
                            <div class="card-header">
                                <h4>Registered & Activated Users</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped" id="memberTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>License ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Type</th>
                                            <th>Hospital</th>
                                            <th>Joined On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $modalId = "viewModal" . $row['id'];
                                            ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= htmlspecialchars($row['license_id']) ?></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                                                <td><?= htmlspecialchars($row['type']) ?></td>
                                                <td>
                                                    <?php
                                                    $hid = $row['hospital_id'];
                                                    $hquery = mysqli_query($conn, "SELECT name FROM hospitals WHERE id = $hid");
                                                    $hname = ($hr = mysqli_fetch_assoc($hquery)) ? $hr['name'] : 'Not Found';
                                                    echo htmlspecialchars($hname);
                                                    ?>
                                                </td>
                                                <td><?= $row['created_at'] ?></td>
                                                <td><button onclick="openModal('<?= $modalId ?>')">View</button></td>
                                            </tr>

                                            <!-- Modal -->
                                            <div id="<?= $modalId ?>" class="custom-modal">
                                                <div class="custom-modal-content">
                                                    <span class="close"
                                                        onclick="closeModal('<?= $modalId ?>')">&times;</span>
                                                    <h3>User Details</h3>
                                                    <p><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></p>
                                                    <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                                                    <p><strong>Mobile:</strong>
                                                        <?= htmlspecialchars($row['mobile_number']) ?></p>
                                                    <p><strong>License ID:</strong>
                                                        <?= htmlspecialchars($row['license_id']) ?></p>
                                                    <p><strong>Type:</strong> <?= htmlspecialchars($row['type']) ?></p>
                                                    <p><strong>Hospital:</strong> <?= htmlspecialchars($hname) ?></p>
                                                    <p><strong>Status:</strong> Active</p>
                                                    <p><strong>Joined On:</strong>
                                                        <?= htmlspecialchars($row['created_at']) ?></p>
                                                    <button onclick="closeModal('<?= $modalId ?>')">Close</button>
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
            const modals = document.getElementsByClassName("custom-modal");
            for (let modal of modals) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            }
        };
    </script>

    <script>
        $(document).ready(function () {
            $('#memberTable').DataTable({
                "pageLength": 5,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true
            });
        });
    </script>


    <?php include 'files/footer.php'; ?>