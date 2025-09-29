<?php
// Include config first
include_once '../config.php';



// Continue with normal page load
include 'files/header.php';
include '../files/db_connect.php';
include 'files/add_hospital.php';

if (isset($_GET['county_id'])) {
  $county_id = $_GET['county_id'];

  // Fetch subcounties based on the county_id
  $subcounties_query = mysqli_query($conn, "SELECT id, sub_county FROM sub_county WHERE county_id = '$county_id' ORDER BY sub_county");

  // Output the subcounty options
  if (mysqli_num_rows($subcounties_query) > 0) {
    while ($row = mysqli_fetch_assoc($subcounties_query)) {
      echo "<option value='{$row['sub_county']}'>{$row['sub_county']}</option>";
    }
  } else {
    echo "<option value='' disabled>No subcounties found</option>";
  }
  exit; // Exit the script after the AJAX response
}

// Fetch all counties for the initial dropdown
$counties = mysqli_query($conn, "SELECT id, county_name FROM counties ORDER BY county_name");

?>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<body>
  <!-- < ?php if (isset($_GET['status'])): ?>
    <script>
      Swal.fire({
        icon: '<?php echo $_GET['status'] == "success" ? "success" : "error"; ?>',
        title: '<?php echo $_GET["status"] == "success" ? "Hospital Added!" : "Something Went Wrong!"; ?>',
        text: '<?php echo $_GET["status"] == "success" ? "The hospital has been added successfully." : "Failed to add the hospital. Please try again."; ?>',
        confirmButtonColor: '#3085d6'
      });
    </script>
  < ?php endif; ?> -->
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php include 'files/nav.php'; ?>
      <?php include 'files/sidebar.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Add Hospital</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Add Hospital</div>
            </div>
          </div>
          <div class="section-body">

            <div class="row">
              <div class="col-8 col-md-8 col-lg-8 text-left">
                <h2 class="section-title">Hi, <?php echo htmlspecialchars($pwdUser['name']); ?>!</h2>
                <p class="section-lead">Use the form below to add a hospital and assign it to a county.</p>
              </div>
            </div>

            <div class="container mt-5">
              <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-12 col-lg-110">
                  <div class="card p-4 text-center" style="border-top: 3px solid rgb(0, 72, 66);">
                    <form method="POST" action="">
                      <div class="form-divider mb-3"><u>Add New Hospital</u></div>

                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="hospital_name">Hospital Name</label>
                          <input type="text" class="form-control" name="hospital_name" id="hospital_name" required>
                        </div>

                        <div class="form-group col-md-6">
                          <label for="county_id">Select County</label>
                          <!-- <select name="county_id" class="form-control" id="county_id" required>
                            <option value="">-- Select County --</option>
                            < ?php
                            $county_query = "SELECT id, county_name FROM counties ORDER BY county_name ASC";
                            $county_result = mysqli_query($conn, $county_query);
                            while ($row = mysqli_fetch_assoc($county_result)) {
                              echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['county_name']) . '</option>';
                            }
                            ?>
                          </select> -->
                          <select class="form-control" id="county_id" name="county_id" required>
                            <option value="" disabled selected>-- Select County --</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($counties)) {
                              echo "<option value='{$row['id']}'>{$row['county_name']}</option>";
                            }
                            ?>
                          </select>
                        </div>

                      </div>

                      <div class="form-row">

                        <div class="form-group col-md-6">
                          <label for="sub_county" class="form-label">Select Sub County</label>
                          <select id="subcounty_id" name="subcounty" class="form-control" required>
                            <option value="" disabled selected>-- Select Sub County --</option>
                          </select>
                        </div>

                        <div class="form-group col-md-6">
                          <label for="address">Hospital Address</label>
                          <input type="text" class="form-control" name="address" id="address" required>
                        </div>
                      </div>

                      <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">Add Hospital</button>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>
      </div>


      <?php
      if (isset($_GET['status'])) {
        $status = $_GET['status'];
        echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: '" . ($status == "success" ? "success" : "error") . "',
                title: '" . ($status == "success" ? "Hospital Added!" : "Error Occurred!") . "',
                text: '" . ($status == "success"
          ? "The hospital has been added successfully to the system."
          : "Failed to add the hospital. Please check your details and try again.") . "',
                confirmButtonColor: '#3085d6'
            });
        });
    </script>";
      }
      ?>



      <script>
        $(document).ready(function () {
          // When the county dropdown is changed
          $('#county_id').change(function () {
            var county_id = $(this).val(); // Get the selected county ID

            // If a county is selected, fetch the corresponding subcounties
            if (county_id) {
              $.ajax({
                url: '', // Send the request to the same file
                type: 'GET',
                data: { county_id: county_id }, // Send county_id to the server
                success: function (response) {
                  // Clear the existing subcounty options
                  $('#subcounty_id').html('<option value="" disabled selected>Select your subcounty</option>');

                  // Append new options from the response (which contains subcounties)
                  $('#subcounty_id').append(response);
                }
              });
            } else {
              // If no county is selected, clear the subcounty dropdown
              $('#subcounty_id').html('<option value="" disabled selected>Select your subcounty</option>');
            }
          });
        });
      </script>


      <?php include 'files/footer.php'; ?>