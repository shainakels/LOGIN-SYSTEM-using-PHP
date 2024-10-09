<?php
session_start();
include("db.php");

if($_SERVER["REQUEST_METHOD"]=="POST") {
    $username=$_POST['username'];
    $password=$_POST['password'];

    $sql = "INSERT INTO users (username, password) VALUES('$username', '$password')";

if($conn->query($sql)===TRUE) {
    $message="REGISTERED SUCCESFULLY";
} else {
    $message="FAILED TO REGISTER.";
}

}

if(isset($_SESSION['username'])) {
    $message="WELCOME " . $_SESSION['username'];
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>REGISTER PAGE</h1>
    <!--PHP SET-->
    <?php if(isset($message)): ?>
        <h1><?php echo $message ?></h1>
    <?php endif; ?>
    <!--REGISTER-->
    <form method="post" action="register.php">
        Username:<input type="text" name="username" required>
        Password:<input type="password" name="password" required>
        <input type="submit" value="REGISTER">
    </form>
    <!--LOGIN-->  
    <form action="login.php">
        <input type="submit" value="LOGIN">
    </form>
</body>
</html>