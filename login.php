<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

$csrf_token = Authentication::generateCSRFToken();

// Jika sudah login, redirect ke admin
if (Authentication::isLoggedIn()) {
    header("Location: admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portofolio Kyy</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .auth-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .auth-box h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-auth {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-auth:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .auth-toggle {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .auth-toggle a {
            color: #667eea;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }

        .error-message,
        .success-message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }

        .error-message {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }

        .success-message {
            background: #efe;
            color: #060;
            border: 1px solid #cfc;
        }

        .error-message.show,
        .success-message.show {
            display: block;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #ddd;
            background: white;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 500;
            transition: 0.3s;
        }

        .tab-btn.active {
            border-color: #667eea;
            color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <a href="index.php" class="back-link">← Back to Home</a>

            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('login')">Login</button>
                <button class="tab-btn" onclick="switchTab('register')">Register</button>
            </div>

            <!-- LOGIN FORM -->
            <div id="login" class="tab-content active">
                <h1>Login</h1>
                
                <div id="login-error" class="error-message"></div>
                <div id="login-success" class="success-message"></div>

                <form id="login-form" onsubmit="handleLogin(event)">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>

                    <button type="submit" class="btn-auth">Login</button>
                </form>
            </div>

            <!-- REGISTER FORM -->
            <div id="register" class="tab-content">
                <h1>Register</h1>
                
                <div id="register-error" class="error-message"></div>
                <div id="register-success" class="success-message"></div>

                <form id="register-form" onsubmit="handleRegister(event)">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label for="register-fullname">Full Name</label>
                        <input type="text" id="register-fullname" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="register-password-confirm">Confirm Password</label>
                        <input type="password" id="register-password-confirm" name="password_confirm" required>
                    </div>

                    <button type="submit" class="btn-auth">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tab).classList.add('active');
            event.target.classList.add('active');
        }

        async function handleLogin(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('login-form'));
            
            try {
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: form
                });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('login-success').textContent = data.message;
                    document.getElementById('login-success').classList.add('show');
                    setTimeout(() => {
                        window.location.href = 'admin/index.php';
                    }, 1500);
                } else {
                    showError('login', data.errors.join(', '));
                }
            } catch (error) {
                showError('login', 'Error: ' + error.message);
            }
        }

        async function handleRegister(e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('register-form'));
            
            try {
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: form
                });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('register-success').textContent = data.message;
                    document.getElementById('register-success').classList.add('show');
                    setTimeout(() => {
                        switchTab('login');
                    }, 1500);
                } else {
                    showError('register', data.errors.join(', '));
                }
            } catch (error) {
                showError('register', 'Error: ' + error.message);
            }
        }

        function showError(form, message) {
            const errorEl = document.getElementById(form + '-error');
            errorEl.textContent = message;
            errorEl.classList.add('show');
            setTimeout(() => {
                errorEl.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>
