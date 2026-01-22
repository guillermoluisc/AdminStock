<?php
// app/models/MovimientoCaja.php
class MovimientoCaja {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener el total actual en caja (a precio de costo)
     */
    public function getTotalCaja() {
        $sql = "SELECT total_caja FROM vista_total_caja";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total_caja'] ?? 0;
    }
    
    /**
     * Obtener todos los movimientos con filtros
     */
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM vista_movimientos_detalle WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($filtros['tipo'])) {
            $sql .= " AND tipo = ?";
            $params[] = $filtros['tipo'];
        }
        
        $sql .= " ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Registrar un movimiento manual (para ajustes)
     */
    public function registrarAjuste($monto, $descripcion) {
        try {
            $sql = "INSERT INTO movimientos_caja (tipo, monto_costo, descripcion) VALUES ('ajuste', ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$monto, $descripcion]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener resumen de movimientos por tipo
     */
    public function getResumen($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT 
                tipo,
                COUNT(*) as cantidad,
                SUM(monto_costo) as total_costo,
                SUM(monto_venta) as total_venta,
                SUM(CASE WHEN tipo = 'venta' THEN monto_venta - monto_costo ELSE 0 END) as ganancia
                FROM movimientos_caja
                WHERE 1=1";
        
        $params = [];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $sql .= " GROUP BY tipo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener ganancia total del período
     */
    public function getGananciaTotal($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT SUM(monto_venta - monto_costo) as ganancia
                FROM movimientos_caja
                WHERE tipo = 'venta'";
        
        $params = [];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['ganancia'] ?? 0;
    }
    
    /**
     * Recalcular toda la caja (útil para correcciones)
     */
    public function recalcularCaja() {
        try {
            $this->db->beginTransaction();
            
            // Limpiar movimientos existentes
            $this->db->exec("DELETE FROM movimientos_caja");
            
            // Reinsertar compras (variedades)
            $this->db->exec("
                INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion, fecha)
                SELECT 
                    'compra',
                    id,
                    precio_compra_total,
                    'Inventario: ' || nombre,
                    fecha_creacion
                FROM variedades
                WHERE activo = 1
            ");
            
            // Reinsertar ventas completadas
            $this->db->exec("
                INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion, fecha)
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
            ");
            
            // Reinsertar egresos
            $this->db->exec("
                INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion, fecha)
                SELECT 
                    'egreso',
                    id,
                    monto,
                    'Egreso: ' || descripcion,
                    fecha
                FROM egresos
            ");
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error recalculando caja: " . $e->getMessage());
            return false;
        }
    }
}