<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$command = 'node download.js'; // Ganti dengan path ke download.js
$handle = popen($command, 'r');

if ($handle) {
    while (!feof($handle)) {
        $output = fgets($handle);
        echo "data: {$output}\n\n"; // Mengirim progres ke browser
        ob_flush();
        flush();
        usleep(50000); // Menambahkan jeda untuk mengurangi beban server
    }
    pclose($handle);
}
?>
