<?php
// app/models/VariedadSalado.php
class VariedadSalado {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT vs.*, ts.nombre as tipo_nombre 
                FROM variedades_salados vs
                JOIN tipos_salados ts ON vs.tipo_salado_id = ts.id";
        if ($soloActivos) $sql .= " WHERE vs.activo = 1";
        $sql .= " ORDER BY ts.nombre, vs.nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getByTipo($tipo_id, $soloActivos = true) {
        $sql = "SELECT * FROM variedades_salados WHERE tipo_salado_id = ?";
        if ($soloActivos) $sql .= " AND activo = 1";
        $sql .= " ORDER BY nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipo_id]);
        return $stmt->fetchAll();
    }
    
    public function crear($tipo_id, $nombre) {
        try {
            $stmt = $this->db->prepare("INSERT INTO variedades_salados (tipo_salado_id, nombre) VALUES (?, ?)");
            $stmt->execute([$tipo_id, $nombre]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE variedades_salados SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>