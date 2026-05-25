<?php
session_start();
require_once "config/config.php";
require_once "config/db.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username_db, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username_db;

                            header("location: index.php");
                            exit;
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Sales Records Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .login-container {
            max-width: 420px;
            margin: auto;
            margin-top: 80px;
        }

        .login-card {
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
            padding: 30px;
            background: #fff;
        }

        .login-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 25px;
        }

        .footer-space {
            margin-top: auto;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-card">

            <h3 class="login-title">Sales Records Login</h3>

            <?php 
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                        value="<?php echo htmlspecialchars($username); ?>"
                    >
                    <div class="invalid-feedback"><?php echo $username_err; ?></div>
                </div>

                <div class="form-group mt-3">
                    <label>Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                    >
                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-link text-muted">← View Main Page</a>
                </div>

            </form>

        </div>
    </div>

    <div class="footer-space">
        <?php include 'inc/footer.php'; ?>
    </div>

</body>
</html>