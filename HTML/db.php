<?php
$db_file = __DIR__ . "/../database.sqlite";
$dsn = "sqlite:" . $db_file;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, null, null, $options);
} catch (\PDOException $e) {
    die("<h1>Database Connection Failed</h1><p>" . $e->getMessage() . "</p>");
}
?>