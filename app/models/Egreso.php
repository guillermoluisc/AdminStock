<?php
// app/models/Egreso.php
class Egreso {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM egresos WHERE 1=1";
        $params = [];
        
        // Filtro por fecha
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND fecha >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND fecha <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        // Filtro por categoría
        if (!empty($filtros['categoria'])) {
            $sql .= " AND categoria = ?";
            $params[] = $filtros['categoria'];
        }
        
        $sql .= " ORDER BY fecha DESC, fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM egresos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO egresos (fecha, monto, descripcion, categoria) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $datos['fecha'],
                $datos['monto'],
                $datos['descripcion'],
                $datos['categoria'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE egresos SET fecha = ?, monto = ?, descripcion = ?, categoria = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['fecha'],
                $datos['monto'],
                $datos['descripcion'],
                $datos['categoria'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM egresos WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getTotalEgresos($filtros = []) {
        $sql = "SELECT SUM(monto) as total FROM egresos WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND fecha >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND fecha <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($filtros['categoria'])) {
            $sql .= " AND categoria = ?";
            $params[] = $filtros['categoria'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
    
    public function getCategorias() {
        $sql = "SELECT DISTINCT categoria FROM egresos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>