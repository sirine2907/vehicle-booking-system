<?php
require_once "includes/db.php";
require_once "includes/functions.php";

require_login();

$user_id = $_SESSION['user_id'];
$errors = [];

// get the user info
function get_user($conn, $user_id)
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$user = get_user($conn, $user_id);

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // form 1: update name and phone
    if ($_POST['action'] == "update_info") {
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);

        if ($name == "") {
            $errors[] = "Name can't be empty.";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, phone = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $name, $phone, $user_id);
            mysqli_stmt_execute($stmt);

            $_SESSION['user_name'] = $name;
            set_message("Your profile was updated.");
            redirect("profile.php");
        }
    }

    // form 2: change password
    if ($_POST['action'] == "change_password") {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if (!password_verify($current, $user['password'])) {
            $errors[] = "Your current password is wrong.";
        } elseif (strlen($new) < 6) {
            $errors[] = "New password must have at least 6 characters.";
        } elseif ($new != $confirm) {
            $errors[] = "The new passwords are not the same.";
        } else {
            $hashed_password = password_hash($new, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            mysqli_stmt_execute($stmt);

            set_message("Your password was changed.");
            redirect("profile.php");
        }
    }
}

// count the bookings of this user
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total_bookings = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

$page_title = "My profile";
require_once "includes/header.php";
?>

<section class="page-header">
    <div class="container">
        <h1>My profile</h1>
        <p class="muted">Member since <?php echo format_date($user['created_at']); ?> &middot; <?php echo $total_bookings; ?> booking(s)</p>
    </div>
</section>

<section class="container">
    <?php foreach ($errors as $error) { ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php } ?>

    <div class="form-row">
        <div class="box">
            <h3>Personal information</h3>
            <form method="post">
                <input type="hidden" name="action" value="update_info">
                <div class="form-group">
                    <label for="name">Full name</label>
                    <input type="text" name="name" id="name" value="<?php echo e($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email (can't be changed)</label>
                    <input type="email" id="email" value="<?php echo e($user['email']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" value="<?php echo e($user['phone']); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </div>

        <div class="box">
            <h3>Change password</h3>
            <form method="post">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label for="current_password">Current password</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New password</label>
                    <input type="password" name="new_password" id="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm new password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-outline">Update password</button>
            </form>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
