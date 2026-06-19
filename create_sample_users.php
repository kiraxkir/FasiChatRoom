<?php

require_once __DIR__ . '/app/config/bootstrap.php';

use App\Services\BaseDeDonnees;

$pdo = BaseDeDonnees::getInstance()->getConnection();

$users = [
    [
        'matricule' => 'SI2024001',
        'full_name' => 'Étudiant Exemple',
        'email' => 'etu1@example.com',
        'password' => 'EtuPass123!',
        'role' => 'etudiant',
        'promo_id' => 1,
    ],
    [
        'matricule' => 'ENS2024001',
        'full_name' => 'Enseignant Exemple',
        'email' => 'ens1@example.com',
        'password' => 'EnsPass123!',
        'role' => 'enseignant',
        'promo_id' => null,
    ],
    [
        'matricule' => 'ASS2024001',
        'full_name' => 'Assistant Exemple',
        'email' => 'ass1@example.com',
        'password' => 'AssPass123!',
        'role' => 'assistant',
        'promo_id' => null,
    ],
];

$roleStmt = $pdo->query("SELECT id, name FROM roles WHERE name IN ('etudiant', 'enseignant', 'assistant')");
$roleIds = [];
while ($row = $roleStmt->fetch()) {
    $roleIds[$row['name']] = $row['id'];
}

$insertSql = 'INSERT INTO users (matricule, full_name, email, password_hash, role_id, created_at, promo_id)
              VALUES (:matricule, :full_name, :email, :password_hash, :role_id, :created_at, :promo_id)';
$insertStmt = $pdo->prepare($insertSql);
$checkStmt = $pdo->prepare('SELECT id FROM users WHERE matricule = :matricule');

foreach ($users as $user) {
    if (!isset($roleIds[$user['role']])) {
        echo sprintf("Erreur rôle introuvable : %s\n", $user['role']);
        continue;
    }

    $checkStmt->execute([':matricule' => $user['matricule']]);
    if ($checkStmt->fetch()) {
        echo sprintf("Compte déjà existant : %s\n", $user['matricule']);
        continue;
    }

    $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);
    $params = [
        ':matricule' => $user['matricule'],
        ':full_name' => $user['full_name'],
        ':email' => $user['email'],
        ':password_hash' => $passwordHash,
        ':role_id' => $roleIds[$user['role']],
        ':created_at' => date('Y-m-d H:i:s'),
        ':promo_id' => $user['promo_id'],
    ];

    try {
        $insertStmt->execute($params);
        echo sprintf("Compte créé : %s\n", $user['matricule']);
    } catch (Exception $e) {
        echo sprintf("Erreur création %s : %s\n", $user['matricule'], $e->getMessage());
    }
}
