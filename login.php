<?php
require_once "includes/db.php";
require_once "includes/functions.php";

if (is_logged_in()) {
    redirect("index.php");
}

$error = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password'])) {
        // new session id after login (more secure)
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        set_message("Welcome back, " . $user['name'] . "!");

        if ($user['role'] == "admin") {
            redirect("admin/index.php");
        }
        redirect("cars.php");
    } else {
        $error = "Wrong email or password.";
    }
}

$page_title = "Login";
require_once "includes/header.php";
?>

<section class="auth-page">
    <div class="container">
        <div class="auth-box box">
            <h1>Welcome back</h1>
            <p class="muted">Login to book a car and see your bookings.</p>

            <?php if ($error != "") { ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php } ?>

            <form method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo e($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="switch">No account yet? <a href="register.php">Create one</a></p>

            <div class="demo-login">
                <strong>Demo accounts</strong><br>
                User: sara@example.com / user123<br>
                Admin: admin@drivenow.com / admin123
            </div>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
