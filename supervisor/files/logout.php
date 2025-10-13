<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Ministry of Health - System Logout</title>
    <meta name="description" content="Republic of Kenya - Ministry of Health Disability Assessment System">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .official-header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .coat-of-arms {
            height: 80px;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
        }

        .government-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .ministry-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .system-title {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .logout-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            position: relative;
            border: 1px solid #e2e8f0;
        }

        .security-badge {
            position: absolute;
            top: -15px;
            right: 20px;
            background: #0d9488;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
        }

        .logout-icon {
            font-size: 3.5rem;
            color: #0d9488;
            margin-bottom: 20px;
            background: #f0fdfa;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            border: 3px solid #e6fffa;
        }

        .logout-title {
            color: #1a365d;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .logout-subtitle {
            color: #4a5568;
            font-size: 1.05rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
            padding: 12px;
            background: #f7fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            background: #48bb78;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .status-text {
            color: #2d3748;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .loading-container {
            margin: 30px 0;
        }

        .loading-text {
            color: #4a5568;
            font-size: 0.9rem;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .loading-bar {
            height: 4px;
            background: #edf2f7;
            border-radius: 2px;
            overflow: hidden;
        }

        .loading-progress {
            height: 100%;
            background: linear-gradient(90deg, #0d9488, #14b8a6);
            border-radius: 2px;
            animation: loading 2s ease-in-out;
            width: 100%;
        }

        .security-notice {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
            text-align: left;
        }

        .security-notice i {
            color: #e53e3e;
            margin-right: 10px;
            float: left;
        }

        .security-notice span {
            color: #742a2a;
            font-size: 0.85rem;
            display: block;
            margin-left: 25px;
            font-weight: 500;
        }

        .next-steps {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .next-steps h4 {
            color: #0d9488;
            font-size: 0.95rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .next-steps ul {
            color: #2d3748;
            font-size: 0.85rem;
            margin-left: 20px;
        }

        .next-steps li {
            margin-bottom: 5px;
        }

        .official-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.8rem;
        }

        .contact-info {
            margin-top: 10px;
            font-size: 0.75rem;
            color: #a0aec0;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes loading {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .logout-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .official-header {
                margin-bottom: 20px;
            }

            .coat-of-arms {
                height: 60px;
            }

            .government-title {
                font-size: 1rem;
            }

            .ministry-title {
                font-size: 1.1rem;
            }

            .logout-title {
                font-size: 1.5rem;
            }

            .logout-subtitle {
                font-size: 0.95rem;
            }
        }

        /* Print styles */
        @media print {
            body {
                background: white !important;
            }

            .logout-container {
                box-shadow: none;
                border: 1px solid #ccc;
            }

            .no-print {
                display: none;
            }
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

    <div class="official-header">
        <!-- Replace with actual path to coat of arms -->
        <img src="../assets/img/coat_of_arms.png" alt="Republic of Kenya Coat of Arms" class="coat-of-arms"
            onerror="this.style.display='none'">
        <div class="government-title">REPUBLIC OF KENYA</div>
        <div class="ministry-title">Ministry of Health</div>
        <div class="system-title">Disability Assessment Information System</div>
    </div>

    <div class="logout-container">
        <div class="security-badge">
            <i class="fas fa-shield-alt"></i> SECURE SYSTEM
        </div>

        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>

        <h1 class="logout-title">Session Terminated</h1>

        <p class="logout-subtitle">
            You have been successfully logged out of the Disability Assessment Information System.
            All session data has been securely cleared from the system.
        </p>

        <div class="status-indicator">
            <div class="status-dot"></div>
            <div class="status-text">Logout completed successfully</div>
        </div>

        <div class="loading-container">
            <div class="loading-text">Preparing redirect to login portal...</div>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>

        <div class="security-notice">
            <i class="fas fa-exclamation-triangle"></i>
            <span>For security purposes, please close your web browser if you are using a public or shared
                computer.</span>
        </div>

        <div class="next-steps">
            <h4><i class="fas fa-list-alt"></i> Recommended Actions:</h4>
            <ul>
                <li>Close all browser windows to ensure complete logout</li>
                <li>Clear browser cache if using a public device</li>
                <li>Contact system administrator for any login issues</li>
            </ul>
        </div>

        <div class="official-footer">
            <div>&copy; <?php echo date('Y'); ?> Ministry of Health - Republic of Kenya. All rights reserved.</div>
            <div class="contact-info">
                For technical support: healthsupport@health.go.ke | +254-20-2717077
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Official government-style logout confirmation
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'SESSION TERMINATED',
                    html: `
                        <div style="text-align: left; font-size: 0.9rem;">
                            <p><strong>System:</strong> Disability Assessment Information System</p>
                            <p><strong>Status:</strong> Logout completed successfully</p>
                            <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                            <hr style="margin: 10px 0;">
                            <p style="font-size: 0.8rem; color: #666;">
                                You will now be redirected to the secure login portal.
                            </p>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'PROCEED TO LOGIN',
                    confirmButtonColor: '#0d9488',
                    background: '#ffffff',
                    color: '#1a365d',
                    timer: 6000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'government-popup',
                        confirmButton: 'government-btn'
                    },
                    willClose: () => {
                        window.location.href = '../../login.php';
                    }
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                        window.location.href = '../../login.php';
                    }
                });
            }, 1500);
        });

        // Add professional styling for SweetAlert
        const style = document.createElement('style');
        style.textContent = `
            .government-popup {
                border-radius: 8px !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            }
            .government-btn {
                border-radius: 6px !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }
        `;
        document.head.appendChild(style);

        // Professional redirect with escape key support
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.location.href = '../../login.php';
            }
        });
    </script>
</body>

</html>