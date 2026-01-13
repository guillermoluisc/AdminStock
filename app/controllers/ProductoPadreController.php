<?php
// app/controllers/ProductoPadreController.php
class ProductoPadreController {
    private $productoPadreModel;
    
    public function __construct() {
        $this->productoPadreModel = new ProductoPadre();
    }
    
    public function index() {
        $productos = $this->productoPadreModel->getAll(true); // Cambiar false a true
        
        $this->render('productos_padre/index', [
            'productos' => $productos
        ]);
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'porcentaje_pack3_tarjeta' => $_POST['porcentaje_pack3_tarjeta'] ?? 100,
                'porcentaje_pack3_efectivo' => $_POST['porcentaje_pack3_efectivo'] ?? 80,
                'porcentaje_unidad_tarjeta' => $_POST['porcentaje_unidad_tarjeta'] ?? 100,
                'porcentaje_unidad_efectivo' => $_POST['porcentaje_unidad_efectivo'] ?? 80
            ];
            
            if ($this->productoPadreModel->crear($datos)) {
                $_SESSION['mensaje'] = 'Producto padre creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=productoPadre&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al crear el producto padre';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $this->render('productos_padre/crear', []);
    }
    
    public function editar($id) {
        $producto = $this->productoPadreModel->getById($id);
        
        if (!$producto) {
            header('Location: index.php?c=productoPadre&a=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'porcentaje_pack3_tarjeta' => $_POST['porcentaje_pack3_tarjeta'] ?? 100,
                'porcentaje_pack3_efectivo' => $_POST['porcentaje_pack3_efectivo'] ?? 80,
                'porcentaje_unidad_tarjeta' => $_POST['porcentaje_unidad_tarjeta'] ?? 100,
                'porcentaje_unidad_efectivo' => $_POST['porcentaje_unidad_efectivo'] ?? 80,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($this->productoPadreModel->actualizar($id, $datos)) {
                $_SESSION['mensaje'] = 'Producto padre actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=productoPadre&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el producto padre';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $this->render('productos_padre/editar', ['producto' => $producto]);
    }
    
    public function eliminar($id) {
        if ($this->productoPadreModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Producto padre eliminado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el producto padre';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=productoPadre&a=index');
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