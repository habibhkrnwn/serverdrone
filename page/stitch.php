<?php
// Fungsi untuk menghitung jumlah folder
function countFolders($dir) {
    if (!is_dir($dir)) {
        return 0;  // Jika direktori tidak ada, kembalikan 0
    }
    $iterator = new DirectoryIterator($dir);
    $count = 0;
    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isDir() && !$fileinfo->isDot()) {
            $count++;
        }
    }
    return $count;
}

// Fungsi untuk mencantumkan file dalam direktori
function listFilesInDirectory($dir) {
    $files = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    $files[] = $file; // Tambahkan file ke array
                }
            }
            closedir($dh);
        }
    }
    return $files;
}

// Lokasi direktori untuk file dan folder
$folderPath = '../serverdrone/Output/Stitch/';
$folderCount = countFolders($folderPath);

$directoryPath = '../serverdrone/Output/Drone';
$filesList = listFilesInDirectory($directoryPath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Server Drone</title>
  <link rel="stylesheet" href="../adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index3.html" class="brand-link">
      <img src="../adminlte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Server Drone</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Server<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../index.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>File Explorer</p></a>
              </li>
              <li class="nav-item">
                <a href="stats.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Stats</p></a>
              </li>
              <li class="nav-item">
                <a href="stitch.php" class="nav-link active"><i class="far fa-circle nav-icon"></i><p>Stitching</p></a>
              </li>
              <li class="nav-item">
                <a href="./index3.html" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Crop Image</p></a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $folderCount; ?></h3>
                <p>Jumlah Sawah di Stitch</p>
              </div>
              <div class="icon">
                <i class="ion ion-folder"></i>
              </div>
              </div>
          </div>
          <div class="col-md-6">
            <!-- Card for file selection -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Select File from Drone Output</h3>
              </div>
              <form action="handle_files.php" method="post">
            <div class="card-body">
                <div class="form-group">
                <label for="folderSelection">Choose Folder:</label>
                <select class="custom-select" name="folderName" id="folderSelection">
                    <?php foreach ($filesList as $file): ?>
                        <option value="<?php echo htmlspecialchars($file); ?>">
                            <?php echo htmlspecialchars($file); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="action" value="submit" class="btn btn-primary">Submit</button>
                <button type="submit" name="action" value="download" class="btn btn-primary">Download</button>
            </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <footer class="main-footer">
    <strong>Tim Drone
  </footer>
</div>
<!-- Scripts -->
 <script>
    function downloadFile() {
  var selectedFile = document.getElementById('fileSelection').value;
  var path = '../serverdrone/Output/Drone/' + selectedFile;  // Sesuaikan path jika perlu
  window.open(path, '_blank'); // Membuka file dalam tab baru untuk di-download
}

 </script>
<script src="../adminlte/plugins/jquery/jquery.min.js"></script>
<script src="../adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../adminlte/dist/js/adminlte.js"></script>
</body>
</html>
