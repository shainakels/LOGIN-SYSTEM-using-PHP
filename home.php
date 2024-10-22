<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['content']) || !empty($_FILES['image']['name']))) {
    $username = $_SESSION['username'];
    $content = isset($_POST['content']) ? $_POST['content'] : null;

    $targetdir = "uploads/";
    $uploadok = 1;
    $imagepath = null;

    if (!empty($_FILES['image']['name'])) {
        $targetfile = $targetdir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($targetfile, PATHINFO_EXTENSION));

        if ($_FILES['image']['size'] > 2000000) {
            $message = "File is too Large";
            $uploadok = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadok = 0;
        }

        if ($uploadok == 1) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetfile)) {
                $imagepath = $targetfile;
            }
        }
    }

    if ($content && $imagepath) {
        $stmt = $conn->prepare("INSERT INTO posts (username, content, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $content, $imagepath);
    } elseif ($content) {
        $stmt = $conn->prepare("INSERT INTO posts (username, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $content);
    } elseif ($imagepath) {
        $stmt = $conn->prepare("INSERT INTO posts (username, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $imagepath);
    }

    if (isset($stmt) && $stmt->execute()) {
        $message = "POSTING SUCCESS";
    } else {
        $message = "POSTING FAIL";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_post_id'])) {
    $post_id = $_POST['delete_post_id'];

    $fetchPost = $conn->query("SELECT image_path FROM posts WHERE id = '$post_id'");
    if ($fetchPost && $fetchPost->num_rows > 0) {
        $post = $fetchPost->fetch_assoc();
        if (!empty($post['image_path'])) {
            unlink($post['image_path']); 
        }
    }

    $sql = "DELETE FROM posts WHERE id = '$post_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Delete Successful";
    }
}

$postsql = "SELECT * FROM posts";
$postresults = $conn->query($postsql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSTS</title>
</head>
<body>
    <h1>POSTS</h1>
    <?php if (isset($message)): ?>
        <p><?php echo $message ?></p>
    <?php endif; ?>

    <form method="post" action="home.php" enctype="multipart/form-data">
        Post Content: <input type="text" name="content"><br>
        Post Image: <input type="file" name="image" accept=".jpg, .jpeg, .png, .gif"><br>
        <input type="submit" value="POST">
    </form>

    <?php while ($post = $postresults->fetch_assoc()): ?>
        <p>
            <b><?php echo $post['username'] ?></b><br>
            <?php if (!empty($post['content'])): ?>
                <?php echo $post['content'] ?><br>
            <?php endif; ?>
            <?php if (!empty($post['image_path'])): ?>
                <img src="<?php echo $post['image_path'] ?>" alt="Post Image" style="max-width:300px"><br>
            <?php endif; ?>
            <form method="post" action="home.php" style="display:inline;">
                <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                <input type="submit" value="DELETE">
            </form>
        </p>
    <?php endwhile; ?>
</body>
</html>
