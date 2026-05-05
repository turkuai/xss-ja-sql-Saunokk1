<?php 
include 'connect.php';
// session_start(); // Ensure session is started, usually done in home.php
?>

<ul>
    <hr>
    <?php

        $sql = "SELECT * FROM posts ORDER BY posted DESC;"; // Added ordering for better UX
        try {
            $query = $conn->prepare($sql);
            $query->execute();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        $res = $query->fetchAll();
        
        $role = $_SESSION['user']['roles'] ?? 'vieras';

        foreach($res as $row) {
            // Find the author from the session data
            $author = array_filter($_SESSION['users'], function($v, $k) use($row) {
                return $v['id'] === $row['author'];
            }, ARRAY_FILTER_USE_BOTH);
            $author = array_values($author);
            $author = !empty($author) ? $author[0] : ['realname' => 'Unknown'];
            ?>
            <li>
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><i><?php echo $row['posted'] . ' – ' . htmlspecialchars($author['realname']); ?></i></p>
                <p><?php echo htmlspecialchars($row['body']); ?></p>
                
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
        <?php } ?>
</ul>