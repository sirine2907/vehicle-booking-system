<?php
$page_title = "Users";
require_once "includes/admin-header.php";

// all users with the number of bookings and money spent
$users = mysqli_query($conn, "SELECT users.*,
                                  COUNT(bookings.id) AS total_bookings,
                                  COALESCE(SUM(CASE WHEN bookings.status = 'approved' THEN bookings.total_price END), 0) AS total_spent
                              FROM users
                              LEFT JOIN bookings ON bookings.user_id = users.id
                              GROUP BY users.id
                              ORDER BY users.created_at DESC, users.id DESC");
?>

<div class="admin-top">
    <div>
        <h1>Users</h1>
        <p class="muted"><?php echo mysqli_num_rows($users); ?> registered account(s)</p>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Bookings</th>
                <th>Spent</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users)) { ?>
                <tr>
                    <td><strong><?php echo e($user['name']); ?></strong></td>
                    <td><?php echo e($user['email']); ?></td>
                    <td><?php echo e($user['phone']); ?></td>
                    <td>
                        <?php if ($user['role'] == "admin") { ?>
                            <span class="badge badge-admin">Admin</span>
                        <?php } else { ?>
                            <span class="badge badge-cancelled">Client</span>
                        <?php } ?>
                    </td>
                    <td><?php echo $user['total_bookings']; ?></td>
                    <td><?php echo format_price($user['total_spent']); ?></td>
                    <td><?php echo format_date($user['created_at']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once "includes/admin-footer.php"; ?>
