<!DOCTYPE html>
<html>
<head>
    
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href="./index.css">
    <title>Blog</title>
</head>
<body>
    <h1>Blog Assignment</h1>

    <?php
    session_start();

    // Check if the admin is logged in
    $isLoggedIn = isset($_SESSION['isLoggedIn']) ? $_SESSION['isLoggedIn'] : false;

    // Check if the admin has submitted login credentials
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Perform basic username and password validation
        if ($username === 'name' && $password === 'pass') {
            $_SESSION['isLoggedIn'] = true;
            $isLoggedIn = true;
        } else {
            echo '<p>Invalid login credentials.</p>';
        }
    }

    // Check if the admin has clicked the logout button
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        $_SESSION['isLoggedIn'] = false;
        $isLoggedIn = false;
        session_destroy();
    }

    // Check if a post has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = $_POST['date'];

        // Save the post to a file
        $post = $date . '###' . $title . '###' . $content . PHP_EOL;
        file_put_contents('posts.txt', $post, FILE_APPEND);
    }

    // Check if a post deletion has been requested
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $postIndex = $_POST['postIndex'];

        // Read all posts
        $posts = file('posts.txt');

        // Check if the post index is valid
        if ($postIndex >= 0 && $postIndex < count($posts)) {
            // Remove the post from the array
            unset($posts[$postIndex]);

            // Save the updated posts to the file
            file_put_contents('posts.txt', implode('', $posts));
        }
    }
    ?>

    <?php if ($isLoggedIn) { ?>
        <!-- Admin Panel -->
        <h2>Admin Panel</h2>
        <p>Welcome, admin!</p>
        <p><a href="?action=logout">Logout</a></p>

        <h3>New Post</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <label>Title:</label><br>
            <input type="text" name="title"><br><br>
            <label>Content:</label><br>
            <textarea name="content"></textarea><br><br>
            <label>Date:</label><br>
            <input type="date" name="date"><br><br>
            <input type="submit" value="Submit">
        </form>

        <hr>
    <?php } else { ?>
        <!-- Login Form -->
        <h2>Admin Login</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            <label>Username:</label><br>
            <input type="text" name="username"><br><br>
            <label>Password:</label><br>
            <input type="password" name="password"><br><br>
            <input type="submit" value="Login">
        </form>

        <hr>
    <?php } ?>

    <!-- Blog Posts -->
    <h2>Blog Posts</h2>
    <?php
    // Read and display existing posts
    $posts = file('posts.txt');
    foreach ($posts as $index => $post) {
        $parts = explode('###', $post);
        $postDate = $parts[0];
        $postTitle = $parts[1];
        $postContent = $parts[2];

        // Check if the post date is on or before the current date, or if the admin is logged in
        if (strtotime($postDate) <= strtotime('today') || $isLoggedIn) {
            echo '<h3>' . $postTitle . '</h3>';
            echo '<p>' . $postDate . '</p>';
            echo '<p>' . $postContent . '</p>';

            if ($isLoggedIn) {
                // Delete button
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="action" value="delete">';
                echo '<input type="hidden" name="postIndex" value="' . $index . '">';
                echo '<input type="submit" value="Delete">';
                echo '</form>';
            }

            echo '<hr>';
        }
    }
    ?>
</body>
</html>
