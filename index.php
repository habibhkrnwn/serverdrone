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

    <!-- JavaScript untuk AJAX download -->
    <script>
    function downloadData(event) {
        event.preventDefault(); // Mencegah reload halaman
        const eventSource = new EventSource('runDownload.php');
        
        eventSource.onmessage = function(event) {
            const progressText = document.getElementById('progressText');
            const progressBar = document.getElementById('progressBar');
            
            // Mengambil progres dalam persentase dari data event
            const progressData = event.data.trim();
            progressText.textContent = progressData;
            
            // Mendapatkan nilai progres dalam persen jika ada
            const progressMatch = progressData.match(/(\d+(\.\d+)?)%/);
            if (progressMatch) {
                const progressPercent = parseFloat(progressMatch[1]);
                progressBar.style.width = progressPercent + '%';
                progressBar.setAttribute('aria-valuenow', progressPercent);
                progressBar.textContent = progressPercent + '%';
            }
            
            // Cek jika sudah mencapai 100%
            if (progressData.includes("100%")) {
                eventSource.close();
                alert('Download completed!');
            }
        };

        eventSource.onerror = function() {
            eventSource.close();
            alert('Error during download.');
        };
    }
</script>

</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item">
                    <!-- Tambahkan atribut onclick untuk memanggil downloadData() -->
                    <a class="nav-link" data-widget="downloadjs" href="#" onclick="downloadData(event)" role="button"><i class="fas fa-download"></i> Download Data</a>
                </li>                
            </ul>
        </nav>
        <!-- Progres bar -->
        <!-- Progress Bar -->
<div id="progressContainer" style="padding: 20px;">
    <div class="progress">
        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
             style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    <div id="progressText" style="margin-top: 10px; font-size: 16px;">Waiting for download to start...</div>
</div>
        <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="adminlte/dist/img/AdminLTELogo.png"  class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Server Drone</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Server
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>File Explorer</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="page/stats.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Stats</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="page/stitch.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Stitching</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index3.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crop Image</p>
                </a>
              </li>
            </ul>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
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
                                // Include function to delete folders with content
                                include 'deleteFolder.php';  // Assume deleteFolder function is in deleteFolder.php

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

                                    if (is_dir($folderPath)) {
                                        deleteFolder($folderPath);  // Memanggil fungsi deleteFolder
                                        echo "<div class='alert alert-success'>Folder <strong>$folderToDelete</strong> berhasil dihapus.</div>";
                                    } else {
                                        echo "<div class='alert alert-danger'>Folder <strong>$folderToDelete</strong> tidak ditemukan.</div>";
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
