<?php
session_start();

$usersFile = 'users.txt';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if (isset($_SESSION['username']) && !isset($users[$_SESSION['username']])) {
    unset($_SESSION['username']);
}

$threadsFile = 'threads.txt';
$threads = file_exists($threadsFile) ? json_decode(file_get_contents($threadsFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'])) {
    if (!isset($_SESSION['username'])) {
        exit;
    }

    $threads[] = [
        'id' => time(),
        'title' => htmlspecialchars($_POST['title']),
        'description' => htmlspecialchars($_POST['description']),
        'author' => $_SESSION['username'],
        'replies' => []
    ];
    file_put_contents($threadsFile, json_encode($threads, JSON_PRETTY_PRINT));
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dark Forum</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Dark Forum</h1>
    <?php if (isset($_SESSION['username'])): ?>
        <p>Logged in as <strong><?php echo $_SESSION['username']; ?></strong> | <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> or <a href="register.php">Register</a> to post threads</p>
    <?php endif; ?>
</header>

<main>
    <section class="threads">
        <?php foreach ($threads as $thread): ?>
            <div class="thread-card">
                <a href="thread.php?id=<?php echo $thread['id']; ?>" class="thread-title">
                    <?php echo $thread['title']; ?>
                </a>
                <p class="thread-author">by <?php echo $thread['author']; ?></p>
                <p class="thread-desc"><?php echo $thread['description']; ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <?php if (isset($_SESSION['username'])): ?>
    <section class="new-thread">
        <h2>Create a New Thread</h2>
        <form action="index.php" method="post">
            <input type="text" name="title" placeholder="Subject" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <button type="submit">Create Thread</button>
        </form>
    </section>
    <?php endif; ?>
</main>
</body>
</html>
