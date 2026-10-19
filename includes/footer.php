</main>

<footer class="footer">
    <div class="container footer-grid">
        <div>
            <a href="index.php" class="logo">Drive<span>Now</span></a>
            <p class="muted">Rent a clean and reliable car in a few clicks. No hidden fees, free cancellation.</p>
        </div>
        <div>
            <h4>Pages</h4>
            <a href="cars.php">Our cars</a>
            <a href="about.php">About us</a>
            <a href="contact.php">Contact</a>
        </div>
        <div>
            <h4>Account</h4>
            <?php if (is_logged_in()) { ?>
                <a href="my-bookings.php">My bookings</a>
                <a href="profile.php">Profile</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="register.php">Create account</a>
            <?php } ?>
        </div>
        <div>
            <h4>Contact</h4>
            <p class="muted">Avenue Habib Bourguiba, Tunis</p>
            <p class="muted">+216 71 000 000</p>
            <p class="muted">hello@drivenow.com</p>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> DriveNow. All rights reserved.</p>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
