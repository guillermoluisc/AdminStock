<?php
// app/controllers/PromocionController.php
class PromocionController {
    private $promocionModel;
    private $variedadModel;
    
    public function __construct() {
        $this->promocionModel = new Promocion();
        $this->variedadModel = new Variedad();
    }
    
    public function index() {
        $promociones = $this->promocionModel->getAll(true);
        
        $this->render('promociones/index', [
            'promociones' => $promociones
        ]);
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'variedad_id' => $_POST['variedad_id'],
                'cantidad' => $_POST['cantidad'],
                'precio_promocional' => $_POST['precio_promocional'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null
            ];
            
            if ($this->promocionModel->crear($datos)) {
                $_SESSION['mensaje'] = 'Promoción creada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=promocion&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al crear la promoción';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $variedades = $this->variedadModel->getAll();
        
        $this->render('promociones/crear', [
            'variedades' => $variedades
        ]);
    }
    
    public function editar($id) {
        $promocion = $this->promocionModel->getById($id);
        
        if (!$promocion) {
            header('Location: index.php?c=promocion&a=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'cantidad' => $_POST['cantidad'],
                'precio_promocional' => $_POST['precio_promocional'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($this->promocionModel->actualizar($id, $datos)) {
                $_SESSION['mensaje'] = 'Promoción actualizada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=promocion&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar la promoción';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $this->render('promociones/editar', ['promocion' => $promocion]);
    }
    
    public function eliminar($id) {
        if ($this->promocionModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Promoción eliminada exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar la promoción';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=promocion&a=index');
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