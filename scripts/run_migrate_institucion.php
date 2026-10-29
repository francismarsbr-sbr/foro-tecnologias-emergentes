<?php
$cfg = require dirname(__DIR__) . '/config/database.php';
$dsn = sprintf('%s:host=%s;port=%s;dbname=%s', $cfg['driver'], $cfg['host'], $cfg['port'], $cfg['dbname']);
$pdo = new PDO($dsn, $cfg['user'], $cfg['password'], $cfg['options']);
$sql = file_get_contents(dirname(__DIR__) . '/database/migrate_institucion.sql');
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer migrate_institucion.sql\n");
    exit(1);
}
try {
    $pdo->exec($sql);
    echo "MIGRATE_OK\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'MIGRATE_FAIL: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
$cols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='estudiantes' ORDER BY ordinal_position")->fetchAll(PDO::FETCH_COLUMN);
echo 'COLUMNS: ' . implode(', ', $cols) . PHP_EOL;
