<?php
// app/controllers/EgresoController.php
class EgresoController {
    private $egresoModel;
    
    public function __construct() {
        $this->egresoModel = new Egreso();
    }
    
    public function index() {
        // Obtener filtros con valores por defecto del mes actual
        $filtros = [];
        
        if (empty($_GET['fecha_desde']) && empty($_GET['fecha_hasta']) && empty($_GET['categoria'])) {
            $filtros['fecha_desde'] = date('Y-m-01');
            $filtros['fecha_hasta'] = date('Y-m-t');
        } else {
            if (!empty($_GET['fecha_desde'])) {
                $filtros['fecha_desde'] = $_GET['fecha_desde'];
            }
            if (!empty($_GET['fecha_hasta'])) {
                $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
            }
            if (!empty($_GET['categoria'])) {
                $filtros['categoria'] = $_GET['categoria'];
            }
        }
        
        $egresos = $this->egresoModel->getAll($filtros);
        $total_egresos = $this->egresoModel->getTotalEgresos($filtros);
        $categorias = $this->egresoModel->getCategorias();
        
        $this->render('egresos/index', [
            'egresos' => $egresos,
            'total_egresos' => $total_egresos,
            'categorias' => $categorias,
            'filtros' => $filtros
        ]);
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'fecha' => $_POST['fecha'],
                'monto' => $_POST['monto'],
                'descripcion' => $_POST['descripcion'],
                'categoria' => !empty($_POST['categoria']) ? $_POST['categoria'] : null
            ];
            
            if ($this->egresoModel->crear($datos)) {
                $_SESSION['mensaje'] = 'Egreso registrado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=egreso&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al registrar el egreso';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $categorias = $this->egresoModel->getCategorias();
        
        $this->render('egresos/crear', [
            'categorias' => $categorias
        ]);
    }
    
    public function editar($id) {
        $egreso = $this->egresoModel->getById($id);
        
        if (!$egreso) {
            header('Location: index.php?c=egreso&a=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'fecha' => $_POST['fecha'],
                'monto' => $_POST['monto'],
                'descripcion' => $_POST['descripcion'],
                'categoria' => !empty($_POST['categoria']) ? $_POST['categoria'] : null
            ];
            
            if ($this->egresoModel->actualizar($id, $datos)) {
                $_SESSION['mensaje'] = 'Egreso actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=egreso&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el egreso';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $categorias = $this->egresoModel->getCategorias();
        
        $this->render('egresos/editar', [
            'egreso' => $egreso,
            'categorias' => $categorias
        ]);
    }
    
    public function eliminar($id) {
        if ($this->egresoModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Egreso eliminado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el egreso';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=egreso&a=index');
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