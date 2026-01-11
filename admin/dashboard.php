<?php
include "../includes/auth.php";
requireRole('admin');

echo "<h1>Admin Dashboard</h1>";
echo "<a href='../logout.php'>Logout</a>";
