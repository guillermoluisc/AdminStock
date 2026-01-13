<?php
// app/models/TipoSalado.php
class TipoSalado {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($soloActivos = true) {
        $sql = "SELECT * FROM tipos_salados";
        if ($soloActivos) $sql .= " WHERE activo = 1";
        $sql .= " ORDER BY nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
?>