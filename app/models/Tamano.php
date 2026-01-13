<?php
// app/models/Tamano.php
class Tamano {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT * FROM tamanos";
        if ($soloActivos) $sql .= " WHERE activo = 1";
        $sql .= " ORDER BY nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function crear($nombre) {
        try {
            $stmt = $this->db->prepare("INSERT INTO tamanos (nombre) VALUES (?)");
            $stmt->execute([$nombre]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE tamanos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>