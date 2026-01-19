<?php
// app/models/Pedido.php
class Pedido {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filtros = []) {
        $sql = "SELECT p.*, 
                COUNT(pd.id) as cantidad_items,
                SUM(pd.cantidad_solicitada) as total_unidades
                FROM pedidos p
                LEFT JOIN pedido_detalles pd ON p.id = pd.pedido_id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por fecha
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(p.fecha_creacion) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(p.fecha_creacion) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getDetalles($pedido_id) {
        $sql = "SELECT pd.*, v.nombre as variedad_nombre, v.stock, v.stock_minimo,
                p.nombre as producto_padre_nombre
                FROM pedido_detalles pd
                JOIN variedades v ON pd.variedad_id = v.id
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE pd.pedido_id = ?
                ORDER BY p.nombre, v.nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll();
    }
    
    public function crear($observaciones, $detalles) {
        try {
            $this->db->beginTransaction();
            
            // Crear pedido
            $stmt = $this->db->prepare("INSERT INTO pedidos (observaciones) VALUES (?)");
            $stmt->execute([$observaciones]);
            $pedido_id = $this->db->lastInsertId();
            
            // Insertar detalles
            $stmtDetalle = $this->db->prepare(
                "INSERT INTO pedido_detalles (pedido_id, variedad_id, cantidad_solicitada, precio_estimado, observaciones) 
                VALUES (?, ?, ?, ?, ?)"
            );
            
            foreach ($detalles as $detalle) {
                $stmtDetalle->execute([
                    $pedido_id,
                    $detalle['variedad_id'],
                    $detalle['cantidad_solicitada'],
                    $detalle['precio_estimado'] ?? null,
                    $detalle['observaciones'] ?? ''
                ]);
            }
            
            $this->db->commit();
            return $pedido_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE pedidos SET 
                estado = ?,
                observaciones = ?,
                fecha_realizacion = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['estado'],
                $datos['observaciones'] ?? '',
                $datos['estado'] == 'realizado' ? date('Y-m-d H:i:s') : null,
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizarDetalle($detalle_id, $cantidad_recibida, $observaciones = '') {
        try {
            $this->db->beginTransaction();
            
            // Actualizar detalle del pedido
            $stmt = $this->db->prepare(
                "UPDATE pedido_detalles SET cantidad_recibida = ?, observaciones = ? WHERE id = ?"
            );
            $stmt->execute([$cantidad_recibida, $observaciones, $detalle_id]);
            
            // Obtener variedad_id y actualizar stock si se recibió mercadería
            if ($cantidad_recibida > 0) {
                $stmt = $this->db->prepare("SELECT variedad_id FROM pedido_detalles WHERE id = ?");
                $stmt->execute([$detalle_id]);
                $detalle = $stmt->fetch();
                
                if ($detalle) {
                    $stmt = $this->db->prepare("UPDATE variedades SET stock = stock + ? WHERE id = ?");
                    $stmt->execute([$cantidad_recibida, $detalle['variedad_id']]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function marcarComoRealizado($id) {
        $stmt = $this->db->prepare(
            "UPDATE pedidos SET estado = 'realizado', fecha_realizacion = ? WHERE id = ?"
        );
        return $stmt->execute([date('Y-m-d H:i:s'), $id]);
    }
    
    public function marcarComoFaltante($id, $observaciones = '') {
        $stmt = $this->db->prepare(
            "UPDATE pedidos SET estado = 'faltante', observaciones = ? WHERE id = ?"
        );
        return $stmt->execute([$observaciones, $id]);
    }
    
    public function eliminar($id) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar detalles
            $stmt = $this->db->prepare("DELETE FROM pedido_detalles WHERE pedido_id = ?");
            $stmt->execute([$id]);
            
            // Eliminar pedido
            $stmt = $this->db->prepare("DELETE FROM pedidos WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>