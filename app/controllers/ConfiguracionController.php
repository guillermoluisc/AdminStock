<?php
// app/controllers/ConfiguracionController.php
class ConfiguracionController {
    private $tamanoModel;
    private $saborModel;
    private $variedadSaladoModel;
    private $tipoSaladoModel;
    
    public function __construct() {
        $this->tamanoModel = new Tamano();
        $this->saborModel = new Sabor();
        $this->variedadSaladoModel = new VariedadSalado();
        $this->tipoSaladoModel = new TipoSalado();
    }
    
    public function index() {
        $this->render('configuracion/index', [
            'tamanos' => $this->tamanoModel->getAll(false),
            'sabores' => $this->saborModel->getAll(false),
            'variedades' => $this->variedadSaladoModel->getAll(false),
            'tipos_salados' => $this->tipoSaladoModel->getAll()
        ]);
    }
    
    public function agregarTamano() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            
            if ($this->tamanoModel->crear($nombre)) {
                $_SESSION['mensaje'] = 'Tamaño agregado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al agregar tamaño';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        header('Location: index.php?c=configuracion&a=index');
        exit;
    }
    
    public function agregarSabor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            
            if ($this->saborModel->crear($nombre)) {
                $_SESSION['mensaje'] = 'Sabor agregado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al agregar sabor';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        header('Location: index.php?c=configuracion&a=index');
        exit;
    }
    
    public function agregarVariedad() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_id = $_POST['tipo_salado_id'];
            $nombre = $_POST['nombre'];
            
            if ($this->variedadSaladoModel->crear($tipo_id, $nombre)) {
                $_SESSION['mensaje'] = 'Variedad agregada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al agregar variedad';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        header('Location: index.php?c=configuracion&a=index');
        exit;
    }
    
    public function eliminarTamano($id) {
        if ($this->tamanoModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Tamaño eliminado';
            $_SESSION['tipo_mensaje'] = 'success';
        }
        header('Location: index.php?c=configuracion&a=index');
        exit;
    }
    
    public function eliminarSabor($id) {
        if ($this->saborModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Sabor eliminado';
            $_SESSION['tipo_mensaje'] = 'success';
        }
        header('Location: index.php?c=configuracion&a=index');
        exit;
    }
    
    public function eliminarVariedad($id) {
        if ($this->variedadSaladoModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Variedad eliminada';
            $_SESSION['tipo_mensaje'] = 'success';
        }
        header('Location: index.php?c=configuracion&a=index');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require BASE_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();
        require BASE_PATH . '/views/layout.php';
    }
}
?>