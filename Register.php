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

$message = "";
$userName = $phone_num = $userPass = $userConPass = $firstName = $lastName = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userName    = trim($_POST['user_name'] ?? '');
    $phone_num   = trim($_POST['Phone_Num'] ?? '');
    $userPass    = $_POST['password'] ?? '';
    $userConPass = $_POST['Con_password'] ?? '';
    $firstName   = trim($_POST['firstname'] ?? '');
    $lastName    = trim($_POST['lastname'] ?? '');

    // Validation
    if ($userPass !== $userConPass) {
        $message = "Your passwords do not match.";
    } elseif (strlen($userPass) != 6) {
        $message = "Your password must be 6 digits long.";
    } elseif (!ctype_digit($userPass)) {
        $message = "Your password must contain only numbers.";
    } elseif (!ctype_digit($phone_num)) {
        $message = "Your phone number must contain only numbers.";
    } elseif (strlen($phone_num) != 9) {
        $message = "Your phone number must be 9 digits long.";
    } else {
        // Check username uniqueness
        $check = $conn->prepare("SELECT 1 FROM Users WHERE UserName = ? LIMIT 1");
        if (!$check) {
            $message = "Prepare failed: " . $conn->error;
        } else {
            $check->bind_param("s", $userName);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "That username is already taken.";
            } else {
                // Insert user (no hashing requested)
                $sql = $conn->prepare("INSERT INTO Users (UserName, FirstName, LastName, PhoneNumber, Password) VALUES (?, ?, ?, ?, ?)");

                if (!$sql) {
                    $message = "Prepare failed: " . $conn->error;
                } else {
                    $sql->bind_param("sssss", $userName, $firstName, $lastName, $phone_num, $userPass);
                    if ($sql->execute()) {
                        $message = "User added successfully!";
                    } else {
                        $message = "Insert failed: " . $sql->error;
                    }
                    $sql->close();
                }
            }
            $check->close();
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
      <a href="login.php">Login</a>
      <a href="">Contact</a>
    </div>
</nav>

<?php if (!empty($message)): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form action="" method="post" autocomplete="off" novalidate>
    <label>UserName</label><br>
    <input type="text" name="user_name" value="<?php echo htmlspecialchars($userName); ?>"required><br><br>

    <label>First Name</label><br>
    <input type="text" name="firstname"value="<?php echo htmlspecialchars($firstName); ?>"required><br><br>

    <label>Last Name</label><br>
    <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastName); ?>" required><br><br>

    <label>Phone Number:</label><br>
    <input type="tel" name="Phone_Num"  value="<?php echo htmlspecialchars($phone_num); ?>" pattern="\d{9}" inputmode="numeric" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" pattern="\d{6}" required><br><br>
    
    <label>Confirm Password:</label><br>
    <input type="password" name="Con_password" required><br><br>

    <input type="submit" value="Submit">
</form>


<footer>
    <h3>TUD</h3>
</footer>
</body>
</html>