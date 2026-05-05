<?php
session_start();
require "connect.php";

$role = $_SESSION['user']['roles'] ?? 'vieras';

if (!in_array($role, ['ylläpitäjä','moderaattori'])) {
    http_response_code(403);
    exit("Forbidden");
}

// id pakollinen
if (!isset($_GET['id'])) {
    exit("Missing id");
}

$post_id = (int)$_GET['id'];

// hae postaus
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// jos ei löydy
if (!$post) {
    exit("Post not found");
}

// päivitys
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'];
    $body = $_POST['body'];

    $stmt = $conn->prepare("UPDATE posts SET title = ?, body = ? WHERE id = ?");
    $stmt->execute([$title, $body, $post_id]);

    header("Location: home.php");
    exit;
}
?>

<form method="POST">
    <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"><br>
    <textarea name="body"><?= htmlspecialchars($post['body']) ?></textarea><br>

    <button type="submit">Tallenna</button>
</form>