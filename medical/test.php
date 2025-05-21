<?php
session_start();

// Dummy session values for testing (ensure real login sets these)
$_SESSION['id'] = 2;
$_SESSION['type'] = 'health-officer';

$official_id = $_SESSION['id'];
$official_type = $_SESSION['type'];

$host = 'localhost';
$db = 'pwd';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get hospital_id of logged-in official
$hospital_id = null;
$official_sql = "SELECT hospital_id FROM officials WHERE id = $official_id";
$official_result = mysqli_query($conn, $official_sql);
if ($official_row = mysqli_fetch_assoc($official_result)) {
    $hospital_id = intval($official_row['hospital_id']);
} else {
    die("Could not find hospital for official ID $official_id.");
}

// Handle validation action
if (isset($_GET['validate_id'])) {
    $validate_id = intval($_GET['validate_id']);
    $update_sql = "UPDATE assessments SET status = 'validated' WHERE id = $validate_id";
    mysqli_query($conn, $update_sql);
    header("Location: test.php");
    exit;
}

echo $hospital_id;

// ✅ Now build the query
$sql = "SELECT 
            a.id AS assessment_id,
            u.name AS user_name,
            u.gender,
            u.dob,
            u.id_number,
            u.mobile_number,
            u.email,
            u.county,
            u.subcounty,
            a.disability_type,
            a.assessment_date,
            a.assessment_time,
            a.status
        FROM assessments a
        JOIN users u ON a.user_id = u.id
        WHERE  a.hospital_id = $hospital_id";

        // WHERE a.status = 'pending' AND a.hospital_id = $hospital_id";

// ✅ Debug check
echo "<pre>Running SQL:\n$sql</pre>";

// ✅ Finally run the query
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Unvalidated Assessments</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #eee;
        }

        .btn {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <h2>Unvalidated Users (<?= ucfirst($official_type) ?> Officer View)</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Assessment ID</th>
                    <th>User Name</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>ID Number</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>County</th>
                    <th>Subcounty</th>
                    <th>Disability Type</th>
                    <th>Assessment Date</th>
                    <th>Assessment Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['assessment_id']) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['dob']) ?></td>
                        <td><?= htmlspecialchars($row['id_number']) ?></td>
                        <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['county']) ?></td>
                        <td><?= htmlspecialchars($row['subcounty']) ?></td>
                        <td><?= htmlspecialchars($row['disability_type']) ?></td>
                        <td><?= htmlspecialchars($row['assessment_date']) ?></td>
                        <td><?= htmlspecialchars($row['assessment_time']) ?></td>
                        <td>Unvalidated</td>
                        <td>
                            <form method="get" action="unvalidated_assessments.php">
                                <input type="hidden" name="validate_id" value="<?= $row['assessment_id'] ?>">
                                <button type="submit" class="btn">Validate</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No unvalidated users found for your hospital.</p>
    <?php endif; ?>

</body>

</html>

<?php
mysqli_close($conn);
?>