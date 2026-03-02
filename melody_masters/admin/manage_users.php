<?php session_start(); include '../includes/db_config.php'; ?>
<div class="container mt-4">
    <h2>Manage Users</h2>
    <table class="table table-striped">
        <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>
        <?php
        $res = $conn->query("SELECT id, username, email, role FROM users");
        while($row = $res->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['email']}</td><td>{$row['role']}</td></tr>";
        }
        ?>
    </table>
</div>