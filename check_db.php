<?php
$c = require __DIR__ . '/app/config/database.php';
try {
    $pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['host'], $c['dbname'], $c['charset']), $c['username'], $c['password']);
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'TABLES:' . implode(',', $tables) . PHP_EOL;
    if (in_array('users', $tables, true)) {
        $stmt = $pdo->query('DESCRIBE users');
        echo 'USERS:COLUMNS\n';
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo $row['Field'] . ':' . $row['Type'] . PHP_EOL;
        }
    }
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
