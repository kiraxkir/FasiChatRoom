<?php
$c = require __DIR__ . '/app/config/database.php';
try {
    $pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['host'], $c['dbname'], $c['charset']), $c['username'], $c['password']);
    $stmt = $pdo->query('SELECT id, matricule, full_name, email, role_id, status FROM users ORDER BY id LIMIT 20');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($users)) {
        echo "Aucun utilisateur trouvé.\n";
        exit;
    }
    foreach ($users as $user) {
        echo sprintf("%d | %s | %s | %s | %s | %s\n", $user['id'], $user['matricule'], $user['full_name'], $user['email'], $user['role_id'], $user['status']);
    }
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
