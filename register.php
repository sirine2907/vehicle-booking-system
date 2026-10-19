<?php
require_once "includes/db.php";
require_once "includes/functions.php";

if (is_logged_in()) {
    redirect("index.php");
}

$errors = [];
$name = "";
$email = "";
$phone = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // check the form
    if ($name == "") {
        $errors[] = "Please enter your name.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must have at least 6 characters.";
    }
    if ($password != $confirm) {
        $errors[] = "The two passwords are not the same.";
    }

    // check if the email is already used
    if (count($errors) == 0) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
            $errors[] = "An account with this email already exists.";
        }
    }

    // save the new user
    if (count($errors) == 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $hashed_password);
        mysqli_stmt_execute($stmt);

        set_message("Your account was created. You can login now.");
        redirect("login.php");
    }
}

$page_title = "Create account";
require_once "includes/header.php";
?>

<section class="auth-page">
    <div class="container">
        <div class="auth-box box">
            <h1>Create an account</h1>
            <p class="muted">It's free and takes less than a minute.</p>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php } ?>

            <form method="post">
                <div class="form-group">
                    <label for="name">Full name</label>
                    <input type="text" name="name" id="name" value="<?php echo e($name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo e($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone (optional)</label>
                    <input type="text" name="phone" id="phone" value="<?php echo e($phone); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign up</button>
            </form>

            <p class="switch">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
