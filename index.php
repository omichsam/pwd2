<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PWD County Portal | Disability Certification</title>
  <meta name="description" content="Transparent PWD certification process with real-time tracking">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <!-- Custom CSS -->
  <style>
    :root {
      --teal-primary: #008080;
      --teal-light: #4da6a6;
      --teal-dark: #006666;
      --teal-darker: #004d4d;
      --teal-lightest: #e6f2f2;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }

    /* Navbar */
    .navbar {
      background-color: white;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
    }

    .navbar-brand {
      font-weight: 700;
      color: var(--teal-primary) !important;
    }

    .nav-link {
      color: #333 !important;
      font-weight: 500;
      margin: 0 8px;
    }

    .nav-link:hover {
      color: var(--teal-primary) !important;
    }

    .btn-teal {
      background-color: var(--teal-primary);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }

    .btn-teal:hover {
      background-color: var(--teal-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 128, 128, 0.2);
    }

    /* Hero Section */
    .hero-section {
      background: linear-gradient(135deg, var(--teal-lightest) 0%, white 100%);
      padding: 100px 0 60px;
    }

    /* Process Animation */
    .process-container {
      position: relative;
      height: 400px;
      margin: 40px 0;
    }

    .process-road {
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 8px;
      background-color: #e0e0e0;
      transform: translateY(-50%);
      border-radius: 4px;
      overflow: hidden;
    }

    .process-progress {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      background-color: var(--teal-primary);
      width: 0;
      transition: width 1s ease;
    }

    .process-marker {
      position: absolute;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 40px;
      height: 40px;
      background-color: white;
      border: 4px solid #e0e0e0;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: #999;
      transition: all 0.3s ease;
      z-index: 2;
    }

    .process-marker.active {
      border-color: var(--teal-primary);
      color: var(--teal-primary);
      transform: translate(-50%, -50%) scale(1.1);
    }

    .process-marker.completed {
      border-color: var(--teal-primary);
      background-color: var(--teal-primary);
      color: white;
    }

    .process-checkpoint {
      position: absolute;
      top: 70%;
      transform: translateX(-50%);
      width: 200px;
      text-align: center;
      opacity: 0.6;
      transition: all 0.3s ease;
    }

    .process-checkpoint.active {
      opacity: 1;
      font-weight: 600;
      color: var(--teal-dark);
    }

    .process-checkpoint.completed {
      opacity: 1;
      color: var(--teal-primary);
    }

    /* Footer */
    .footer {
      background-color: var(--teal-darker);
      color: white;
      padding: 40px 0 20px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .footer-links a:hover {
      color: white;
      padding-left: 5px;
    }

    /* Animation */
    @keyframes bounce {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    .bounce-animation {
      animation: bounce 2s infinite;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .process-container {
        height: 300px;
      }

      .process-checkpoint {
        width: 120px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="/">
        <i class="fas fa-wheelchair me-2"></i>PWD County
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#process">Process</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login">Login</a>
          </li>
          <li class="nav-item ms-lg-2">
            <a href="register" class="btn btn-teal">Register</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-4 fw-bold mb-4">Your PWD Certification Journey</h1>
          <p class="lead mb-4">Track your application through every step with our transparent, gamified process.</p>
          <div class="d-flex flex-wrap gap-3">
            <a href="register" class="btn btn-teal btn-lg">
              Apply Now <i class="fas fa-arrow-right ms-2"></i>
            </a>
            <a href="login" class="btn btn-outline-teal btn-lg">
              Track Application
            </a>
          </div>
        </div>
        <div class="col-lg-6 mt-4 mt-lg-0">
          <img src="https://assets8.lottiefiles.com/packages/lf20_obhph3sh.json" alt="PWD Process" class="img-fluid">
        </div>
      </div>
    </div>
  </section>

  <!-- Process Section -->
  <section class="py-5 bg-light" id="process">
    <div class="container">
      <h2 class="text-center mb-5">Transparent Certification Process</h2>

      <div class="process-container">
        <div class="process-road">
          <div class="process-progress" id="processProgress"></div>
        </div>

        <!-- Process Markers -->
        <div class="process-marker" style="left: 10%;" id="marker1">1</div>
        <div class="process-marker" style="left: 30%;" id="marker2">2</div>
        <div class="process-marker" style="left: 50%;" id="marker3">3</div>
        <div class="process-marker" style="left: 70%;" id="marker4">4</div>
        <div class="process-marker" style="left: 90%;" id="marker5">5</div>

        <!-- Checkpoints -->
        <div class="process-checkpoint" style="left: 10%;" id="checkpoint1">
          <i class="fas fa-user-edit mb-2"></i><br>
          Application Submitted
        </div>
        <div class="process-checkpoint" style="left: 30%;" id="checkpoint2">
          <i class="fas fa-user-md mb-2"></i><br>
          Medical Review
        </div>
        <div class="process-checkpoint" style="left: 50%;" id="checkpoint3">
          <i class="fas fa-clipboard-check mb-2"></i><br>
          Health Verification
        </div>
        <div class="process-checkpoint" style="left: 70%;" id="checkpoint4">
          <i class="fas fa-stamp mb-2"></i><br>
          County Approval
        </div>
        <div class="process-checkpoint" style="left: 90%;" id="checkpoint5">
          <i class="fas fa-award mb-2 bounce-animation"></i><br>
          Certificate Ready!
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="register" class="btn btn-teal btn-lg px-4">
          Start Your Journey
        </a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
              <img src="https://media.giphy.com/media/LnUvQeW4jIxJ3XvXjJ/giphy.gif" alt="Real-time tracking"
                class="img-fluid mb-3" style="height: 100px;">
              <h4>Real-time Tracking</h4>
              <p>See exactly where your application is in the process with live updates.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
              <img src="https://media.giphy.com/media/RiAFUYQYqCDrZvFQIZ/giphy.gif" alt="Secure process"
                class="img-fluid mb-3" style="height: 100px;">
              <h4>Secure Process</h4>
              <p>Your data is protected with bank-level encryption throughout the journey.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
              <img src="https://media.giphy.com/media/duNowzaVje6DiEfFMa/giphy.gif" alt="Digital certificate"
                class="img-fluid mb-3" style="height: 100px;">
              <h4>Digital Certificate</h4>
              <p>Download your PWD ID immediately when approved, no waiting.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-5 bg-teal-lightest">
    <div class="container text-center">
      <h2 class="mb-4">Ready to Begin Your PWD Journey?</h2>
      <p class="lead mb-5">Join thousands who have successfully navigated our transparent certification process.</p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="register" class="btn btn-teal btn-lg px-4">
          <i class="fas fa-user-plus me-2"></i> Register as PWD
        </a>
        <a href="official_reg" class="btn btn-outline-teal btn-lg px-4">
          <i class="fas fa-user-md me-2"></i> Official Registration
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 mb-4">
          <h4><i class="fas fa-wheelchair me-2"></i>PWD County</h4>
          <p class="mt-3">Making disability certification transparent and accessible for everyone.</p>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Quick Links</h5>
          <ul class="list-unstyled footer-links">
            <li class="mb-2"><a href="/">Home</a></li>
            <li class="mb-2"><a href="#process">Process</a></li>
            <li class="mb-2"><a href="login">Login</a></li>
            <li class="mb-2"><a href="register">Register</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Contact</h5>
          <ul class="list-unstyled footer-links">
            <li class="mb-2"><i class="fas fa-envelope me-2"></i> help@pwdcounty.gov</li>
            <li class="mb-2"><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
          </ul>
        </div>
      </div>
      <div class="text-center pt-3 mt-3 border-top">
        <p>&copy; <span id="currentYear"></span> PWD County. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JS -->
  <script>
    // Process animation
    document.addEventListener('DOMContentLoaded', function () {
      // Set current year
      document.getElementById('currentYear').textContent = new Date().getFullYear();

      // Animate process
      const progress = document.getElementById('processProgress');
      const markers = [
        document.getElementById('marker1'),
        document.getElementById('marker2'),
        document.getElementById('marker3'),
        document.getElementById('marker4'),
        document.getElementById('marker5')
      ];

      const checkpoints = [
        document.getElementById('checkpoint1'),
        document.getElementById('checkpoint2'),
        document.getElementById('checkpoint3'),
        document.getElementById('checkpoint4'),
        document.getElementById('checkpoint5')
      ];

      // Simulate progress (in a real app, this would come from backend)
      setTimeout(() => {
        progress.style.width = '10%';
        markers[0].classList.add('active');
        checkpoints[0].classList.add('active');
      }, 500);

      setTimeout(() => {
        progress.style.width = '30%';
        markers[0].classList.remove('active');
        markers[0].classList.add('completed');
        markers[1].classList.add('active');
        checkpoints[0].classList.remove('active');
        checkpoints[0].classList.add('completed');
        checkpoints[1].classList.add('active');
      }, 1500);

      setTimeout(() => {
        progress.style.width = '50%';
        markers[1].classList.remove('active');
        markers[1].classList.add('completed');
        markers[2].classList.add('active');
        checkpoints[1].classList.remove('active');
        checkpoints[1].classList.add('completed');
        checkpoints[2].classList.add('active');
      }, 2500);

      setTimeout(() => {
        progress.style.width = '70%';
        markers[2].classList.remove('active');
        markers[2].classList.add('completed');
        markers[3].classList.add('active');
        checkpoints[2].classList.remove('active');
        checkpoints[2].classList.add('completed');
        checkpoints[3].classList.add('active');
      }, 3500);

      setTimeout(() => {
        progress.style.width = '90%';
        markers[3].classList.remove('active');
        markers[3].classList.add('completed');
        markers[4].classList.add('active');
        checkpoints[3].classList.remove('active');
        checkpoints[3].classList.add('completed');
        checkpoints[4].classList.add('active');
      }, 4500);

      setTimeout(() => {
        markers[4].classList.remove('active');
        markers[4].classList.add('completed');
        checkpoints[4].classList.remove('active');
        checkpoints[4].classList.add('completed');
      }, 5500);
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function () {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('shadow');
        navbar.style.padding = '10px 0';
      } else {
        navbar.classList.remove('shadow');
        navbar.style.padding = '15px 0';
      }
    });
  </script>
</body>

</html>