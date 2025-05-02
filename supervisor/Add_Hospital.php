<?php include 'files/header.php'; ?>
<?php
include '../files/db_connect.php'; //  database connection file
require_once 'files/add_hospital.php';


?>

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
                <div class="col-12 col-sm-10 col-md-8 col-lg-110">
                  <div class="card p-4 text-center" style="border-top: 3px solid rgb(0, 72, 66);">
                    <form method="POST" action="">
                      <div class="form-divider mb-3"><u>Add New Hospital</u></div>

                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="hospital_name">Hospital Name</label>
                          <input type="text" class="form-control" name="hospital_name" id="hospital_name" required>
                        </div>

                        <div class="form-group col-md-6">
                          <label for="county_name">Select County</label>
                          <select name="county_name" class="form-control" id="county_name" required>
                            <option value="">-- Select County --</option>
                            <option value="Mombasa">Mombasa</option>
                            <option value="Kwale">Kwale</option>
                            <option value="Kilifi">Kilifi</option>
                            <option value="Tana River">Tana River</option>
                            <option value="Lamu">Lamu</option>
                            <option value="Taita-Taveta">Taita-Taveta</option>
                            <option value="Garissa">Garissa</option>
                            <option value="Wajir">Wajir</option>
                            <option value="Mandera">Mandera</option>
                            <option value="Marsabit">Marsabit</option>
                            <option value="Isiolo">Isiolo</option>
                            <option value="Meru">Meru</option>
                            <option value="Tharaka-Nithi">Tharaka-Nithi</option>
                            <option value="Embu">Embu</option>
                            <option value="Kitui">Kitui</option>
                            <option value="Machakos">Machakos</option>
                            <option value="Makueni">Makueni</option>
                            <option value="Nyandarua">Nyandarua</option>
                            <option value="Nyeri">Nyeri</option>
                            <option value="Kirinyaga">Kirinyaga</option>
                            <option value="Murang'a">Murang'a</option>
                            <option value="Kiambu">Kiambu</option>
                            <option value="Turkana">Turkana</option>
                            <option value="West Pokot">West Pokot</option>
                            <option value="Samburu">Samburu</option>
                            <option value="Trans Nzoia">Trans Nzoia</option>
                            <option value="Uasin Gishu">Uasin Gishu</option>
                            <option value="Elgeyo-Marakwet">Elgeyo-Marakwet</option>
                            <option value="Nandi">Nandi</option>
                            <option value="Baringo">Baringo</option>
                            <option value="Laikipia">Laikipia</option>
                            <option value="Nakuru">Nakuru</option>
                            <option value="Narok">Narok</option>
                            <option value="Kajiado">Kajiado</option>
                            <option value="Kericho">Kericho</option>
                            <option value="Bomet">Bomet</option>
                            <option value="Kakamega">Kakamega</option>
                            <option value="Vihiga">Vihiga</option>
                            <option value="Bungoma">Bungoma</option>
                            <option value="Busia">Busia</option>
                            <option value="Siaya">Siaya</option>
                            <option value="Kisumu">Kisumu</option>
                            <option value="Homa Bay">Homa Bay</option>
                            <option value="Migori">Migori</option>
                            <option value="Kisii">Kisii</option>
                            <option value="Nyamira">Nyamira</option>
                            <option value="Nairobi">Nairobi</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="sub_county">Sub County</label>
                          <input type="text" class="form-control" name="sub_county" id="sub_county" required>
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
      <?php include 'files/footer.php'; ?>