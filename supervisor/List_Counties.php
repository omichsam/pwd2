<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'files/header.php';
include '../files/db_connect.php'; // Ensure you have a valid connection

// Check if the request is to fetch sub-counties based on county_id
if (isset($_GET['county_id'])) {
  $county_id = (int) $_GET['county_id']; // Sanitize input to avoid SQL injection

  // Query to fetch sub-counties for the selected county
  $subcounty_query = "SELECT * FROM sub_county WHERE county_id = ?";

  // Prepare the statement and bind parameters
  if ($stmt = mysqli_prepare($conn, $subcounty_query)) {
    mysqli_stmt_bind_param($stmt, "i", $county_id); // Bind county_id as integer
    mysqli_stmt_execute($stmt);
    $subcounty_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($subcounty_result) > 0) {
      $sub_counties = mysqli_fetch_all($subcounty_result, MYSQLI_ASSOC);
      echo json_encode(['status' => 'success', 'sub_counties' => $sub_counties]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'No sub-counties found for this county.']);
    }

    mysqli_stmt_close($stmt);
  } else {
    // Log and return SQL errors
    error_log("SQL Error: " . mysqli_error($conn));
    echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
  }
  exit;
} else {
  echo json_encode(['status' => 'error', 'message' => 'No county_id provided.']);
}
?>




<body>

  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <!-- Top Navigation -->
      <?php include 'files/nav.php'; ?>

      <!-- Sidebar Navigation -->
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Counties</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="#">County List</a></div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Registered Counties</h2>
            <p class="section-lead">View and manage registered Counties.</p>

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>All Counties</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>County Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $query = "SELECT * FROM counties";
                          $result = mysqli_query($conn, $query);
                          $count = 1;
                          while ($row = mysqli_fetch_assoc($result)) {
                            // Escape PHP output in JavaScript string correctly
                            echo "<tr>
                                      <td>{$count}</td>
                                      <td>{$row['county_name']}</td>
                                      <td>{$row['created_at']}</td>
                                      <td>
                                          <button class='btn btn-sm btn-info' onclick='viewCounty(" . json_encode($row) . ")'>
                                              <i class=\"fas fa-eye\"></i> View
                                          </button>
                                          <!-- Button to View Sub Counties with Redirect -->
                                          <button class='btn btn-sm btn-danger' onclick='window.location.href = \"List_subCounties?county_id=" . $row['id'] . "\"'>
                                              <i class=\"fas fa-eye\"></i> View Sub Counties
                                          </button>
                                          
                                          <button class='btn btn-sm btn-primary' onclick='editCounty(" . json_encode($row) . ")'>
                                              <i class=\"fas fa-edit\"></i> Edit
                                          </button>
                                      </td>
                                  </tr>";
                            $count++;
                          }
                          ?>

                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>
      </div>

      <!-- JavaScript for SweetAlert Popups and Fetching Sub-Counties -->
      <script>
        function viewCounty(county) {
          Swal.fire({
            title: `<strong>County: ${county.county_name}</strong>`,
            html: `<p><strong>Created At:</strong> ${county.created_at}</p>`,
            icon: 'info',
            confirmButtonText: 'Close'
          });
        }


        function viewSubcounty(county) {
          // Log the county_id to check it's passed correctly
          console.log("Fetching sub-counties for county_id:", county.id);

          fetch(`List_Counties.php?county_id=${county.id}`)
            .then(response => response.json())
            .then(data => {
              console.log("Sub-county data:", data); // Log the data received
              if (data.status === 'success') {
                let subcountyList = '<ul>';
                data.sub_counties.forEach(subcounty => {
                  subcountyList += `<li>${subcounty.sub_county}</li>`;
                });
                subcountyList += '</ul>';

                Swal.fire({
                  title: `<strong>Sub-Counties of ${county.county_name}</strong>`,
                  html: subcountyList,
                  icon: 'info',
                  confirmButtonText: 'Close'
                });
              } else {
                // If no sub-counties found, display the message
                Swal.fire({
                  title: 'No Sub-Counties Found',
                  text: data.message,
                  icon: 'warning',
                  confirmButtonText: 'Close'
                });
              }
            })
            .catch(error => {
              // Log any error
              console.error("Error fetching sub-counties:", error);

              Swal.fire({
                title: 'Error!',
                text: 'An error occurred while fetching the sub-counties. Please try again.',
                icon: 'error',
                confirmButtonText: 'Close'
              });
            });
        }




        function editCounty(county) {
          Swal.fire({
            title: 'Edit County Info',
            html: `
                        <div class="form-group text-left">
                            <label for="edit-name">County Name</label>
                            <input id="edit-name" class="form-control" placeholder="County Name" value="${county.county_name}" readonly>
                        </div>
                    `,
            showCancelButton: true,
            confirmButtonText: 'Save Changes',
            preConfirm: () => {
              const updated = {
                id: county.id,
                name: document.getElementById('edit-name').value
              };
              return fetch('List_Counties.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updated)
              }).then(response => {
                if (!response.ok) throw new Error('Update failed');
                return response.json();
              }).then(data => {
                if (data.success) {
                  Swal.fire('Updated!', data.message, 'success').then(() => location.reload());
                } else {
                  Swal.fire('Error!', data.message, 'error');
                }
              }).catch(err => {
                Swal.fire('Failed!', 'Could not save changes.', 'error');
              });
            }
          });
        }
      </script>

    </div>
 <?php include 'files/footer.php'; ?>