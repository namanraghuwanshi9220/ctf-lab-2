<?php
// broken-login-cdn/html/init_db.php
$dbDir = __DIR__ . '/db';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

$dbFile = $dbDir . '/database.sqlite';
$needsSetup = !file_exists($dbFile);

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($needsSetup) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'user'
        )");

        $adminUser = 'admin';
        $adminPass = password_hash('VerySecureAdminPass123!', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->execute([':username' => $adminUser, ':password' => $adminPass, ':role' => 'admin']);

        $regUser = 'pleb';
        $regPass = password_hash('userpass', PASSWORD_DEFAULT);
        $stmt->execute([':username' => $regUser, ':password' => $regPass, ':role' => 'user']);
    }
} catch (PDOException $e) {
    die("DB Init Error: " . $e->getMessage());
}
?>
