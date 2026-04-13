<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('admin')) {
    echo json_encode(['available' => false]);
    exit();
}

$email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';
$exclude_id = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : 0;

if (empty($email)) {
    echo json_encode(['available' => true]);
    exit();
}

$query = "SELECT id FROM users WHERE email = '$email'";
if ($exclude_id > 0) {
    $query .= " AND id != $exclude_id";
}

$result = mysqli_query($conn, $query);
$exists = mysqli_num_rows($result) > 0;

echo json_encode(['available' => !$exists]);
?>
