<?php
require_once "includes/db.php";
require_once "includes/functions.php";

$errors = [];
$name = "";
$email = "";
$subject = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($name == "" || $subject == "" || $message == "") {
        $errors[] = "Please fill all the fields.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    // save the message so the admin can read it
    if (count($errors) == 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
        mysqli_stmt_execute($stmt);

        set_message("Thank you! Your message was sent, we will answer soon.");
        redirect("contact.php");
    }
} elseif (is_logged_in()) {
    // fill the name for logged users
    $name = $_SESSION['user_name'];
}

$page_title = "Contact";
require_once "includes/header.php";
?>

<section class="page-header">
    <div class="container">
        <h1>Contact us</h1>
        <p class="muted">A question about a car or a booking? Send us a message.</p>
    </div>
</section>

<section class="container contact-grid">
    <div>
        <div class="info-card">
            <h4>Our agency</h4>
            <p class="muted">Avenue Habib Bourguiba, Tunis</p>
        </div>
        <div class="info-card">
            <h4>Phone</h4>
            <p class="muted">+216 71 000 000</p>
        </div>
        <div class="info-card">
            <h4>Email</h4>
            <p class="muted">hello@drivenow.com</p>
        </div>
        <div class="info-card">
            <h4>Opening hours</h4>
            <p class="muted">Monday - Saturday: 8:00 - 19:00</p>
        </div>
    </div>

    <div class="box">
        <h3>Send a message</h3>

        <?php foreach ($errors as $error) { ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php } ?>

        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Your name</label>
                    <input type="text" name="name" id="name" value="<?php echo e($name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Your email</label>
                    <input type="email" name="email" id="email" value="<?php echo e($email); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" value="<?php echo e($subject); ?>" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" required><?php echo e($message); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send message</button>
        </form>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
