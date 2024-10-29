<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['the_file'])) {
    // Tentukan direktori upload dasar
    $base_upload_dir = 'serverdrone/Output/Drone/';
    
    // Periksa apakah 'folder_name' dikirim dari aplikasi
    if (isset($_POST['folder_name'])) {
        $folder_name = basename($_POST['folder_name']);  // Mengambil nama folder dari input dan mengamankan namanya
        $upload_dir = $base_upload_dir . $folder_name . '/';
        
        // Membuat folder jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    } else {
        // Jika tidak ada 'folder_name', gunakan direktori dasar
        $upload_dir = $base_upload_dir;
    }
    
    // Tentukan jalur lengkap untuk file yang akan di-upload
    $upload_file = $upload_dir . basename($_FILES['the_file']['name']);
    
    // Pindahkan file yang di-upload ke direktori tujuan
    if (move_uploaded_file($_FILES['the_file']['tmp_name'], $upload_file)) {
        echo "File uploaded successfully to " . $upload_file;
    } else {
        echo "File upload failed.";
    }
} else {
    echo "Invalid request method or file not set.";
}
?>
