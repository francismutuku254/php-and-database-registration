<?php
// Retrieve form data
$user = $_POST['username'];
$email = $_POST['email'];
$pass = $_POST['password'];

// echo "Username: $user<br>";
// echo "Email: $email<br>";
// echo "Password: $pass<br>";

// Database connection parameters
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "register";

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the email already exists
$emailCheckStmt = $conn->prepare("SELECT email FROM registration WHERE email = ?");
if ($emailCheckStmt === false) {
    die("Prepare failed: " . $conn->error);
}

if (!$emailCheckStmt->bind_param("s", $email)) {
    die("Binding parameters failed: (" . $emailCheckStmt->errno . ") " . $emailCheckStmt->error);
}

if (!$emailCheckStmt->execute()) {
    die("Execute failed: (" . $emailCheckStmt->errno . ") " . $emailCheckStmt->error);
}

$emailCheckStmt->store_result();
if ($emailCheckStmt->num_rows > 0) {
    die("Error: This email is already registered.");
}

// Close the statement used for email checking
$emailCheckStmt->close();

// Prepare and bind for insertion
$stmt = $conn->prepare("INSERT INTO registration (username, email, password) VALUES (?, ?, ?)");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("sss", $user, $email, $pass)) {
    die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
}

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
