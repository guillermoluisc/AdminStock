<?php
// app/models/Variedad.php
class Variedad {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT v.*, p.nombre as producto_padre_nombre
                FROM variedades v
                JOIN productos_padre p ON v.producto_padre_id = p.id";
        if ($soloActivos) $sql .= " WHERE v.activo = 1";
        $sql .= " ORDER BY p.nombre, v.nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getAllConFiltros($filtros = []) {
        $sql = "SELECT v.*, p.nombre as producto_padre_nombre
                FROM variedades v
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE v.activo = 1";
        
        $params = [];
        
        // Filtro por estado de stock
        if (!empty($filtros['stock_estado'])) {
            switch ($filtros['stock_estado']) {
                case 'bajo':
                    $sql .= " AND v.stock <= v.stock_minimo";
                    break;
                case 'normal':
                    $sql .= " AND v.stock > v.stock_minimo AND v.stock <= (v.stock_minimo * 2)";
                    break;
                case 'alto':
                    $sql .= " AND v.stock > (v.stock_minimo * 2)";
                    break;
                case 'sin_stock':
                    $sql .= " AND v.stock = 0";
                    break;
            }
        }
        
        // Filtro por producto padre
        if (!empty($filtros['producto_padre_id'])) {
            $sql .= " AND v.producto_padre_id = ?";
            $params[] = $filtros['producto_padre_id'];
        }
        
        $sql .= " ORDER BY p.nombre, v.nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT v.*, p.nombre as producto_padre_nombre
                FROM variedades v
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE v.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByProductoPadre($producto_padre_id, $soloActivos = true) {
        $sql = "SELECT * FROM variedades WHERE producto_padre_id = ?";
        if ($soloActivos) $sql .= " AND activo = 1";
        $sql .= " ORDER BY nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$producto_padre_id]);
        return $stmt->fetchAll();
    }
    
    public function getStockBajo() {
        $sql = "SELECT v.*, p.nombre as producto_padre_nombre
                FROM variedades v
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE v.stock <= v.stock_minimo AND v.activo = 1
                ORDER BY v.stock ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO variedades (
                producto_padre_id,
                nombre,
                descripcion,
                precio_compra_total,
                cantidad_comprada,
                precio_costo_unitario,
                precio_venta_unitario,
                stock,
                stock_minimo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $datos['producto_padre_id'],
                $datos['nombre'],
                $datos['descripcion'] ?? '',
                $datos['precio_compra_total'],
                $datos['cantidad_comprada'],
                $datos['precio_costo_unitario'],
                $datos['precio_venta_unitario'],
                $datos['stock'] ?? $datos['cantidad_comprada'],
                $datos['stock_minimo'] ?? 10
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE variedades SET 
                nombre = ?,
                descripcion = ?,
                precio_compra_total = ?,
                cantidad_comprada = ?,
                precio_costo_unitario = ?,
                precio_venta_unitario = ?,
                stock = ?,
                stock_minimo = ?,
                activo = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'] ?? '',
                $datos['precio_compra_total'],
                $datos['cantidad_comprada'],
                $datos['precio_costo_unitario'],
                $datos['precio_venta_unitario'],
                $datos['stock'],
                $datos['stock_minimo'] ?? 10,
                $datos['activo'] ?? 1,
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizarStock($id, $cantidad) {
        $stmt = $this->db->prepare("UPDATE variedades SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$cantidad, $id]);
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE variedades SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function calcularPrecioCostoUnitario($precioTotal, $cantidad) {
        if ($cantidad == 0) return 0;
        return round($precioTotal / $cantidad, 2);
    }
}
?>