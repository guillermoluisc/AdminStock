<?php
// app/config/database.php
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new PDO('sqlite:' . DB_PATH);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public static function initialize() {
        $dbDir = dirname(DB_PATH);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }
        
        $db = self::getInstance()->getConnection();
        
        // Tabla de PRODUCTOS PADRE (categorías generales)
        $sql = "CREATE TABLE IF NOT EXISTS productos_padre (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre VARCHAR(200) NOT NULL,
            porcentaje_pack3_tarjeta DECIMAL(5,2) DEFAULT 100,
            porcentaje_pack3_efectivo DECIMAL(5,2) DEFAULT 80,
            porcentaje_unidad_tarjeta DECIMAL(5,2) DEFAULT 100,
            porcentaje_unidad_efectivo DECIMAL(5,2) DEFAULT 80,
            activo INTEGER DEFAULT 1,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        
        // Tabla de VARIEDADES (productos hijos) - ACTUALIZADA CON 4 PRECIOS
        $sql = "CREATE TABLE IF NOT EXISTS variedades (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            producto_padre_id INTEGER NOT NULL,
            nombre VARCHAR(200) NOT NULL,
            descripcion TEXT,
            precio_compra_total DECIMAL(10,2) NOT NULL,
            cantidad_comprada INTEGER NOT NULL,
            precio_costo_unitario DECIMAL(10,2) NOT NULL,
            precio_pack3_tarjeta DECIMAL(10,2) DEFAULT 0,
            precio_pack3_efectivo DECIMAL(10,2) DEFAULT 0,
            precio_unidad_tarjeta DECIMAL(10,2) DEFAULT 0,
            precio_unidad_efectivo DECIMAL(10,2) DEFAULT 0,
            precio_venta_unitario DECIMAL(10,2) NOT NULL,
            stock INTEGER DEFAULT 0,
            stock_minimo INTEGER DEFAULT 10,
            activo INTEGER DEFAULT 1,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (producto_padre_id) REFERENCES productos_padre(id)
        )";
        $db->exec($sql);
        
        // Verificar si las columnas ya existen (para bases de datos existentes)
        try {
            $db->exec("SELECT precio_pack3_tarjeta FROM variedades LIMIT 1");
        } catch(PDOException $e) {
            // Si no existe, agregar las columnas
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_pack3_tarjeta DECIMAL(10,2) DEFAULT 0");
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_pack3_efectivo DECIMAL(10,2) DEFAULT 0");
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_unidad_tarjeta DECIMAL(10,2) DEFAULT 0");
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_unidad_efectivo DECIMAL(10,2) DEFAULT 0");
        }
        
        // Tabla de PROMOCIONES
        $sql = "CREATE TABLE IF NOT EXISTS promociones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            variedad_id INTEGER NOT NULL,
            cantidad INTEGER NOT NULL,
            precio_promocional DECIMAL(10,2) NOT NULL,
            activo INTEGER DEFAULT 1,
            fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_fin DATETIME,
            FOREIGN KEY (variedad_id) REFERENCES variedades(id)
        )";
        $db->exec($sql);
        
        // Tabla de VENTAS
        $sql = "CREATE TABLE IF NOT EXISTS ventas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            total DECIMAL(10,2) NOT NULL,
            metodo_pago VARCHAR(20) NOT NULL,
            descuento_aplicado DECIMAL(10,2) DEFAULT 0,
            nombre_cliente VARCHAR(200),
            estado VARCHAR(20) DEFAULT 'completada',
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_formalizacion DATETIME
        )";
        $db->exec($sql);

        try {
            $db->exec("SELECT nombre_cliente FROM ventas LIMIT 1");
        } catch(PDOException $e) {
            $db->exec("ALTER TABLE ventas ADD COLUMN nombre_cliente VARCHAR(200)");
            $db->exec("ALTER TABLE ventas ADD COLUMN estado VARCHAR(20) DEFAULT 'completada'");
            $db->exec("ALTER TABLE ventas ADD COLUMN fecha_formalizacion DATETIME");
        }
        
        // Tabla de DETALLE DE VENTAS
        $sql = "CREATE TABLE IF NOT EXISTS venta_detalles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            venta_id INTEGER NOT NULL,
            variedad_id INTEGER NOT NULL,
            cantidad INTEGER NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            descuento_unitario DECIMAL(10,2) DEFAULT 0,
            subtotal DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (venta_id) REFERENCES ventas(id),
            FOREIGN KEY (variedad_id) REFERENCES variedades(id)
        )";
        $db->exec($sql);
        
        // Tabla de PEDIDOS
        $sql = "CREATE TABLE IF NOT EXISTS pedidos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            estado VARCHAR(20) DEFAULT 'pendiente',
            observaciones TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_realizacion DATETIME
        )";
        $db->exec($sql);
        
        // Tabla de DETALLE DE PEDIDOS
        $sql = "CREATE TABLE IF NOT EXISTS pedido_detalles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pedido_id INTEGER NOT NULL,
            variedad_id INTEGER NOT NULL,
            cantidad_solicitada INTEGER NOT NULL,
            cantidad_recibida INTEGER DEFAULT 0,
            precio_estimado DECIMAL(10,2),
            observaciones TEXT,
            FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
            FOREIGN KEY (variedad_id) REFERENCES variedades(id)
        )";
        $db->exec($sql);
        
        // Tabla de MIGRACIONES
        $sql = "CREATE TABLE IF NOT EXISTS migraciones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            version VARCHAR(50) NOT NULL,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        
        // Insertar datos iniciales de ejemplo
        self::insertarDatosIniciales($db);
    }
    
    private static function insertarDatosIniciales($db) {
        // Verificar si ya hay datos
        $count = $db->query("SELECT COUNT(*) FROM productos_padre")->fetchColumn();
        if ($count > 0) return;
        
        // Productos padre de ejemplo
        $db->exec("INSERT INTO productos_padre (nombre, porcentaje_pack3_tarjeta, porcentaje_pack3_efectivo, porcentaje_unidad_tarjeta, porcentaje_unidad_efectivo) VALUES 
            ('Colaless/Vedetina/Culot', 100, 80, 100, 80),
            ('Remera Básica', 100, 80, 100, 80),
            ('Pantalón Jean', 100, 85, 100, 85)");
        
        // Variedades de ejemplo
        $db->exec("INSERT INTO variedades (producto_padre_id, nombre, descripcion, precio_compra_total, cantidad_comprada, precio_costo_unitario, precio_pack3_tarjeta, precio_pack3_efectivo, precio_unidad_tarjeta, precio_unidad_efectivo, precio_venta_unitario, stock, stock_minimo) VALUES 
            (1, 'Colaless VINTAGE Negro M', 'Colaless negra talle M', 100000, 10, 10000, 20000, 18000, 6666.67, 6000, 10000, 10, 5),
            (2, 'Remera Básica Blanca L', 'Remera blanca talle L', 100000, 10, 10000, 20000, 18000, 6666.67, 6000, 12000, 8, 5),
            (3, 'Jean Azul 32', 'Jean azul talle 32', 150000, 8, 18750, 37500, 34687.5, 12500, 11562.5, 22000, 8, 3)");
    }
    
    public static function runMigration($version, $sql) {
        $db = self::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT COUNT(*) FROM migraciones WHERE version = ?");
        $stmt->execute([$version]);
        
        if ($stmt->fetchColumn() == 0) {
            $db->exec($sql);
            $stmt = $db->prepare("INSERT INTO migraciones (version) VALUES (?)");
            $stmt->execute([$version]);
            return true;
        }
        return false;
    }
}
?>