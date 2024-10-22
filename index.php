<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Drone File Explorer - AdminLTE</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">Server Drone</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="?" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">File Explorer</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <?php
                                $baseDir = "serverdrone";
                                $baseUrl = "?dir=";
                                $currentDir = isset($_GET['dir']) ? $_GET['dir'] : $baseDir;
                                $dirParts = explode('/', str_replace($baseDir . '/', '', $currentDir));
                                $path = $baseDir;

                                echo '<li class="breadcrumb-item"><a href="?dir=' . $baseDir . '">Home</a></li>';
                                foreach ($dirParts as $key => $part) {
                                    if (!empty($part)) {
                                        $path .= '/' . $part;
                                        if ($key === array_key_last($dirParts)) {
                                            echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($part) . '</li>';
                                        } else {
                                            echo '<li class="breadcrumb-item"><a href="?dir=' . $path . '">' . htmlspecialchars($part) . '</a></li>';
                                        }
                                    }
                                }
                                ?>
                            </ol>
                        </nav>

                        <!-- Upload file -->
                        <form action="" method="POST" enctype="multipart/form-data" class="mr-3 mb-3">
                            <div class="input-group">
                                <input type="file" name="uploadedFiles[]" class="form-control" multiple required>
                                <button class="btn btn-success" type="submit" name="uploadFiles">Upload Files</button>
                            </div>
                        </form>

                        <!-- Create new folder -->
                        <form method="POST" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="folderName" class="form-control" placeholder="Enter new folder name" required>
                                <button class="btn btn-primary" type="submit">Create Folder</button>
                            </div>
                        </form>
                    </div>

                    <!-- Folder and file display in responsive table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                    <th>Ukuran</th>
                                    <th>Terakhir Diubah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                // Handle folder creation
                            if (isset($_POST['folderName'])) {
                                $newFolderName = trim($_POST['folderName']);
                                $newFolderPath = $currentDir . '/' . $newFolderName;

                                if (!file_exists($newFolderPath)) {
                                    mkdir($newFolderPath, 0777, true);
                                    echo "<div class='alert alert-success'>Folder <strong>$newFolderName</strong> berhasil dibuat.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Folder <strong>$newFolderName</strong> sudah ada.</div>";
                                }
                            }

                            // Handle file upload
                            if (isset($_POST['uploadFiles'])) {
                                $uploadDir = $currentDir . '/';
                                foreach ($_FILES['uploadedFiles']['name'] as $key => $name) {
                                    $tmpName = $_FILES['uploadedFiles']['tmp_name'][$key];
                                    $uploadFilePath = $uploadDir . basename($name);
                                    
                                    if (move_uploaded_file($tmpName, $uploadFilePath)) {
                                        echo "<div class='alert alert-success'>File <strong>$name</strong> berhasil diupload.</div>";
                                    } else {
                                        echo "<div class='alert alert-danger'>Terjadi kesalahan saat mengupload file <strong>$name</strong>.</div>";
                                    }
                                }
                            }

                            // Handle rename folder
                            if (isset($_POST['renameFolder']) && isset($_POST['oldFolderName']) && isset($_POST['newFolderName'])) {
                                $oldFolderName = $_POST['oldFolderName'];
                                $newFolderName = $_POST['newFolderName'];
                                $oldFolderPath = $currentDir . '/' . $oldFolderName;
                                $newFolderPath = $currentDir . '/' . $newFolderName;

                                if (file_exists($oldFolderPath) && !file_exists($newFolderPath)) {
                                    rename($oldFolderPath, $newFolderPath);
                                    echo "<div class='alert alert-success'>Folder <strong>$oldFolderName</strong> berhasil diubah menjadi <strong>$newFolderName</strong>.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Tidak dapat mengganti nama folder <strong>$oldFolderName</strong>.</div>";
                                }
                            }

                            // Handle delete folder
                            if (isset($_POST['deleteFolder']) && isset($_POST['folderToDelete'])) {
                                $folderToDelete = $_POST['folderToDelete'];
                                $folderPath = $currentDir . '/' . $folderToDelete;

                                if (is_dir($folderPath) && count(scandir($folderPath)) == 2) {
                                    rmdir($folderPath);
                                    echo "<div class='alert alert-success'>Folder <strong>$folderToDelete</strong> berhasil dihapus.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Folder <strong>$folderToDelete</strong> tidak dapat dihapus atau tidak kosong.</div>";
                                }
                            }

                            // Handle delete file
                            if (isset($_POST['deleteFile']) && isset($_POST['fileToDelete'])) {
                                $fileToDelete = $_POST['fileToDelete'];
                                $filePath = $currentDir . '/' . $fileToDelete;

                                if (file_exists($filePath)) {
                                    unlink($filePath); // Menghapus file
                                    echo "<div class='alert alert-success'>File <strong>$fileToDelete</strong> berhasil dihapus.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>File <strong>$fileToDelete</strong> tidak ditemukan.</div>";
                                }
                            }

                            
                                // Folder and file listing
                                $files = scandir($currentDir);  // scandir digunakan untuk membaca isi dari direktori
                                if ($files === false) {
                                    echo "<tr><td colspan='4'>Tidak bisa membaca isi folder.</td></tr>";
                                } else {
                                    foreach ($files as $file) {
                                        if ($file != '.' && $file != '..') {  // Mengabaikan direktori . dan ..
                                            $filePath = $currentDir . '/' . $file;
                                            $fileSize = is_dir($filePath) ? '--' : filesize($filePath) . ' bytes';  // Cek apakah folder atau file
                                            $fileModTime = date("F d Y H:i:s", filemtime($filePath));  // Mengambil waktu modifikasi terakhir

                                            // Jika itu adalah folder
                                            if (is_dir($filePath)) {
                                                echo "<tr>
                                                        <td><i class='fas fa-folder'></i> <a href='?dir=" . htmlspecialchars($filePath) . "'>" . htmlspecialchars($file) . "</a></td>
                                                        <td>
                                                            <form method='POST' class='d-inline'>
                                                                <input type='hidden' name='oldFolderName' value='" . htmlspecialchars($file) . "'>
                                                                <input type='text' name='newFolderName' placeholder='Rename folder' class='form-control d-inline-block' style='width: 200px;' required>
                                                                <button class='btn btn-warning' name='renameFolder' type='submit'>Rename</button>
                                                            </form>
                                                            <form method='POST' class='d-inline'>
                                                                <input type='hidden' name='folderToDelete' value='" . htmlspecialchars($file) . "'>
                                                                <button class='btn btn-danger' name='deleteFolder' type='submit'>Delete</button>
                                                            </form>
                                                        </td>
                                                        <td>$fileSize</td>
                                                        <td>$fileModTime</td>
                                                    </tr>";
                                            } 
                                            // Jika itu adalah file
                                            else {
                                                echo "<tr>
                                                        <td><i class='fas fa-file'></i> " . htmlspecialchars($file) . "</td>
                                                        <td>
                                                            <form method='POST' class='d-inline'>
                                                                <input type='hidden' name='fileToDelete' value='" . htmlspecialchars($file) . "'>
                                                                <button class='btn btn-danger' name='deleteFile' type='submit'>Delete</button>
                                                            </form>
                                                        </td>
                                                        <td>$fileSize</td>
                                                        <td>$fileModTime</td>
                                                    </tr>";
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; 2024 <a href="#">Your Company</a>.</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- AdminLTE JS -->
    <script src="adminlte/plugins/jquery/jquery.min.js"></script>
    <script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
