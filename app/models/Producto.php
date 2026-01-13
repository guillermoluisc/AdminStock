<?php
// app/models/Producto.php
class Producto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = false) {
        $sql = "SELECT p.*, 
                t.nombre as tamano_nombre,
                s.nombre as sabor_nombre,
                vs.nombre as variedad_nombre,
                ts.nombre as tipo_salado_nombre
                FROM productos p
                LEFT JOIN tamanos t ON p.tamano_id = t.id
                LEFT JOIN sabores s ON p.sabor_id = s.id
                LEFT JOIN variedades_salados vs ON p.variedad_salado_id = vs.id
                LEFT JOIN tipos_salados ts ON vs.tipo_salado_id = ts.id";
        
        if ($soloActivos) {
            $sql .= " WHERE p.activo = 1";
        }
        $sql .= " ORDER BY p.tipo, p.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, 
                t.nombre as tamano_nombre,
                s.nombre as sabor_nombre,
                vs.nombre as variedad_nombre
                FROM productos p
                LEFT JOIN tamanos t ON p.tamano_id = t.id
                LEFT JOIN sabores s ON p.sabor_id = s.id
                LEFT JOIN variedades_salados vs ON p.variedad_salado_id = vs.id
                WHERE p.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO productos (tipo, tamano_id, sabor_id, variedad_salado_id, stock, precio_compra, precio_venta, precio_promo, cantidad_promo) 
                    VALUES (:tipo, :tamano_id, :sabor_id, :variedad_salado_id, :stock, :precio_compra, :precio_venta, :precio_promo, :cantidad_promo)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':tipo' => $datos['tipo'],
                ':tamano_id' => $datos['tamano_id'] ?: null,
                ':sabor_id' => $datos['sabor_id'] ?: null,
                ':variedad_salado_id' => $datos['variedad_salado_id'] ?: null,
                ':stock' => $datos['stock'] ?? 0,
                ':precio_compra' => $datos['precio_compra'] ?? 0,
                ':precio_venta' => $datos['precio_venta'],
                ':precio_promo' => $datos['precio_promo'] ?: null,
                ':cantidad_promo' => $datos['cantidad_promo'] ?: null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE productos 
                    SET stock = :stock,
                        precio_compra = :precio_compra,
                        precio_venta = :precio_venta,
                        precio_promo = :precio_promo,
                        cantidad_promo = :cantidad_promo,
                        activo = :activo
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':stock' => $datos['stock'],
                ':precio_compra' => $datos['precio_compra'],
                ':precio_venta' => $datos['precio_venta'],
                ':precio_promo' => $datos['precio_promo'] ?: null,
                ':cantidad_promo' => $datos['cantidad_promo'] ?: null,
                ':activo' => $datos['activo'] ?? 1
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizarStock($id, $cantidad) {
        $stmt = $this->db->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$cantidad, $id]);
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getNombreCompleto($producto) {
        if ($producto['tipo'] == 'dulce') {
            return $producto['tamano_nombre'] . ' - ' . $producto['sabor_nombre'];
        } else {
            return $producto['variedad_nombre'];
        }
    }
}
?>