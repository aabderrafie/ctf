<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
$flag = getenv('FLAG');
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
</head>
<body>
    <h1>Welcome to the Admin Panel</h1>
    <p>FLAG: <?= $flag ?></p>
</body>
</html>

