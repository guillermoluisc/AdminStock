<?php
// app/index.php - Router principal
session_start();

date_default_timezone_set('America/Argentina/Buenos_Aires');
define('BASE_PATH', __DIR__);
define('DB_PATH', BASE_PATH . '/database/database.db');

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/helpers/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once BASE_PATH . '/config/database.php';
Database::initialize();

$controller = $_GET['c'] ?? 'variedad';
$action = $_GET['a'] ?? 'index';
$id = $_GET['id'] ?? null;

$controllerName = ucfirst($controller) . 'Controller';

if (class_exists($controllerName)) {
    $controllerInstance = new $controllerName();
    
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action($id);
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Acción no encontrada";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Controlador no encontrado";
}
?>