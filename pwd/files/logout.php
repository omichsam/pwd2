<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Logging Out - Ministry of Health</title>
    <meta name="description" content="Ministry of Health Disability Assessment System">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logout-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(13, 148, 136, 0.2);
            padding: 60px 50px;
            text-align: center;
            max-width: 520px;
            width: 100%;
            position: relative;
            overflow: hidden;
            border: 2px solid #e6fffa;
        }

        .logout-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0d9488, #14b8a6);
            border-radius: 24px 24px 0 0;
        }

        .logout-icon {
            font-size: 4.5rem;
            color: #0d9488;
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }

        .logout-title {
            color: #1e293b;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .logout-subtitle {
            color: #64748b;
            font-size: 1.15rem;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .loading-bar {
            height: 6px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            margin: 35px 0;
            border: 1px solid #e2e8f0;
        }

        .loading-progress {
            height: 100%;
            background: linear-gradient(90deg, #0d9488, #14b8a6);
            border-radius: 10px;
            animation: loading 2s ease-in-out;
            width: 100%;
        }

        .redirect-info {
            background: #f0fdfa;
            border-radius: 16px;
            padding: 20px;
            margin-top: 30px;
            border-left: 5px solid #0d9488;
            border: 1px solid #ccfbf1;
        }

        .redirect-info p {
            color: #0f766e;
            font-size: 0.95rem;
            margin: 0;
            font-weight: 500;
        }

        .redirect-info i {
            color: #0d9488;
            margin-right: 10px;
        }

        .security-notice {
            margin-top: 25px;
            padding: 16px;
            background: #f0fdfa;
            border-radius: 16px;
            border: 1px solid #99f6e4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .security-notice i {
            color: #0d9488;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .security-notice span {
            color: #0f766e;
            font-size: 0.9rem;
            font-weight: 500;
        }

        @keyframes pulse {
            0% { 
                transform: scale(1); 
                color: #0d9488;
            }
            50% { 
                transform: scale(1.1); 
                color: #14b8a6;
            }
            100% { 
                transform: scale(1); 
                color: #0d9488;
            }
        }

        @keyframes loading {
            0% { 
                width: 0%; 
                background: linear-gradient(90deg, #0d9488, #14b8a6);
            }
            50% {
                background: linear-gradient(90deg, #14b8a6, #2dd4bf);
            }
            100% { 
                width: 100%; 
                background: linear-gradient(90deg, #0d9488, #14b8a6);
            }
        }

        .footer {
            margin-top: 35px;
            color: #94a3b8;
            font-size: 0.85rem;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .teal-accent {
            color: #0d9488;
            font-weight: 600;
        }

        /* Additional decorative elements */
        .decoration-circle {
            position: absolute;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
            border-radius: 50%;
            z-index: -1;
        }

        .circle-1 {
            top: -40px;
            right: -40px;
        }

        .circle-2 {
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            opacity: 0.7;
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .logout-container {
                padding: 40px 25px;
                margin: 15px;
                border-radius: 20px;
            }
            
            .logout-title {
                font-size: 1.8rem;
            }
            
            .logout-subtitle {
                font-size: 1rem;
            }
            
            .logout-icon {
                font-size: 3.5rem;
            }
            
            .redirect-info,
            .security-notice {
                border-radius: 12px;
            }
        }

        /* Hover effects for better interactivity */
        .logout-container {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logout-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(13, 148, 136, 0.3);
        }
    </style>
</head>

<body>
    <?php
    session_start();
    $_SESSION = [];
    session_destroy();

    // Optionally clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    ?>

    <!-- Decorative background circles -->
    <div class="decoration-circle circle-1"></div>
    <div class="decoration-circle circle-2"></div>

    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        
        <h1 class="logout-title">You've Been Logged Out</h1>
        
        <p class="logout-subtitle">
            You have been successfully signed out of the <span class="teal-accent">Disability Assessment System</span>. 
            For security reasons, please close your browser.
        </p>

        <div class="loading-bar">
            <div class="loading-progress"></div>
        </div>

        <div class="redirect-info">
            <p><i class="fas fa-info-circle"></i> Redirecting to login page in a few seconds...</p>
        </div>

        <div class="security-notice">
            <i class="fas fa-shield-alt"></i>
            <span>Your session has been securely terminated</span>
        </div>

        <div class="footer">
            Ministry of Health &copy; <?php echo date('Y'); ?> | <span class="teal-accent">Secure System</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Enhanced logout confirmation with teal theme
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Logout Successful',
                    text: 'You have been securely logged out of the system.',
                    showConfirmButton: true,
                    confirmButtonText: 'Return to Login',
                    confirmButtonColor: '#0d9488',
                    background: '#ffffff',
                    color: '#1e293b',
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-swal',
                        confirmButton: 'rounded-btn'
                    },
                    willClose: () => {
                        window.location.href = '../../login.php';
                    }
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                        window.location.href = '../../login.php';
                    }
                });
            }, 1000);
        });

        // Add custom styles for SweetAlert
        const style = document.createElement('style');
        style.textContent = `
            .rounded-swal {
                border-radius: 20px !important;
                border: 2px solid #f0fdfa !important;
            }
            .rounded-btn {
                border-radius: 12px !important;
            }
        `;
        document.head.appendChild(style)

        // Keyboard shortcut to immediately redirect
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                window.location.href = '../../login.php';
            }
        });
    </script>
</body>
</html>