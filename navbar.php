<nav class="navbar">
    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['account_type'] !== 'admin'): ?>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="modes.php">Game Modes</a>
        <a href="contacts.php">Contact</a>
        <a href="leaderboards.php">Leaderboards</a>
    <?php endif; ?>

    <div class="navbar">
        <?php if (isset($_SESSION['logged_in'])): ?>
            <?php if ($_SESSION['account_type'] === 'admin'): ?>
                <a href="dashboard-admin.php">Admin Dashboard</a>
                <a href="api/logout.php"><button class="btn-login logout">Logout</button></a>
            <?php else: ?>
                <a href="dashboard-user.php">My Profile</a>
                <a href="api/logout.php">Logout</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.html"><button class="btn-login">Login</button></a>
        <?php endif; ?>
    </div>
</nav>
