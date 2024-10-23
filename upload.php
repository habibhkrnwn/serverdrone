<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['the_file'])) {
    $upload_dir = 'serverdrone/Output/Drone/';
    $upload_file = $upload_dir . basename($_FILES['the_file']['name']);

    if (move_uploaded_file($_FILES['the_file']['tmp_name'], $upload_file)) {
        echo "File uploaded successfully.";
    } else {
        echo "File upload failed.";
    }
}else {
    echo "Invalid request method.";}
?>

