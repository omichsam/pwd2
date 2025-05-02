<?php
include 'files/header.php';
include '../files/db_connect.php'; // 
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
      <?php
      // include 'db_connection.php'; // your DB connection file
      
      $query = "SELECT * FROM hospitals ORDER BY created_at DESC";
      $result = mysqli_query($conn, $query);
      ?>

      <!-- SweetAlert2 CDN -->
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Hospitals</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="#">Hospital List</a></div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Registered Hospitals</h2>
            <p class="section-lead">View and manage registered hospitals.</p>

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>All Hospitals</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Hospital Name</th>
                            <th>County</th>
                            <th>Subcounty</th>
                            <th>Address</th>
                            <th>Created At</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $count = 1;
                          while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                        <td>{$count}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['county']}</td>
                        <td>{$row['subcounty']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                          <button class='btn btn-sm btn-info' onclick='viewHospital(" . json_encode($row) . ")'><i class=\"fas fa-eye\"></i> View</button>
                          <button class='btn btn-sm btn-warning' onclick='editHospital(" . json_encode($row) . ")'><i class=\"fas fa-edit\"></i> Edit</button>
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

      <!-- JavaScript for Swal popups -->
      <script>
        function viewHospital(hospital) {
          Swal.fire({
            title: `<strong>Hospital: ${hospital.name}</strong>`,
            html: `
      <p><strong>County:</strong> ${hospital.county}</p>
      <p><strong>Subcounty:</strong> ${hospital.subcounty}</p>
      <p><strong>Address:</strong> ${hospital.address}</p>
      <p><strong>Created At:</strong> ${hospital.created_at}</p>
    `,
            icon: 'info',
            confirmButtonText: 'Close'
          });
        }

        function editHospital(hospital) {
          Swal.fire({
            title: 'Edit Hospital Info',
            html: `
      <input id="edit-name" class="swal2-input" placeholder="Hospital Name" value="${hospital.name}">
      <input id="edit-county" class="swal2-input" placeholder="County" value="${hospital.county}">
      <input id="edit-subcounty" class="swal2-input" placeholder="Subcounty" value="${hospital.subcounty}">
      <input id="edit-address" class="swal2-input" placeholder="Address" value="${hospital.address}">
    `,
            showCancelButton: true,
            confirmButtonText: 'Save Changes',
            preConfirm: () => {
              const updated = {
                id: hospital.id,
                name: document.getElementById('edit-name').value,
                county: document.getElementById('edit-county').value,
                subcounty: document.getElementById('edit-subcounty').value,
                address: document.getElementById('edit-address').value
              };
              return fetch('List_Hospital.php', {
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


      <?php include 'files/footer.php'; ?>