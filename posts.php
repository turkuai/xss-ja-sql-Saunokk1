<?php
include 'connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sql = "SELECT * FROM posts ORDER BY posted DESC";

try {
    $query = $conn->prepare($sql);
    $query->execute();
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$role = $_SESSION['user']['roles'] ?? 'vieras';

function verifySignature($data, $signature, $publicKey) {
    $pubKeyId = openssl_pkey_get_public($publicKey);
    if (!$pubKeyId) return false;

    return openssl_verify(
        $data,
        base64_decode($signature),
        $pubKeyId,
        OPENSSL_ALGO_SHA256
    ) === 1;
}
?>

<ul>
<hr>

<?php foreach ($res as $row): ?>

    <?php
        $isValid = false;

        if (!empty($row['signature']) && !empty($row['pubkey'])) {
            $isValid = verifySignature($row['body'], $row['signature'], $row['pubkey']);
        }
    ?>

    <li>
        <h3><?= htmlspecialchars($row['title']) ?></h3>

        <p><i><?= $row['posted'] ?></i></p>

        <p><?= htmlspecialchars($row['body']) ?></p>

        <?php if ($row['signature']): ?>
            <?php if ($isValid): ?>
                <p style="color:green;">✔ Allekirjoitus OK</p>
            <?php else: ?>
                <p style="color:red;">⚠ Viestiä on muokattu</p>
            <?php endif; ?>

            <details>
                <summary>Allekirjoitus ja julkinen avain</summary>
                <pre><?= htmlspecialchars($row['signature']) ?></pre>
                <pre><?= htmlspecialchars($row['pubkey']) ?></pre>
            </details>
        <?php else: ?>
            <p>Ei allekirjoitusta</p>
        <?php endif; ?>

        <?php if (in_array($role, ['ylläpitäjä','moderaattori'])): ?>
            <a href="edit-post.php?id=<?= $row['id'] ?>">
                <button>Muokkaa</button>
            </a>
        <?php endif; ?>

        <?php if ($role === 'ylläpitäjä'): ?>
            <form action="delete-post.php" method="post" style="display:inline;">
                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                <button style="color:red;">Poista</button>
            </form>
        <?php endif; ?>

        <hr>
    </li>

<?php endforeach; ?>

</ul>