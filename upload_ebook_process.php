<?php
include 'db_connect.php'; // Your DB connection

if(isset($_POST['upload'])){
    $file_name = $_FILES['ebook']['name'];
    $file_tmp = $_FILES['ebook']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allow PDF and PowerPoint files
    $allowed_ext = ['pdf', 'ppt', 'pptx'];
    if(!in_array($file_ext, $allowed_ext)){
        echo "Only PDF and PPT/PPTX files are allowed.";
        exit();
    }

    $target_dir = "uploads/"; // Make sure this folder exists
    $target_file = $target_dir . basename($file_name);

    if(move_uploaded_file($file_tmp, $target_file)){
        $sql = "INSERT INTO ebooks (file_name, file_path) VALUES ('$file_name', '$target_file')";
        if($conn->query($sql)){
            echo "File uploaded successfully!";
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
