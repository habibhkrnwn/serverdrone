<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Jalankan perintah Python dan simpan outputnya
    $command = escapeshellcmd('python python/test_openc.py');
    $output = shell_exec($command);
    echo "<h1>Output dari Python:</h1>";
    echo "<pre>$output</pre>"; // Tampilkan output dalam format yang terformat
} else {
    // Formulir belum disubmit, tampilkan formulir
?>
    <form method="post">
        <input type="submit" value="Run Python Script">
    </form>
<?php
}
?>
