<?php
$upload_dir = "uploads/";

if (isset($_POST['folder_name'])) {
    $folder_name = $_POST['folder_name'];
    $upload_dir .= $folder_name . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
}

if (isset($_FILES['the_file'])) {
    $file_tmp = $_FILES['the_file']['tmp_name'];
    $file_name = basename($_FILES['the_file']['name']);
    $destination = $upload_dir . $file_name;

    if (move_uploaded_file($file_tmp, $destination)) {
        echo "File uploaded successfully!";
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "No file uploaded.";
}
?>
