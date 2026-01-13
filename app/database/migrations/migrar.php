<?php
// app/database/migrations/migrar.php
// Ejecutar: servidor\php\php.exe -f app\database\migrations\migrar.php

define('BASE_PATH', dirname(__DIR__));
define('DB_PATH', BASE_PATH . '/database/database.db');

require_once BASE_PATH . '/config/database.php';

echo "===========================================\n";
echo "  Sistema de Migraciones\n";
echo "===========================================\n\n";

// Cargar todas las migraciones del directorio
$migracionesDir = __DIR__;
$archivos = glob($migracionesDir . '/migration_*.php');

if (empty($archivos)) {
    echo "[!] No se encontraron archivos de migración\n";
    exit;
}

sort($archivos);

echo "[*] Migraciones encontradas: " . count($archivos) . "\n\n";

foreach ($archivos as $archivo) {
    $nombreArchivo = basename($archivo);
    echo "[*] Procesando: $nombreArchivo\n";
    
    // Incluir el archivo de migración
    $migracion = require $archivo;
    
    if (!isset($migracion['version']) || !isset($migracion['sql'])) {
        echo "    [ERROR] Formato de migración inválido\n";
        continue;
    }
    
    $version = $migracion['version'];
    $sql = $migracion['sql'];
    $descripcion = $migracion['descripcion'] ?? 'Sin descripción';
    
    // Ejecutar migración
    if (Database::runMigration($version, $sql)) {
        echo "    [OK] Migración $version aplicada: $descripcion\n";
    } else {
        echo "    [SKIP] Migración $version ya fue aplicada\n";
    }
}

echo "\n[✓] Proceso de migraciones completado\n\n";

// Mostrar versión actual
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT version, fecha FROM migraciones ORDER BY fecha DESC LIMIT 5");
$versiones = $stmt->fetchAll();

if ($versiones) {
    echo "Últimas migraciones aplicadas:\n";
    foreach ($versiones as $v) {
        echo "  - {$v['version']} ({$v['fecha']})\n";
    }
}

echo "\n";
?>