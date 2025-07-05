<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PWD Access Portal | Assessment</title>
  <meta name="description" content="Interactive PWD certification process with real-time tracking and gamification">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    :root {
      --teal-primary: #008080;
      --teal-light: #4da6a6;
      --teal-dark: #006666;
      --teal-darker: #004d4d;
      --teal-lightest: #e6f2f2;
      --teal-gradient: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
      --gold: #FFD700;
      --silver: #C0C0C0;
      --bronze: #CD7F32;
    }

    body {
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      background-color: #f8f9fa;
      color: #333;
    }

    /* Navbar */
    .navbar {
      background-color: white;
      box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
      transition: all 0.3s ease;
    }

    .navbar.scrolled {
      padding: 10px 0;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: 700;
      color: var(--teal-primary) !important;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .navbar-brand i {
      font-size: 1.8rem;
      margin-right: 10px;
    }

    .nav-link {
      color: #555 !important;
      font-weight: 500;
      margin: 0 10px;
      position: relative;
      transition: all 0.3s ease;
    }

    .nav-link:hover {
      color: var(--teal-primary) !important;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background-color: var(--teal-primary);
      transition: width 0.3s ease;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .btn-teal {
      background: var(--teal-gradient);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 10px 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 128, 128, 0.3);
    }

    .btn-teal:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 128, 128, 0.4);
      color: white;
    }

    .btn-outline-teal {
      border: 2px solid var(--teal-primary);
      color: var(--teal-primary);
      border-radius: 50px;
      padding: 8px 25px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-outline-teal:hover {
      background: var(--teal-gradient);
      color: white;
      border-color: var(--teal-primary);
    }

    /* Hero Section */
    .hero-section {
      background: var(--teal-gradient);
      padding: 150px 0 80px;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjAzKSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
    }

    .hero-title {
      font-weight: 700;
      font-size: 3.5rem;
      margin-bottom: 20px;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .hero-subtitle {
      font-size: 1.3rem;
      opacity: 0.9;
      margin-bottom: 30px;
    }

    /* Gamification Elements */
    .badge-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 40px;
    }

    .badge {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      position: relative;
      cursor: pointer;
    }

    .badge:hover {
      transform: translateY(-5px) scale(1.1);
    }

    .badge-gold {
      background: linear-gradient(135deg, #FFD700 0%, #FFEC80 100%);
    }

    .badge-silver {
      background: linear-gradient(135deg, #C0C0C0 0%, #E0E0E0 100%);
    }

    .badge-bronze {
      background: linear-gradient(135deg, #CD7F32 0%, #E6A85E 100%);
    }

    .badge-tooltip {
      position: absolute;
      bottom: -40px;
      left: 50%;
      transform: translateX(-50%);
      background: white;
      color: #333;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 0.8rem;
      white-space: nowrap;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .badge:hover .badge-tooltip {
      opacity: 1;
      visibility: visible;
      bottom: -50px;
    }

    .progress-container {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      margin-top: -50px;
      position: relative;
      z-index: 10;
    }

    /* Process Section */
    .process-section {
      padding: 80px 0;
      background-color: #f8f9fa;
    }

    .section-title {
      font-weight: 700;
      color: var(--teal-darker);
      margin-bottom: 50px;
      position: relative;
      display: inline-block;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 50px;
      height: 3px;
      background: var(--teal-gradient);
      border-radius: 3px;
    }

    .process-steps {
      display: flex;
      flex-direction: column;
      gap: 30px;
      position: relative;
    }

    .process-steps::before {
      content: '';
      position: absolute;
      top: 0;
      left: 20px;
      height: 100%;
      width: 4px;
      background: var(--teal-lightest);
      z-index: 1;
    }

    .process-step {
      display: flex;
      align-items: flex-start;
      gap: 20px;
      position: relative;
      z-index: 2;
    }

    .step-number {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      border: 3px solid var(--teal-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: var(--teal-primary);
      flex-shrink: 0;
      transition: all 0.3s ease;
    }

    .process-step.active .step-number {
      background: var(--teal-gradient);
      color: white;
      border-color: var(--teal-primary);
      transform: scale(1.1);
    }

    .process-step.completed .step-number {
      background: var(--teal-primary);
      color: white;
      border-color: var(--teal-primary);
    }

    .step-content {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      flex-grow: 1;
      transition: all 0.3s ease;
    }

    .process-step.active .step-content {
      border-left: 4px solid var(--teal-primary);
      transform: translateX(5px);
    }

    .step-title {
      font-weight: 600;
      margin-bottom: 10px;
      color: var(--teal-darker);
    }

    .step-description {
      color: #666;
      margin-bottom: 0;
    }

    .step-badge {
      background: var(--teal-lightest);
      color: var(--teal-primary);
      font-size: 0.8rem;
      padding: 3px 10px;
      border-radius: 50px;
      display: inline-block;
      margin-top: 10px;
      font-weight: 600;
    }

    .process-step.completed .step-badge {
      background: rgba(0, 128, 128, 0.1);
    }

    /* Features Section */
    .features-section {
      padding: 80px 0;
      background: white;
    }

    .feature-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      height: 100%;
      transition: all 0.3s ease;
      border: 1px solid rgba(0, 0, 0, 0.05);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }

    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 128, 128, 0.1);
    }

    .feature-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: var(--teal-lightest);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: var(--teal-primary);
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
      background: var(--teal-gradient);
      color: white;
      transform: rotate(10deg) scale(1.1);
    }

    .feature-title {
      font-weight: 600;
      margin-bottom: 15px;
      color: var(--teal-darker);
    }

    .feature-description {
      color: #666;
    }

    /* Testimonials */
    .testimonial-section {
      padding: 80px 0;
      background: var(--teal-lightest);
    }

    .testimonial-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      position: relative;
      margin-top: 40px;
    }

    .testimonial-card::before {
      content: '\201C';
      position: absolute;
      top: -30px;
      left: 20px;
      font-size: 5rem;
      color: var(--teal-light);
      font-family: serif;
      line-height: 1;
    }

    .testimonial-text {
      font-style: italic;
      color: #555;
      margin-bottom: 20px;
    }

    .testimonial-author {
      display: flex;
      align-items: center;
    }

    .author-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .author-name {
      font-weight: 600;
      margin-bottom: 0;
    }

    .author-title {
      font-size: 0.8rem;
      color: #777;
    }

    /* CTA Section */
    .cta-section {
      padding: 100px 0;
      background: var(--teal-gradient);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .cta-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjAzKSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
    }

    .cta-title {
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .cta-subtitle {
      font-size: 1.2rem;
      opacity: 0.9;
      margin-bottom: 40px;
    }

    /* Footer */
    .footer {
      background: var(--teal-darker);
      color: white;
      padding: 60px 0 20px;
    }

    .footer-logo {
      font-weight: 700;
      font-size: 1.8rem;
      margin-bottom: 20px;
      display: inline-block;
    }

    .footer-logo i {
      margin-right: 10px;
    }

    .footer-about {
      margin-bottom: 20px;
      opacity: 0.8;
    }

    .footer-title {
      font-weight: 600;
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 10px;
    }

    .footer-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 2px;
      background: var(--teal-light);
    }

    .footer-links {
      list-style: none;
      padding: 0;
    }

    .footer-links li {
      margin-bottom: 10px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.8) !important;
      text-decoration: none;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
    }

    .footer-links a:hover {
      color: white;
      padding-left: 5px;
      color: rgba(255, 255, 255, 0.8) !important;
    }

    .footer-links a i {
      margin-right: 8px;
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.8) !important;
    }

    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    .social-link {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .social-link:hover {
      background: var(--teal-primary);
      transform: translateY(-3px);
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 20px;
      margin-top: 40px;
      text-align: center;
      opacity: 0.7;
      font-size: 0.9rem;
    }

    /* Animations */
    @keyframes float {
      0% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-10px);
      }

      100% {
        transform: translateY(0px);
      }
    }

    .float-animation {
      animation: float 3s ease-in-out infinite;
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(0, 128, 128, 0.4);
      }

      70% {
        box-shadow: 0 0 0 15px rgba(0, 128, 128, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(0, 128, 128, 0);
      }
    }

    .pulse-animation {
      animation: pulse 2s infinite;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .hero-title {
        font-size: 2.8rem;
      }
    }

    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.2rem;
      }

      .hero-subtitle {
        font-size: 1.1rem;
      }

      .process-steps::before {
        left: 15px;
      }

      .step-number {
        width: 30px;
        height: 30px;
        font-size: 0.9rem;
      }
    }

    @media (max-width: 576px) {
      .hero-section {
        padding: 120px 0 60px;
      }

      .hero-title {
        font-size: 1.8rem;
      }

      .badge-container {
        gap: 10px;
      }

      .badge {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="/">
        <i class="fas fa-wheelchair"></i>PWD Access
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#process">Process</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#features">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login">Login</a>
          </li>
          <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
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
          <h1 class="hero-title animate__animated animate__fadeInDown">Your PWD Assessment Journey</h1>
          <p class="hero-subtitle animate__animated animate__fadeIn animate__delay-1s">Track your application through
            every step with our transparent process. Easy visibilty of your process!</p>
          <div class="d-flex flex-wrap gap-3 animate__animated animate__fadeIn animate__delay-2s">
            <a href="register" class="btn btn-light btn-lg px-4">
              Apply Now <i class="fas fa-arrow-right ms-2"></i>
            </a>
            <a href="login" class="btn btn-outline-light btn-lg px-4">
              Track Application
            </a>
          </div>
        </div>
        <div class="col-lg-6 mt-4 mt-lg-0 animate__animated animate__fadeInRight animate__delay-1s">
          <img src="https://assets8.lottiefiles.com/packages/lf20_obhph3sh.json" alt="PWD Process"
            class="img-fluid float-animation">
        </div>
      </div>

      <!-- Gamification Badges -->
      <div class="badge-container animate__animated animate__fadeInUp animate__delay-2s">
        <div class="badge badge-gold">
          <i class="fas fa-medal"></i>
          <span class="badge-tooltip">Completion Badge</span>
        </div>
        <div class="badge badge-silver">
          <i class="fas fa-tachometer-alt"></i>
          <span class="badge-tooltip">Fast Tracker</span>
        </div>
        <div class="badge badge-bronze">
          <i class="fas fa-check-circle"></i>
          <span class="badge-tooltip">First Step</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Progress Container -->
  <div class="container">
    <div class="progress-container animate__animated animate__fadeInUp animate__delay-3s">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h4 class="mb-3">Your Progress Journey</h4>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-teal" role="progressbar" style="width: 25%;" aria-valuenow="25"
              aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <p class="mt-2 text-muted">25% completed - Next step: Medical Review</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
          <a href="login" class="btn btn-teal pulse-animation">
            <i class="fas fa-tasks me-2"></i> View Detailed Progress
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Process Section -->
  <section class="process-section" id="process">
    <div class="container">
      <h2 class="section-title text-center animate__animated animate__fadeIn">Our Transparent Assessment Process</h2>

      <div class="process-steps">
        <!-- Step 1 -->
        <div class="process-step active animate__animated animate__fadeIn">
          <div class="step-number">1</div>
          <div class="step-content">
            <h5 class="step-title">Application Submission</h5>
            <p class="step-description">Complete your online form with your personal details and disability information.
              Upload required documents for verification.</p>
            <span class="step-badge">Current Step</span>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="process-step animate__animated animate__fadeIn animate__delay-1s">
          <div class="step-number">2</div>
          <div class="step-content">
            <h5 class="step-title">Medical Review</h5>
            <p class="step-description">The certified medical team will review your documents and may request additional
              information or examination.</p>
            <span class="step-badge">Pending</span>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="process-step animate__animated animate__fadeIn animate__delay-2s">
          <div class="step-number">3</div>
          <div class="step-content">
            <h5 class="step-title">Approval Verification</h5>
            <p class="step-description">The hospital health department verifies the medical assessment and disability
              classification.</p>
            <span class="step-badge">Pending</span>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="process-step animate__animated animate__fadeIn animate__delay-3s">
          <div class="step-number">4</div>
          <div class="step-content">
            <h5 class="step-title">County Approval</h5>
            <p class="step-description">Final approval from the county disability services office with official
              certification.</p>
            <span class="step-badge">Pending</span>
          </div>
        </div>

        <!-- Step 5 -->
        <div class="process-step animate__animated animate__fadeIn animate__delay-4s">
          <div class="step-number">5</div>
          <div class="step-content">
            <h5 class="step-title"> Assessment Report Delivery</h5>
            <p class="step-description">Receive your Assessment report after the approval process.</p>
            <span class="step-badge">Pending</span>
          </div>
        </div>
      </div>

      <div class="text-center mt-5">
        <a href="register" class="btn btn-teal btn-lg px-5">
          <i class="fas fa-play-circle me-2"></i> Start Your Journey
        </a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section" id="features">
    <div class="container">
      <h2 class="section-title text-center">Why Choose Our Platform</h2>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card animate__animated animate__fadeInUp">
            <div class="feature-icon">
              <i class="fas fa-bolt"></i>
            </div>
            <h4 class="feature-title">Fast Processing</h4>
            <p class="feature-description">Our streamlined process reduces wait times from weeks to days with real-time
              updates at every stage.</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
            <div class="feature-icon">
              <i class="fas fa-lock"></i>
            </div>
            <h4 class="feature-title">Secure & Private</h4>
            <p class="feature-description">Military-grade encryption protects your sensitive health information
              throughout the process.</p>
          </div>
        </div>

        <!-- <div class="col-md-4">
          <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
            <div class="feature-icon">
              <i class="fas fa-gamepad"></i>
            </div>
            <h4 class="feature-title">Gamified Experience</h4>
            <p class="feature-description">Earn badges, points, and rewards as you complete each step, making the
              process engaging.</p>
          </div>
        </div> -->

        <div class="col-md-4">
          <div class="feature-card animate__animated animate__fadeInUp">
            <div class="feature-icon">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <h4 class="feature-title">Mobile Friendly</h4>
            <p class="feature-description">Complete your application and track progress from any device, anytime,
              anywhere.</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
            <div class="feature-icon">
              <i class="fas fa-headset"></i>
            </div>
            <h4 class="feature-title">24/7 Support</h4>
            <p class="feature-description">Our disability specialists are available round the clock to assist with your
              application.</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
            <div class="feature-icon">
              <i class="fas fa-percentage"></i>
            </div>
            <h4 class="feature-title">Benefit Access</h4>
            <p class="feature-description">Immediate access to disability discounts and services upon certification
              approval.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonial-section">
    <div class="container" hidden>
      <h2 class="section-title text-center">Success Stories</h2>

      <div class="row">
        <div class="col-lg-4 mb-4">
          <div class="testimonial-card">
            <p class="testimonial-text">"The assessment process made what I expected to be a stressful process actually
              enjoyable. I looked forward to completing each step!"</p>
            <div class="testimonial-author">
              <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah J." class="author-avatar">
              <div>
                <h5 class="author-name">Sarah J.</h5>
                <p class="author-title">PWD Member since 2022</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 mb-4">
          <div class="testimonial-card">
            <p class="testimonial-text">"From application to approval in just 9 days! The real-time tracking kept me
              informed at every stage."</p>
            <div class="testimonial-author">
              <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Michael T." class="author-avatar">
              <div>
                <h5 class="author-name">Michael T.</h5>
                <p class="author-title">PWD Member since 2023</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 mb-4">
          <div class="testimonial-card">
            <p class="testimonial-text">"As someone with limited mobility, being able to complete everything online was
              a lifesaver. Thank you!"</p>
            <div class="testimonial-author">
              <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Priya K." class="author-avatar">
              <div>
                <h5 class="author-name">Priya K.</h5>
                <p class="author-title">PWD Member since 2021</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="container">
      <h2 class="cta-title">Ready to Begin Your Assessment Journey?</h2>
      <p class="cta-subtitle">Join thousands who have successfully navigated our transparent Assessment process.</p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="register" class="btn btn-light btn-lg px-5">
          <i class="fas fa-user-plus me-2"></i> Register for Assessment
        </a>
        <a href="official_reg" class="btn btn-outline-light btn-lg px-5">
          <i class="fas fa-user-md me-2"></i> Official Registration
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-5 mb-5 mb-lg-0">
          <a href="/" class="footer-logo text-light">
            <i class="fas fa-wheelchair "></i>PWD Access
          </a>
          <p class="footer-about">Making disability assessment process transparent and accessible for everyone through
            innovative technology and compassionate service.</p>
          <!-- <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
          </div> -->
        </div>

        <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
          <h5 class="footer-title">Quick Links</h5>
          <ul class="footer-links">
            <li><a href="/"><i class="fas fa-chevron-right"></i> Home</a></li>
            <li><a href="#process"><i class="fas fa-chevron-right"></i> Process</a></li>
            <li><a href="#features"><i class="fas fa-chevron-right"></i> Features</a></li>
            <li><a href="login"><i class="fas fa-chevron-right"></i> Login</a></li>
            <li><a href="register"><i class="fas fa-chevron-right"></i> Register</a></li>
          </ul>
        </div>

        <!-- <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
          <h5 class="footer-title">Resources</h5>
          <ul class="footer-links">
            <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Documentation</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Disability Rights</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Support Services</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Benefits Guide</a></li>
          </ul>
        </div> -->

        <div class="col-lg-4 col-md-4 text-light">
          <h5 class="footer-title">Contact Us</h5>
          <ul class="footer-links text-light">
            <li class="text-light"><a href="mailto:help@pwdaccess.online"><i class="fas fa-envelope text-light"></i>
                help@pwdaccess.online</a></li>
            <!-- <li class="text-light"><a href="tel:1234567890"><i class="fas fa-phone text-light"></i> (123) 456-7890</a>
            </li> -->
            <!-- <li class="text-light"><a href="#"><i class="fas fa-map-marker-alt text-light"></i> 123 Disability Ave,
                County</a></li> -->
            <li class="text-light"><a href="#"><i class="fas fa-clock text-light"></i> Monday - Sunday 24 / 7  </a></li>
            <!-- <li class="text-light"><a href="#"><i class="fas fa-ambulance text-light"></i> Emergency Support</a></li> -->
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <span id="currentYear"></span> PWD Access. All rights reserved. | <a href="#"
            style="color: rgba(255,255,255,0.7);">Privacy Policy</a> | <a href="#"
            style="color: rgba(255,255,255,0.7);">Terms of Service</a></p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JS -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Set current year
      document.getElementById('currentYear').textContent = new Date().getFullYear();

      // Navbar scroll effect
      window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });

      // Animate process steps on scroll
      const processSteps = document.querySelectorAll('.process-step');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate__fadeIn');
          }
        });
      }, { threshold: 0.1 });

      processSteps.forEach(step => {
        observer.observe(step);
      });

      // Simulate progress updates
      function updateProgress() {
        const progressBar = document.querySelector('.progress-bar');
        let width = 25;
        const interval = setInterval(() => {
          if (width >= 100) {
            clearInterval(interval);
            return;
          }
          width += 0.5;
          progressBar.style.width = width + '%';
          progressBar.setAttribute('aria-valuenow', width);

          // Update progress text
          const progressText = document.querySelector('.progress-container p');
          if (width < 50) {
            progressText.textContent = Math.floor(width) + '% completed - Next step: Medical Review';
          } else if (width < 75) {
            progressText.textContent = Math.floor(width) + '% completed - Next step: Health Verification';
          } else {
            progressText.textContent = Math.floor(width) + '% completed - Next step: County Approval';
          }
        }, 50);
      }

      // Start progress animation when progress container is visible
      const progressContainer = document.querySelector('.progress-container');
      const progressObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
          updateProgress();
          progressObserver.unobserve(progressContainer);
        }
      });

      progressObserver.observe(progressContainer);
    });
  </script>
</body>

</html>