<?php
session_start();
include './config/connection.php';
if (isset($_SESSION['id']) && $_SESSION['logged_in']) {
    header("Location: /");
    exit;
}
// Regenerate session to prevent fixation
if (
    !isset($_SESSION['last_regenerated']) ||
    $_SESSION['last_regenerated'] < time() - 1800
) {
    session_regenerate_id(true);
    $_SESSION['last_regenerated'] = time();
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle success message from registration
$registerSuccess = false;
if (isset($_SESSION['register_success']) && $_SESSION['register_success'] === true) {
    $registerSuccess = true;
    unset($_SESSION['register_success']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="./assets/css/auth.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <img src="./assets/image/logo_skyrail.png" alt="Sky Rail">
        <div class="auth-row">
            <div class="auth">
                <h1>Log in</h1>

                <form id="login-form" class="auth-form" onsubmit="return false;">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <input id="email-field" class="input-group" type="email" name="email" placeholder="Email">
                        <p id="email-error" class="error-message"></p>
                    </div>

                    <div class="form-group">
                        <input id="password-field" class="input-group" type="password" name="password"
                            placeholder="Password">
                        <p id="password-error" class="error-message"></p>
                    </div>

                    <button type="submit" class="form-btn" onclick="submitLogin()">Login</button>
                </form>

                <p class="other">Don't have an account? <a href="register">Register here</a></p>
            </div>
        </div>
    </div>

    <script>
        const registerSuccess = <?= $registerSuccess ? 'true' : 'false'; ?>;
        if (registerSuccess) {
            Swal.fire({
                title: 'Sukses!',
                text: 'Registrasi berhasil! Silakan login!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
        function submitLogin() {
            const email = document.getElementById("email-field");
            const password = document.getElementById("password-field");
            const csrf = document.getElementById("csrf_token").value;

            const emailError = document.getElementById("email-error");
            const passwordError = document.getElementById("password-error");

            // Reset errors
            emailError.textContent = "";
            passwordError.textContent = "";
            email.classList.remove("bounce");
            password.classList.remove("bounce");

            const data = new FormData();
            data.append("email", email.value);
            data.append("password", password.value);
            data.append("csrf_token", csrf);

            fetch("./handler/login_handler.php", {
                method: "POST",
                body: data,
            })
                .then((res) => res.json())
                .then((result) => {
                    if (result.success) {
                        window.location.href = result.redirect;
                    } else {
                        if (result.email_error) {
                            emailError.textContent = result.email_error;
                            email.classList.add("bounce");
                        }
                        if (result.password_error) {
                            passwordError.textContent = result.password_error;
                            password.classList.add("bounce");
                        }

                        setTimeout(() => {
                            email.classList.remove("bounce");
                            password.classList.remove("bounce");
                        }, 1000);
                    }
                })
                .catch(() => {
                    emailError.textContent = "Terjadi kesalahan. Silakan coba lagi.";
                    email.classList.add("bounce");
                });
        }
    </script>
</body>

</html>