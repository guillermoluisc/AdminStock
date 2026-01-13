<?php
// app/models/Promocion.php
class Promocion {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT pr.*, v.nombre as variedad_nombre, p.nombre as producto_padre_nombre
                FROM promociones pr
                JOIN variedades v ON pr.variedad_id = v.id
                JOIN productos_padre p ON v.producto_padre_id = p.id";
        if ($soloActivos) $sql .= " WHERE pr.activo = 1";
        $sql .= " ORDER BY p.nombre, v.nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT pr.*, v.nombre as variedad_nombre, p.nombre as producto_padre_nombre
                FROM promociones pr
                JOIN variedades v ON pr.variedad_id = v.id
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE pr.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByVariedad($variedad_id, $soloActivos = true) {
        $sql = "SELECT * FROM promociones WHERE variedad_id = ?";
        if ($soloActivos) $sql .= " AND activo = 1";
        $sql .= " ORDER BY cantidad";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$variedad_id]);
        return $stmt->fetchAll();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO promociones (
                variedad_id,
                cantidad,
                precio_promocional,
                fecha_inicio,
                fecha_fin
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $datos['variedad_id'],
                $datos['cantidad'],
                $datos['precio_promocional'],
                $datos['fecha_inicio'] ?? date('Y-m-d H:i:s'),
                $datos['fecha_fin'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE promociones SET 
                cantidad = ?,
                precio_promocional = ?,
                fecha_inicio = ?,
                fecha_fin = ?,
                activo = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['cantidad'],
                $datos['precio_promocional'],
                $datos['fecha_inicio'] ?? date('Y-m-d H:i:s'),
                $datos['fecha_fin'] ?? null,
                $datos['activo'] ?? 1,
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE promociones SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function aplicarPromocion($variedad_id, $cantidad) {
        // Buscar la mejor promoción aplicable
        $sql = "SELECT * FROM promociones 
                WHERE variedad_id = ? AND activo = 1 AND cantidad <= ?
                ORDER BY cantidad DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$variedad_id, $cantidad]);
        $promo = $stmt->fetch();
        
        if ($promo) {
            $cantidadPromos = floor($cantidad / $promo['cantidad']);
            $sobrante = $cantidad % $promo['cantidad'];
            
            return [
                'aplica' => true,
                'cantidad_promos' => $cantidadPromos,
                'sobrante' => $sobrante,
                'precio_promo' => $promo['precio_promocional'],
                'cantidad_promo' => $promo['cantidad']
            ];
        }
        
        return ['aplica' => false];
    }
}
?>