<?php
$file = __DIR__ . '/config/config.example.php';
$newfile = __DIR__ . '/config/config-local.php';

if (!copy($file, $newfile)) {
    echo "Could not copy file $file...\n";
    exit;
}
echo "Created file " . $newfile."\n";