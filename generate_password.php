<?php
$password = "admin123"; // your desired admin password
$hashed = password_hash($password, PASSWORD_DEFAULT);
echo $hashed;
?>
