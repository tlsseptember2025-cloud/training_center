<?php
session_start();

if (isset($_SESSION['user_id'])) {
    echo "User is logged in. User ID: " . $_SESSION['user_id'];
} else {
    echo "User is NOT logged in";
}
