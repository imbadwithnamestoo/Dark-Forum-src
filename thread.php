<?php
session_start();

$threadsFile = 'threads.txt';
$threads = file_exists($threadsFile) ? json_decode(file_get_contents($threadsFile), true) : [];

if (!isset($_GET['id'])) {
    echo "Thread ID missing.";
    exit;
}

$threadId = (int)$_GET['id'];
$threadIndex = null;

foreach ($threads as $index => $t) {
    if ($t['id'] === $threadId) {
        $threadIndex = $index;
        break;
    }
}

if ($threadIndex === null) {
    echo "Thread not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    $threads[$threadIndex]['replies'][] = [
        'author' => $_SESSION['username'],
        'content' => htmlspecialchars($_POST['reply'])
    ];
    file_put_contents($threadsFile, json_encode($threads, JSON_PRETTY_PRINT));
    header("Location: thread.php?id=$threadId");
    exit;
}

$thread = $threads[$threadIndex];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $thread['title']; ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Dark Forum</h1>
    <p><a href="index.php">Back to threads</a></p>
    <?php if (isset($_SESSION['username'])): ?>
        <p>Logged in as <strong><?php echo $_SESSION['username']; ?></strong> | <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> or <a href="register.php">Register</a> to reply</p>
    <?php endif; ?>
</header>

<main>
    <div class="thread-card">
        <h2 class="thread-title"><?php echo $thread['title']; ?></h2>
        <p class="thread-author">by <?php echo $thread['author']; ?></p>
        <p class="thread-desc"><?php echo $thread['description']; ?></p>
    </div>

    <section class="replies">
        <h3>Replies</h3>
        <?php if (empty($thread['replies'])): ?>
            <p>No replies yet.</p>
        <?php else: ?>
            <?php foreach ($thread['replies'] as $reply): ?>
                <div class="reply-card">
                    <p class="reply-author"><?php echo $reply['author']; ?> says:</p>
                    <p class="reply-content"><?php echo $reply['content']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php if (isset($_SESSION['username'])): ?>
    <section class="new-reply">
        <h3>Post a Reply</h3>
        <form action="thread.php?id=<?php echo $threadId; ?>" method="post">
            <textarea name="reply" placeholder="Your reply" required></textarea>
            <button type="submit">Reply</button>
        </form>
    </section>
    <?php endif; ?>
</main>
</body>
</html>
