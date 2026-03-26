<?php
/**
 * Logout Script
 * Location: /college/logout.php
 * Destroys session and redirects to /college/login.php
 */

// Start session
session_start();

// Store username before destroying session (optional - for goodbye message)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Clear any remember me cookies (if you have implemented)
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time()-3600, '/');
}
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time()-3600, '/');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - College Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logout-container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: white;
            animation: scaleIn 0.6s ease;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        h1 {
            color: #2c3e50;
            font-size: 2em;
            margin-bottom: 15px;
        }

        p {
            color: #7f8c8d;
            font-size: 1.1em;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .username {
            color: #667eea;
            font-weight: 600;
        }

        .redirect-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            color: #6c757d;
            font-size: 0.95em;
        }

        .btn-login {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 30px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            animation: progressBar 3s ease forwards;
        }

        @keyframes progressBar {
            from {
                width: 0%;
            }
            to {
                width: 100%;
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .logout-container {
                padding: 40px 30px;
            }

            h1 {
                font-size: 1.5em;
            }

            p {
                font-size: 1em;
            }

            .logout-icon {
                width: 80px;
                height: 80px;
                font-size: 2.5em;
            }
        }
    </style>
</head>
<body>

<div class="logout-container">
    <div class="logout-icon">
        👋
    </div>
    
    <h1>Logged Out Successfully!</h1>
    
    <p>
        Goodbye, <span class="username"><?php echo htmlspecialchars($username); ?></span>!<br>
        You have been successfully logged out from the system.
    </p>
    
    <div class="redirect-info">
        🔄 Redirecting to login page in <strong id="countdown">3</strong> seconds...
    </div>
    
    <a href="/college/login.php" class="btn-login">🔐 Login Again</a>
    
    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>
</div>

<script>
// Countdown timer
let countdown = 3;
const countdownElement = document.getElementById('countdown');

const timer = setInterval(() => {
    countdown--;
    countdownElement.textContent = countdown;
    
    if (countdown <= 0) {
        clearInterval(timer);
        // Redirect to /college/login.php
        window.location.href = '/college/login.php';
    }
}, 1000);

// Immediate redirect on button click
document.querySelector('.btn-login').addEventListener('click', function(e) {
    clearInterval(timer);
});
</script>

</body>
</html>
