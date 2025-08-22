<?php
$host = "brainbloom.mysql.database.azure.com";
$user = "brainbloomadmin";
$pass = "Rakib@bindu";
$db   = "quiz-system";

// Path to SSL certificate in project folder
$sslcert = __DIR__ . "/DigiCertGlobalRootCA.crt.pem";

// Initialize connection
$conn = mysqli_init();

// Enable SSL
mysqli_ssl_set($conn, NULL, NULL, $sslcert, NULL, NULL);

// Connect with SSL
if (!mysqli_real_connect($conn, $host, $user, $pass, $db, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
