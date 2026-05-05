<?php
    session_start();
    if(!array_key_exists('user', $_SESSION)) {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super foorumi</title>
</head>
<body>
    <h1>Super foorumi</h1>
    <p>
        Tervettuloa <?php echo $_SESSION['user']['realname']; ?>
        <a href="logout.php"><button>Kirjaudu ulos</button></a>
    </p>

    <form method="POST">
    <textarea name="public_key" placeholder="Liitä Julkinen Avain"></textarea>
    <button type="submit">Tallenna avain</button>
    </form>

    <?php
    include 'posts.php';
    include 'post-form.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE users SET public_key = ? WHERE id = ?");
    $stmt->execute([$_POST['public_key'], $_SESSION['user']['id']]);
}
    ?>
</body>
</html>