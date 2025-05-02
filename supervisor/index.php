<?php include 'files/header.php'; ?>

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
            <h1>Dashboard</h1>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1 border border-dark">
                <div class="card-icon bg-primary">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Approved Assessment</h4>
                  </div>
                  <div class="card-body">
                    <b>507</b>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1 bg-dark">
                <div class="card-icon bg-danger text-light">
                  <i class="fas fa-ban"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4 class="text-light">Rejected</h4>
                  </div>
                  <div class="card-body">
                    <b class="text-light">42</b>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12 ">
              <div class="card card-statistic-1 border border-warning">
                <div class="card-icon bg-warning">
                  <i class="fas fa-stethoscope"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Medical Officers</h4>
                  </div>
                  <div class="card-body">
                    <b>151</b>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12" hidden>
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Online Users</h4>
                  </div>
                  <div class="card-body">
                    47
                  </div>
                </div>
              </div>
            </div>
          </div>

        </section>
      </div>

      <?php include 'files/footer.php'; ?>