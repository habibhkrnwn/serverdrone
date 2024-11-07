<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['the_file'])) {
    // Tentukan direktori upload dasar
    $base_upload_dir = 'serverdrone/Output/Drone/';

    // Periksa apakah 'folder_name' dikirim dari aplikasi
    if (isset($_POST['folder_name'])) {
        $folder_name = basename($_POST['folder_name']);  // Mengambil nama folder dari input dan mengamankan namanya

        // Validasi karakter nama folder untuk mencegah injection
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $folder_name)) {
            $upload_dir = $base_upload_dir . $folder_name . '/';
            
            // Membuat folder jika belum ada dan periksa keberhasilan
            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                die("Failed to create directory: $upload_dir");
            }
        } else {
            die("Invalid folder name.");
        }
    } else {
        // Jika tidak ada 'folder_name', gunakan direktori dasar
        $upload_dir = $base_upload_dir;
    }

    // Validasi ekstensi file yang diizinkan
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $file_extension = pathinfo($_FILES['the_file']['name'], PATHINFO_EXTENSION);

    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        die("Invalid file extension. Allowed extensions: " . implode(', ', $allowed_extensions));
    }

    // Tentukan jalur lengkap untuk file yang akan di-upload
    $upload_file = $upload_dir . basename($_FILES['the_file']['name']);

    // Pindahkan file yang di-upload ke direktori tujuan
    if (move_uploaded_file($_FILES['the_file']['tmp_name'], $upload_file)) {
        echo "File uploaded successfully to " . htmlspecialchars($upload_file);
    } else {
        echo "File upload failed.";
    }
} else {
    echo "Invalid request method or file not set.";
}
?>
