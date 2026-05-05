<?php
include 'connect.php';
session_start();

$title = $_POST['title'];
$body = $_POST['body'];
$signature = $_POST['signature'] ?? null;

$userId = $_SESSION['user']['id'];
$publicKey = $_SESSION['user']['public_key'] ?? null;

$today = date("Y-m-d");

$sql = "INSERT INTO posts (title, body, posted, author, signature, pubkey)
        VALUES (:title, :body, :posted, :userId, :signature, :pubkey)";

try {
    $query = $conn->prepare($sql);

    $query->execute([
        'title' => $title,
        'body' => $body,
        'posted' => $today,
        'userId' => $userId,
        'signature' => $signature,
        'pubkey' => $publicKey
    ]);

    header('Location: home.php');
    exit;

} catch (PDOException $e) {
    die('Virhe: ' . $e->getMessage());
}
?>