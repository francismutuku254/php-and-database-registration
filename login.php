<?php
// Start the session to manage login state
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['emaill'];
    $pass = $_POST['passwordd'];

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

    // Prepare and bind to check for email and password
    $stmt = $conn->prepare("SELECT password FROM registration WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    if (!$stmt->bind_param("s", $email)) {
        die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    // Get the result
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result to a variable
        $stmt->bind_result($storedPassword);
        $stmt->fetch();

        // Debugging output
        // echo "Entered Password: '$pass'<br>";
        // echo "Stored Password: '$storedPassword'<br>";

        // Compare the plain text password with the stored password
        if ($pass === $storedPassword) {
            // Password is correct, start a session
            $_SESSION['email'] = $email;
            echo "Login successful! Welcome " . htmlspecialchars($email) . ".";
            // Redirect to a protected page (e.g., dashboard.php)
            // header("Location: dashboard.php");
        } else {
            // Password is incorrect
            echo "Invalid password.";
        }
    } else {
        // Email not found
        echo "No account found with that email.";
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
