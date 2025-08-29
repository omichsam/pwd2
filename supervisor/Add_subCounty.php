<?php
// Include necessary files
include 'files/header.php';
include '../files/db_connect.php';
require_once 'files/add_subCounty.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Sub County</title>
  <style>
    .form-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    .county-section,
    .subcounty-section {
      padding: 1.5rem;
      background: #f9f9f9;
      border-radius: 8px;
    }

    .sub-county-list {
      margin-top: 1rem;
    }

    .sub-county-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .sub-county-input {
      flex: 1;
    }

    .add-btn-container {
      display: flex;
      justify-content: flex-end;
    }

    .count-label {
      font-size: 0.85rem;
      color: #666;
      margin-bottom: 1rem;
    }

    input,
    select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      background: white;
    }

    .btn {
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      border: none;
    }

    .btn-primary {
      background: #3498db;
      color: white;
    }

    .btn-danger {
      background: #e74c3c;
      color: white;
    }

    .btn-success {
      background: #2ecc71;
      color: white;
      padding: 0.75rem 1.5rem;
    }

    .form-footer {
      grid-column: span 2;
      text-align: right;
      margin-top: 1.5rem;
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <!-- Navigation and Sidebar -->
      <?php include 'files/nav.php'; ?>
      <?php include 'files/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Add Sub County</h1>
          </div>

          <div class="section-body">
            <div class="container">
              <div class="card">
                <form method="POST" action="">
                  <div class="form-container">
                    <!-- County Selection -->
                    <div class="county-section">
                      <h3>County</h3>
                      <select name="county_id" class="form-control" id="county_id" required>
                        <option value="">-- Select County --</option>
                        <?php
                        $county_query = "SELECT id, county_name FROM counties ORDER BY county_name ASC";
                        $county_result = mysqli_query($conn, $county_query);
                        while ($row = mysqli_fetch_assoc($county_result)) {
                          echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['county_name']) . '</option>';
                        }
                        ?>
                      </select>
                    </div>

                    <!-- Sub-counties Section -->
                    <div class="subcounty-section">
                      <div class="add-btn-container">
                        <button type="button" class="btn btn-primary" id="add_sub_county">
                          + Add Sub County
                        </button>
                      </div>
                      <div class="count-label" id="count-display">1 sub-county added</div>

                      <div class="sub-county-list" id="sub_county_fields">
                        <div class="sub-county-item">
                          <div class="sub-county-input">
                            <input type="text" name="sub_county[]" placeholder="Enter sub-county name"
                              class="sub-county-input" required>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="form-footer">
                    <button type="submit" class="btn btn-success">
                      Save All Sub Counties
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Status Notification -->
      <?php if (isset($_GET['status'])): ?>
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
              icon: '<?php echo $_GET['status'] == "success" ? "success" : "error"; ?>',
              title: '<?php echo $_GET['status'] == "success" ? "Success!" : "Error Occurred!"; ?>',
              text: '<?php
              echo $_GET['status'] == "success"
                ? "Sub-counties added successfully."
                : "Failed to add sub-counties. Please try again.";
              ?>',
              confirmButtonColor: '#3085d6'
            });
          });
        </script>
      <?php endif; ?>

      <?php include 'files/footer.php'; ?>
    </div>
  </div>

  <script>
    let subCountyCount = 1;
    const countDisplay = document.getElementById('count-display');
    const container = document.getElementById('sub_county_fields');

    document.getElementById('add_sub_county').addEventListener('click', function () {
      subCountyCount++;
      updateCountDisplay();

      const newField = document.createElement('div');
      newField.classList.add('sub-county-item');
      newField.innerHTML = `
                <div class="sub-county-input">
                    <input type="text" name="sub_county[]" 
                           placeholder="Enter sub-county name" 
                           class="sub-county-input" required>
                </div>
                <button type="button" class="btn btn-danger remove-field">
                    ×
                </button>
            `;
      container.appendChild(newField);

      // Add remove functionality
      newField.querySelector('.remove-field').addEventListener('click', function () {
        container.removeChild(newField);
        subCountyCount--;
        updateCountDisplay();
      });
    });

    function updateCountDisplay() {
      countDisplay.textContent = `${subCountyCount} sub-county${subCountyCount !== 1 ? 's' : ''} added`;
    }
  </script>
</body>

</html>