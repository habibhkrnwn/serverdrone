<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $folderName = $_POST['folderName'];
    $folderPath = "../serverdrone/Output/Drone/" . $folderName;
    $outputPath = "../serverdrone/Output/Stitch/" . $folderName . ".JPG";

    switch ($action) {
        case 'submit':
            // Menjalankan skrip Python dengan parameter folder
            $command = escapeshellcmd("python python/stitch.py") . ' ' . escapeshellarg($folderPath) . ' ' . escapeshellarg($outputPath);
            $output = shell_exec($command);
            // Pastikan output tidak null sebelum mencetak
            if ($output === null) {
                echo "No output from Python script or script execution failed.";
            } else {
                echo "Python script output: " . htmlspecialchars($output);
            }
            break;
        case 'download':
            // Handle the download action
            $zipFileName = $folderName . '.zip';
            $zip = new ZipArchive;
            if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath), RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($folderPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
                header('Content-Length: ' . filesize($zipFileName));
                readfile($zipFileName);
                unlink($zipFileName); // Optional: Hapus file ZIP setelah diunduh
                exit;
            } else {
                echo 'Gagal membuat file ZIP';
            }
            break;
            default:
            echo "Invalid action";
            break;
    }
} else {
    echo "Invalid request";
}
?>