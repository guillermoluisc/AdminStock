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
                precio_pack3_tarjeta,
                precio_pack3_efectivo,
                precio_unidad_tarjeta,
                precio_unidad_efectivo,
                precio_venta_unitario,
                stock,
                stock_minimo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $datos['producto_padre_id'],
                $datos['nombre'],
                $datos['descripcion'] ?? '',
                $datos['precio_compra_total'],
                $datos['cantidad_comprada'],
                $datos['precio_costo_unitario'],
                $datos['precio_pack3_tarjeta'] ?? 0,
                $datos['precio_pack3_efectivo'] ?? 0,
                $datos['precio_unidad_tarjeta'] ?? 0,
                $datos['precio_unidad_efectivo'] ?? 0,
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
                precio_pack3_tarjeta = ?,
                precio_pack3_efectivo = ?,
                precio_unidad_tarjeta = ?,
                precio_unidad_efectivo = ?,
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
                $datos['precio_pack3_tarjeta'] ?? 0,
                $datos['precio_pack3_efectivo'] ?? 0,
                $datos['precio_unidad_tarjeta'] ?? 0,
                $datos['precio_unidad_efectivo'] ?? 0,
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
    
public function calcularPreciosVenta($costo_unitario, $producto_padre_id) {
    $productoPadreModel = new ProductoPadre();
    $producto_padre = $productoPadreModel->getById($producto_padre_id);
    
    if (!$producto_padre) {
        return [
            'precio_pack3_tarjeta' => 0,
            'precio_pack3_efectivo' => 0,
            'precio_unidad_tarjeta' => 0,
            'precio_unidad_efectivo' => 0
        ];
    }
    
    // PRECIOS PACK x3 (sobre el costo total: costo_unitario × 3)
    $costo_pack = $costo_unitario * 3;
    $precio_pack3_tarjeta = $costo_pack + ($costo_pack * $producto_padre['porcentaje_pack3_tarjeta'] / 100);
    $precio_pack3_efectivo = $costo_pack + ($costo_pack * $producto_padre['porcentaje_pack3_efectivo'] / 100);
    
    // PRECIOS POR UNIDAD (sobre costo unitario)
    $precio_unidad_tarjeta = $costo_unitario + ($costo_unitario * $producto_padre['porcentaje_unidad_tarjeta'] / 100);
    $precio_unidad_efectivo = $costo_unitario + ($costo_unitario * $producto_padre['porcentaje_unidad_efectivo'] / 100);
    
    return [
        'precio_pack3_tarjeta' => round($precio_pack3_tarjeta, 2),
        'precio_pack3_efectivo' => round($precio_pack3_efectivo, 2),
        'precio_unidad_tarjeta' => round($precio_unidad_tarjeta, 2),
        'precio_unidad_efectivo' => round($precio_unidad_efectivo, 2)
    ];
}

/**
 * NUEVA FUNCIÓN: Calcula precios con contexto completo
 * Esta es la que se debe usar desde AJAX para recalcular precios
 */
public function calcularPreciosVentaCompleto($precio_compra_total, $cantidad_comprada, $producto_padre_id) {
    $productoPadreModel = new ProductoPadre();
    $producto_padre = $productoPadreModel->getById($producto_padre_id);
    
    if (!$producto_padre) {
        return [
            'costo_unitario' => 0,
            'precio_pack3_tarjeta' => 0,
            'precio_pack3_efectivo' => 0,
            'precio_unidad_tarjeta' => 0,
            'precio_unidad_efectivo' => 0
        ];
    }
    
    // 1. COSTO UNITARIO
    $costo_unitario = $cantidad_comprada > 0 ? ($precio_compra_total / $cantidad_comprada) : 0;
    
    // 2. PRECIOS PACK x3 (sobre el precio de compra total)
    // Fórmula: PrecioCompraTotal + (PrecioCompraTotal × Porcentaje/100)
    $precio_pack3_tarjeta = $precio_compra_total + ($precio_compra_total * $producto_padre['porcentaje_pack3_tarjeta'] / 100);
    $precio_pack3_efectivo = $precio_compra_total + ($precio_compra_total * $producto_padre['porcentaje_pack3_efectivo'] / 100);
    
    // 3. PRECIOS POR UNIDAD (sobre el costo unitario)
    // Fórmula: CostoUnitario + (CostoUnitario × Porcentaje/100)
    $precio_unidad_tarjeta = $costo_unitario + ($costo_unitario * $producto_padre['porcentaje_unidad_tarjeta'] / 100);
    $precio_unidad_efectivo = $costo_unitario + ($costo_unitario * $producto_padre['porcentaje_unidad_efectivo'] / 100);
    
    return [
        'costo_unitario' => round($costo_unitario, 2),
        'precio_pack3_tarjeta' => round($precio_pack3_tarjeta, 2),
        'precio_pack3_efectivo' => round($precio_pack3_efectivo, 2),
        'precio_unidad_tarjeta' => round($precio_unidad_tarjeta, 2),
        'precio_unidad_efectivo' => round($precio_unidad_efectivo, 2)
    ];
}

public function getTotalStockDisponible() {
    $sql = "SELECT SUM(precio_compra_total) as total FROM variedades WHERE activo = 1";
    $stmt = $this->db->query($sql);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}
}
?>