<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config-local.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<?php
$db = new \db\Db(DB_DSN, DB_USERNAME, DB_PASSWORD);
foreach ($db->getHistory() as $line) {
    echo "<p><b>" . $line['author'] . "</b> [" . $line['time'] . "] " . $line['text']."<br></p>";
}
?>
</body>
</html>