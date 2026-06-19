<?php
$c = require __DIR__ . '/app/config/database.php';
try {
    $pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['host'], $c['dbname'], $c['charset']), $c['username'], $c['password']);
    $stmt = $pdo->query('DESCRIBE valve_announcements');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo $col['Field'] . ' ' . $col['Type'] . ' ' . $col['Null'] . ' ' . $col['Key'] . ' ' . $col['Default'] . ' ' . $col['Extra'] . PHP_EOL;
    }
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
