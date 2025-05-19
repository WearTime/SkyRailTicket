<?php
session_start();
include './config/connection.php';

if (isset($_SESSION['id']) || isset($_SESSION['logged_in'])) {
    header("Location: /");
    exit;
}
if (
    !isset($_SESSION['last_regenerated']) ||
    $_SESSION['last_regenerated'] < time() - 1800
) {
    session_regenerate_id(true);
    $_SESSION['last_regenerated'] = time();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = "";


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>
    <div class="container">
        <img src="./assets/image/logo_skyrail.png" alt="">

        <div class="auth-row">
            <div class="auth">
                <h1>Register</h1>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message" style="color: red;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="auth-form" onsubmit="return false;">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <input id="username-field" class="input-group" type="text" name="username" id="username"
                            placeholder="Username">
                        <p id="username-error" class="error-message"></p>
                    </div>

                    <div class="form-group">
                        <input id="email-field" class="input-group" type="email" name="email" placeholder="Email">
                        <p id="email-error" class="error-message"></p>
                    </div>

                    <div class="form-group">
                        <input id="password-field" class="input-group" type="password" name="password"
                            placeholder="Password">
                        <p id="password-error" class="error-message"></p>
                    </div>

                    <div class="form-group">
                        <input id="confirm-password-field" class="input-group" type="password" name="confirm_password"
                            id="confirm_password" placeholder="Confirm Password">
                        <p id="confirm-passwd-error" class="error-message"></p>

                    </div>

                    <button type="submit" class="form-btn" onclick="submitRegister()">Register</button>
                </form>

                <p class="other">Already have an account? <a href="login">Login here</a></p>
            </div>
        </div>
    </div>
    <script>
        function submitRegister() {
            const email = document.getElementById("email-field");
            const password = document.getElementById("password-field");
            const confirm_password = document.getElementById("confirm-password-field");
            const username = document.getElementById("username-field");
            const csrf = document.getElementById("csrf_token").value;

            const emailError = document.getElementById("email-error");
            const passwordError = document.getElementById("password-error");
            const confirmPasswordError = document.getElementById("confirm-passwd-error");
            const usernameError = document.getElementById("username-error");

            // Reset errors
            emailError.textContent = "";
            usernameError.textContent = "";
            passwordError.textContent = "";
            confirmPasswordError.textContent = "";
            email.classList.remove("bounce");
            username.classList.remove("bounce");
            password.classList.remove("bounce");
            confirm_password.classList.remove("bounce");

            const data = new FormData();
            data.append("username", username.value);
            data.append("email", email.value);
            data.append("confirm_password", confirm_password.value);
            data.append("password", password.value);
            data.append("csrf_token", csrf);

            fetch("./handler/register_handler.php", {
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
                        if (result.username_error) {
                            usernameError.textContent = result.username_error;
                            username.classList.add("bounce");
                        }
                        if (result.confirm_passwd_error) {
                            confirmPasswordError.textContent = result.confirm_passwd_error;
                            confirm_password.classList.add("bounce");
                        }

                        setTimeout(() => {
                            email.classList.remove("bounce");
                            password.classList.remove("bounce");
                            confirm_password.classList.add("bounce");
                            username.classList.add("bounce");
                        }, 1000);
                    }
                })
                .catch(() => {
                    usernameError.textContent = "Terjadi kesalahan. Silakan coba lagi.";
                    username.classList.add("bounce");
                });
        }
    </script>
</body>

</html>