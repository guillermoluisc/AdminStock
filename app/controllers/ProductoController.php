<?php
// app/controllers/ProductoController.php
class ProductoController {
    private $productoModel;
    private $tamanoModel;
    private $saborModel;
    private $tipoSaladoModel;
    private $variedadSaladoModel;
    
    public function __construct() {
        $this->productoModel = new Producto();
        $this->tamanoModel = new Tamano();
        $this->saborModel = new Sabor();
        $this->tipoSaladoModel = new TipoSalado();
        $this->variedadSaladoModel = new VariedadSalado();
    }
    
    public function index() {
        $productos = $this->productoModel->getAll(true);
        
        $this->render('productos/index', [
            'productos' => $productos
        ]);
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'tipo' => $_POST['tipo'],
                'tamano_id' => $_POST['tamano_id'] ?? null,
                'sabor_id' => $_POST['sabor_id'] ?? null,
                'variedad_salado_id' => $_POST['variedad_salado_id'] ?? null,
                'stock' => $_POST['stock'] ?? 0,
                'precio_compra' => $_POST['precio_compra'] ?? 0,
                'precio_venta' => $_POST['precio_venta'],
                'precio_promo' => $_POST['precio_promo'] ?? null,
                'cantidad_promo' => $_POST['cantidad_promo'] ?? null
            ];
            
            if ($this->productoModel->crear($datos)) {
                $_SESSION['mensaje'] = 'Producto creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=producto&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al crear el producto';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $this->render('productos/crear', [
            'tamanos' => $this->tamanoModel->getAll(),
            'sabores' => $this->saborModel->getAll(),
            'tipos_salados' => $this->tipoSaladoModel->getAll(),
            'variedades_salados' => $this->variedadSaladoModel->getAll()
        ]);
    }
    
    public function editar($id) {
        $producto = $this->productoModel->getById($id);
        
        if (!$producto) {
            header('Location: index.php?c=producto&a=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'stock' => $_POST['stock'],
                'precio_compra' => $_POST['precio_compra'],
                'precio_venta' => $_POST['precio_venta'],
                'precio_promo' => $_POST['precio_promo'] ?? null,
                'cantidad_promo' => $_POST['cantidad_promo'] ?? null,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($this->productoModel->actualizar($id, $datos)) {
                $_SESSION['mensaje'] = 'Producto actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=producto&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el producto';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $this->render('productos/editar', ['producto' => $producto]);
    }
    
    public function eliminar($id) {
        if ($this->productoModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Producto eliminado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el producto';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=producto&a=index');
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