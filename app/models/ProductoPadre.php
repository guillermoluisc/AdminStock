<?php
// app/models/ProductoPadre.php
class ProductoPadre {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT * FROM productos_padre";
        if ($soloActivos) $sql .= " WHERE activo = 1";
        $sql .= " ORDER BY nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM productos_padre WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO productos_padre (
                nombre, 
                porcentaje_pack3_tarjeta, 
                porcentaje_pack3_efectivo,
                porcentaje_unidad_tarjeta,
                porcentaje_unidad_efectivo
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['porcentaje_pack3_tarjeta'] ?? 100,
                $datos['porcentaje_pack3_efectivo'] ?? 80,
                $datos['porcentaje_unidad_tarjeta'] ?? 100,
                $datos['porcentaje_unidad_efectivo'] ?? 80
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE productos_padre SET 
                nombre = ?,
                porcentaje_pack3_tarjeta = ?,
                porcentaje_pack3_efectivo = ?,
                porcentaje_unidad_tarjeta = ?,
                porcentaje_unidad_efectivo = ?,
                activo = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['nombre'],
                $datos['porcentaje_pack3_tarjeta'] ?? 100,
                $datos['porcentaje_pack3_efectivo'] ?? 80,
                $datos['porcentaje_unidad_tarjeta'] ?? 100,
                $datos['porcentaje_unidad_efectivo'] ?? 80,
                $datos['activo'] ?? 1,
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE productos_padre SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function calcularPrecioVenta($costo, $cantidad, $metodoPago, $producto_padre_id) {
        $producto = $this->getById($producto_padre_id);
        if (!$producto) return $costo;
        
        $porcentaje = 100;
        
        if ($cantidad == 3) {
            // Pack x3
            $porcentaje = $metodoPago == 'efectivo' 
                ? $producto['porcentaje_pack3_efectivo'] 
                : $producto['porcentaje_pack3_tarjeta'];
        } else {
            // Por unidad
            $porcentaje = $metodoPago == 'efectivo' 
                ? $producto['porcentaje_unidad_efectivo'] 
                : $producto['porcentaje_unidad_tarjeta'];
        }
        
        // Calcular precio: costo + (costo * porcentaje / 100)
        $precioFinal = $costo + ($costo * $porcentaje / 100);
        
        return round($precioFinal, 2);
    }
    
    public function cantidadVariedades($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM variedades WHERE producto_padre_id = ? AND activo = 1");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }
}
?>