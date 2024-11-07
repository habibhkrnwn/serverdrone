<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$command = 'node download.js';
$handle = popen($command, 'r');

if ($handle) {
    while (!feof($handle)) {
        $output = fgets($handle);
        if ($output !== false) {
            echo "data: {$output}\n\n";
            ob_flush();
            flush();
        }
        usleep(50000);
    }
    pclose($handle);
} else {
    echo "data: Error starting download\n\n";
    ob_flush();
    flush();
}
?>
