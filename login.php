<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "Library";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message  = "";
$userName = "";
$userPass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get inputs (trim to remove stray spaces)
    $userName = trim($_POST['user_name'] ?? '');
    $userPass = $_POST['password'] ?? '';

    // Basic check that fields aren’t empty (optional — your form already has required)
    if ($userName === '' || $userPass === '') {
        $message = "Please enter both username and password.";
    } else {
        // One query that checks username AND password (no hashing, as requested)
        $sql = $conn->prepare("SELECT UserName FROM Users WHERE UserName = ? AND Password = ? LIMIT 1");
        if (!$sql) {
            $message = "Prepare failed: " . $conn->error;
        } else {
            $sql->bind_param("ss", $userName, $userPass);
            $sql->execute();
            $sql->store_result();

            if ($sql->num_rows === 1) {
                $message = "Login successful!";
            } else {
                $message = "Incorrect username or password.";
            }

            $sql->close();
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home</title>
		<link rel="stylesheet" href="style.css">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<header><h3>TUD Library</h3></header>
		<nav>
			<div class="topnav">
			  <a href="index.html">Home</a>
			  <a class = "active" href="Login.php">Login</a>
			  <a href="">Contact</a>
			</div>
		</nav>
		
        <?php if ($message != ''): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form action ="" method ="post">
            <label>UserName</label><br>
            <input type="text" name="user_name" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <input type="submit" value="Submit">
        </form>
            

            

            
			
		</div>
		<footer>
			<h3>TUD </h3>
		</footer>		
	</body>
</html>