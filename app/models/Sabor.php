<?php
// app/models/Sabor.php
class Sabor {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT * FROM sabores";
        if ($soloActivos) $sql .= " WHERE activo = 1";
        $sql .= " ORDER BY nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function crear($nombre) {
        try {
            $stmt = $this->db->prepare("INSERT INTO sabores (nombre) VALUES (?)");
            $stmt->execute([$nombre]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE sabores SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>