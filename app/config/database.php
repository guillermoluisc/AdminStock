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
        
        // Tabla de PRODUCTOS PADRE
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
        
        // Tabla de VARIEDADES
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
            unidades_por_pack INTEGER DEFAULT 3,
            FOREIGN KEY (producto_padre_id) REFERENCES productos_padre(id)
        )";
        $db->exec($sql);
        
        // Agregar columnas si no existen
        try {
            $db->exec("SELECT precio_pack3_tarjeta FROM variedades LIMIT 1");
        } catch(PDOException $e) {
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_pack3_tarjeta DECIMAL(10,2) DEFAULT 0");
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_pack3_efectivo DECIMAL(10,2) DEFAULT 0");
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_unidad_tarjeta DECIMAL(10,2) DEFAULT 0");
            $db->exec("ALTER TABLE variedades ADD COLUMN precio_unidad_efectivo DECIMAL(10,2) DEFAULT 0");
        }
        
        try {
            $db->exec("SELECT unidades_por_pack FROM variedades LIMIT 1");
        } catch(PDOException $e) {
            $db->exec("ALTER TABLE variedades ADD COLUMN unidades_por_pack INTEGER DEFAULT 3");
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

        // Tabla de EGRESOS
        $sql = "CREATE TABLE IF NOT EXISTS egresos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fecha DATE NOT NULL,
            monto DECIMAL(10,2) NOT NULL,
            descripcion TEXT NOT NULL,
            categoria VARCHAR(100),
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
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
        
        // ===================================
        // NUEVO: Sistema de Caja
        // ===================================
        
        // Tabla de MOVIMIENTOS DE CAJA
        $sql = "CREATE TABLE IF NOT EXISTS movimientos_caja (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo VARCHAR(20) NOT NULL,
            referencia_id INTEGER,
            monto_costo DECIMAL(10,2) NOT NULL,
            monto_venta DECIMAL(10,2) DEFAULT 0,
            descripcion TEXT,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        
        // Índices
        $db->exec("CREATE INDEX IF NOT EXISTS idx_movimientos_tipo ON movimientos_caja(tipo)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_movimientos_fecha ON movimientos_caja(fecha)");
        
        // Vista: Total en caja
        $db->exec("DROP VIEW IF EXISTS vista_total_caja");
        $db->exec("
            CREATE VIEW vista_total_caja AS
            SELECT 
                SUM(monto_costo) as total_caja
            FROM movimientos_caja
        ");
        
        // Vista: Desglose de movimientos
        $db->exec("DROP VIEW IF EXISTS vista_movimientos_detalle");
        $db->exec("
            CREATE VIEW vista_movimientos_detalle AS
            SELECT 
                m.id,
                m.tipo,
                m.fecha,
                m.descripcion,
                m.monto_costo,
                m.monto_venta,
                CASE 
                    WHEN m.tipo = 'venta' AND m.monto_venta > 0 
                    THEN m.monto_venta - m.monto_costo 
                    ELSE 0 
                END as ganancia,
                v.nombre_cliente,
                v.metodo_pago
            FROM movimientos_caja m
            LEFT JOIN ventas v ON m.tipo = 'venta' AND m.referencia_id = v.id
            ORDER BY m.fecha DESC
        ");
        
        // TRIGGERS para movimientos automáticos
        
        // Trigger: Compra de variedad
        $db->exec("DROP TRIGGER IF EXISTS trigger_compra_variedad");
        $db->exec("
            CREATE TRIGGER trigger_compra_variedad
            AFTER INSERT ON variedades
            BEGIN
                INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion)
                VALUES (
                    'compra',
                    NEW.id,
                    NEW.precio_compra_total,
                    'Compra: ' || NEW.nombre || ' (Stock: ' || NEW.stock || ')'
                );
            END
        ");
        
        // Trigger: Venta completada
        $db->exec("DROP TRIGGER IF EXISTS trigger_venta_caja");
        
        
        // Trigger: Formalización de pre-venta
        $db->exec("DROP TRIGGER IF EXISTS trigger_formalizar_preventa");
       
        
        // Trigger: Cancelación de venta
        $db->exec("DROP TRIGGER IF EXISTS trigger_cancelar_venta");
       
        
        // Trigger: Egreso
        $db->exec("DROP TRIGGER IF EXISTS trigger_egreso_caja");
        
        // Tabla de MIGRACIONES
        $sql = "CREATE TABLE IF NOT EXISTS migraciones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            version VARCHAR(50) NOT NULL,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        
        // Inicializar movimientos de caja con datos existentes
        self::inicializarMovimientosCaja($db);
        
        // NO insertar datos de ejemplo en producción
        // self::insertarDatosIniciales($db);
    }
    
    private static function inicializarMovimientosCaja($db) {
        // Insertar compras de variedades existentes
        $db->exec("
            INSERT OR IGNORE INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion, fecha)
            SELECT 
                'compra',
                id,
                precio_compra_total,
                'Inventario inicial: ' || nombre,
                fecha_creacion
            FROM variedades
            WHERE id NOT IN (SELECT COALESCE(referencia_id, 0) FROM movimientos_caja WHERE tipo = 'compra')
        ");
        
        // Insertar ventas completadas existentes
        $db->exec("
            INSERT OR IGNORE INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion, fecha)
            SELECT 
                'venta',
                v.id,
                (SELECT SUM(vd.cantidad * var.precio_costo_unitario)
                 FROM venta_detalles vd
                 JOIN variedades var ON vd.variedad_id = var.id
                 WHERE vd.venta_id = v.id),
                v.total,
                'Venta #' || v.id || COALESCE(' - ' || v.nombre_cliente, ''),
                v.fecha
            FROM ventas v
            WHERE v.estado = 'completada'
            AND v.id NOT IN (SELECT COALESCE(referencia_id, 0) FROM movimientos_caja WHERE tipo = 'venta')
        ");
        
        // Insertar egresos existentes
        // $db->exec("
        //     INSERT OR IGNORE INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion, fecha)
        //     SELECT 
        //         'egreso',
        //         id,
        //         monto,
        //         'Egreso: ' || descripcion,
        //         fecha
        //     FROM egresos
        //     WHERE id NOT IN (SELECT COALESCE(referencia_id, 0) FROM movimientos_caja WHERE tipo = 'egreso')
        // ");
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
    
    /**
     * NUEVO: Limpiar base de datos para distribución
     */
    public static function limpiarParaDistribucion() {
        $db = self::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            // Limpiar todas las tablas de datos
            $db->exec("DELETE FROM movimientos_caja");
            $db->exec("DELETE FROM venta_detalles");
            $db->exec("DELETE FROM ventas");
            $db->exec("DELETE FROM pedido_detalles");
            $db->exec("DELETE FROM pedidos");
            $db->exec("DELETE FROM egresos");
            $db->exec("DELETE FROM promociones");
            $db->exec("DELETE FROM variedades");
            $db->exec("DELETE FROM productos_padre");
            $db->exec("DELETE FROM migraciones");
            
            // Reiniciar autoincrement
            $db->exec("DELETE FROM sqlite_sequence");
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            return false;
        }
    }
}
?>