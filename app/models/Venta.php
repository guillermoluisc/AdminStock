<?php
// app/models/Venta.php
class Venta {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM ventas WHERE 1=1";
        $params = [];
        
        // Filtro por fecha
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        // Filtro por método de pago
        if (!empty($filtros['metodo_pago'])) {
            $sql .= " AND metodo_pago = ?";
            $params[] = $filtros['metodo_pago'];
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filtros['estado'];
        } else {
            // Por defecto no mostrar canceladas
            $sql .= " AND estado != 'cancelada'";
        }
        
        $sql .= " ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ventas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getDetalles($venta_id) {
        $sql = "SELECT vd.*, v.nombre as variedad_nombre, p.nombre as producto_padre_nombre
                FROM venta_detalles vd
                JOIN variedades v ON vd.variedad_id = v.id
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE vd.venta_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll();
    }
    
    public function getVentasPorProducto($variedad_id, $fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT SUM(vd.cantidad) as total_vendido, COUNT(DISTINCT vd.venta_id) as cantidad_ventas
                FROM venta_detalles vd
                JOIN ventas v ON vd.venta_id = v.id
                WHERE vd.variedad_id = ? AND v.estado != 'cancelada'";
        
        $params = [$variedad_id];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(v.fecha) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(v.fecha) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public function crear($total, $metodo_pago, $descuento_aplicado, $detalles, $nombre_cliente = null, $es_preventa = false) {
        try {
            $this->db->beginTransaction();
            
            // Determinar estado inicial
            $estado = $es_preventa ? 'preventa' : 'completada';
            $fecha_formalizacion = $es_preventa ? null : date('Y-m-d H:i:s');
            
            // Crear venta
            $stmt = $this->db->prepare("INSERT INTO ventas (total, metodo_pago, descuento_aplicado, nombre_cliente, estado, fecha_formalizacion) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$total, $metodo_pago, $descuento_aplicado, $nombre_cliente, $estado, $fecha_formalizacion]);
            $venta_id = $this->db->lastInsertId();
            
            // Insertar detalles y actualizar stock
            $stmtDetalle = $this->db->prepare(
                "INSERT INTO venta_detalles (venta_id, variedad_id, cantidad, precio_unitario, descuento_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            $stmtStock = $this->db->prepare("UPDATE variedades SET stock = stock - ? WHERE id = ?");
            
            foreach ($detalles as $detalle) {
                $stmtDetalle->execute([
                    $venta_id,
                    $detalle['variedad_id'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['descuento_unitario'] ?? 0,
                    $detalle['subtotal']
                ]);
                
                // Descontar stock (tanto para venta completa como pre-venta)
                $stmtStock->execute([
                    $detalle['cantidad'],
                    $detalle['variedad_id']
                ]);
            }
            
            $this->db->commit();
            return $venta_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function formalizarPreventa($id) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE ventas SET estado = 'completada', fecha_formalizacion = ? WHERE id = ? AND estado = 'preventa'"
            );
            return $stmt->execute([date('Y-m-d H:i:s'), $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function cancelarPreventa($id) {
        try {
            $this->db->beginTransaction();
            
            // Obtener detalles de la venta
            $detalles = $this->getDetalles($id);
            
            // Devolver stock
            $stmtStock = $this->db->prepare("UPDATE variedades SET stock = stock + ? WHERE id = ?");
            foreach ($detalles as $detalle) {
                $stmtStock->execute([$detalle['cantidad'], $detalle['variedad_id']]);
            }
            
            // Marcar venta como cancelada
            $stmt = $this->db->prepare(
                "UPDATE ventas SET estado = 'cancelada' WHERE id = ? AND estado = 'preventa'"
            );
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getTotalVentas($filtros = []) {
        $sql = "SELECT SUM(total) as total FROM ventas WHERE estado != 'cancelada'";
        $params = [];
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($filtros['metodo_pago'])) {
            $sql .= " AND metodo_pago = ?";
            $params[] = $filtros['metodo_pago'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
    
    public function getEstadisticas($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT 
                COUNT(*) as total_ventas,
                SUM(total) as total_vendido,
                AVG(total) as promedio_venta,
                SUM(CASE WHEN metodo_pago = 'efectivo' THEN total ELSE 0 END) as total_efectivo,
                SUM(CASE WHEN metodo_pago = 'tarjeta' THEN total ELSE 0 END) as total_tarjeta,
                SUM(CASE WHEN metodo_pago = 'transferencia' THEN total ELSE 0 END) as total_transferencia
                FROM ventas WHERE estado = 'completada'";
        
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
        
        // Combinar tarjeta y transferencia en un solo campo para compatibilidad
        if ($result) {
            $result['total_tarjeta_combinado'] = ($result['total_tarjeta'] ?? 0) + ($result['total_transferencia'] ?? 0);
        }
        
        return $result;
    }
}
?>