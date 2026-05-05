<?php
session_start();
require "connect.php";

$role = $_SESSION['user']['roles'] ?? 'vieras';

if ($role !== 'ylläpitäjä') {
    http_response_code(403);
    exit("Forbidden");
}

$post_id = $_POST['post_id'];

$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

header("Location: home.php");