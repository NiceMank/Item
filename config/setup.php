<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Lancez ce script en ligne de commande : php config/setup.php\n");
}

require_once __DIR__ . '/env.php';

$cfg = db_config();
if (!preg_match('/^[A-Za-z0-9_]+$/', $cfg['name'])) {
    fwrite(STDERR, "Nom de base invalide.\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($cfg['host'], $cfg['user'], $cfg['pass']);
} catch (mysqli_sql_exception $e) {
    fwrite(STDERR, "Connexion impossible ({$cfg['user']}@{$cfg['host']}).\n");
    fwrite(STDERR, "Définissez DB_HOST, DB_USER, DB_PASS et DB_NAME, puis relancez.\n");
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$mysqli->set_charset('utf8mb4');
$name = $cfg['name'];
$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db($name);

$schema = file_get_contents(__DIR__ . '/schema.sql');
if ($schema === false) {
    fwrite(STDERR, "schema.sql introuvable.\n");
    exit(1);
}

if (!$mysqli->multi_query($schema)) {
    fwrite(STDERR, $mysqli->error . "\n");
    exit(1);
}

do {
    if ($result = $mysqli->store_result()) {
        $result->free();
    }
} while ($mysqli->more_results() && $mysqli->next_result());

if ($mysqli->errno) {
    fwrite(STDERR, $mysqli->error . "\n");
    exit(1);
}

echo "Base `{$name}` prête.\n";
echo "Compte utilisé : {$cfg['user']}@{$cfg['host']}\n";
echo "Démarrez le site avec le même environnement (DB_HOST, DB_USER, DB_PASS, DB_NAME).\n";
