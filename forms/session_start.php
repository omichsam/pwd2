<?php
session_start();
if (!isset($_SESSION['id_number'])) {
    header('Location: login.php');
    exit();
}
// Access session variables
echo "Welcome " . $_SESSION['type'];
?>