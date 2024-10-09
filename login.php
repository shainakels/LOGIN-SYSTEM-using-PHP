<?php
session_start();
include("db.php");

if($_SERVER["REQUEST_METHOD"]=="POST") {
    $username=$_POST['username'];
    $password=$_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

if($result->num_rows>0) {
    $_SESSION['username']=$username;
    header("Location: home.php");
}

}

if(isset($_SESSION['username'])) {
    $message="WELCOME " . $_SESSION['username'];
} else {
    $message="Not Logged In.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>
<body>
    <h1>LOGIN PAGE</h1>
    <!--PHP SET-->
    <?php if(isset($message)): ?>
        <h1><?php echo $message ?></h1>
    <?php endif; ?>
    <!--LOGIN-->
    <form method="post" action="login.php">
        Username:<input type="text" name="username" required>
        Password:<input type="password" name="password" required>
        <input type="submit" value="LOGIN">
    </form>
    <!--LOGOUT-->
    <form method="post" action="logout.php">
        <input type="submit" value="LOGOUT">
    </form>
    <!--REGISTER-->
    <form action="register.php">
        <input type="submit" value="REGISTER">
    </form>

</body>
</html>