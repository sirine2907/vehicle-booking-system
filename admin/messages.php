<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";

if (!is_admin()) {
    redirect("../login.php");
}

// delete a message
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_id'])) {
    $message_id = (int) $_POST['delete_id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM messages WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    mysqli_stmt_execute($stmt);
    set_message("The message was deleted.");
    redirect("messages.php");
}

$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC, id DESC");

$page_title = "Messages";
require_once "includes/admin-header.php";
?>

<div class="admin-top">
    <div>
        <h1>Messages</h1>
        <p class="muted">Messages sent from the contact page.</p>
    </div>
</div>

<?php if (mysqli_num_rows($messages) == 0) { ?>
    <div class="empty-box">
        <h3>No messages yet</h3>
    </div>
<?php } ?>

<?php while ($message = mysqli_fetch_assoc($messages)) { ?>
    <div class="message-item">
        <div class="message-item-head">
            <div>
                <h3><?php echo e($message['subject']); ?></h3>
                <p class="muted">
                    From <?php echo e($message['name']); ?>
                    (<a href="mailto:<?php echo e($message['email']); ?>"><?php echo e($message['email']); ?></a>)
                    &middot; <?php echo format_date($message['created_at']); ?>
                </p>
            </div>
            <form method="post">
                <input type="hidden" name="delete_id" value="<?php echo $message['id']; ?>">
                <button type="submit" class="btn btn-danger btn-small" data-confirm="Delete this message?">Delete</button>
            </form>
        </div>
        <p><?php echo nl2br(e($message['message'])); ?></p>
    </div>
<?php } ?>

<?php require_once "includes/admin-footer.php"; ?>
