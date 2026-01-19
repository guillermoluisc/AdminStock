<?php
// app/controllers/VariedadController.php
class VariedadController {
    private $variedadModel;
    private $productoPadreModel;
    private $promocionModel;
    
    public function __construct() {
        $this->variedadModel = new Variedad();
        $this->productoPadreModel = new ProductoPadre();
        $this->promocionModel = new Promocion();
    }
    
    public function index() {
        // Obtener filtros
        $filtros = [];
        if (isset($_GET['stock_estado'])) {
            $filtros['stock_estado'] = $_GET['stock_estado'];
        }
        if (isset($_GET['producto_padre_id']) && $_GET['producto_padre_id'] !== '') {
            $filtros['producto_padre_id'] = $_GET['producto_padre_id'];
        }
        
        $variedades = $this->variedadModel->getAllConFiltros($filtros);
        $productos_padre = $this->productoPadreModel->getAll();
        
        $this->render('variedades/index', [
            'variedades' => $variedades,
            'productos_padre' => $productos_padre,
            'filtros' => $filtros
        ]);
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $precio_compra_total = $_POST['precio_compra_total'];
            $cantidad_comprada = $_POST['cantidad_comprada'];
            $precio_costo_unitario = $this->variedadModel->calcularPrecioCostoUnitario($precio_compra_total, $cantidad_comprada);
            
            $datos = [
                'producto_padre_id' => $_POST['producto_padre_id'],
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio_compra_total' => $precio_compra_total,
                'cantidad_comprada' => $cantidad_comprada,
                'precio_costo_unitario' => $precio_costo_unitario,
                'precio_pack3_tarjeta' => $_POST['precio_pack3_tarjeta'] ?? 0,
                'precio_pack3_efectivo' => $_POST['precio_pack3_efectivo'] ?? 0,
                'precio_unidad_tarjeta' => $_POST['precio_unidad_tarjeta'] ?? 0,
                'precio_unidad_efectivo' => $_POST['precio_unidad_efectivo'] ?? 0,
                'precio_venta_unitario' => $_POST['precio_venta_unitario'] ?? 0,
                'stock' => $_POST['stock'] ?? $cantidad_comprada,
                'stock_minimo' => $_POST['stock_minimo'] ?? 10,
                'unidades_por_pack' => $_POST['unidades_por_pack'] ?? 3
            ];
            
            $variedad_id = $this->variedadModel->crear($datos);
            
            if ($variedad_id) {
                // Crear promoción si se configuró
                if (!empty($_POST['promo_cantidad']) && !empty($_POST['promo_precio'])) {
                    $promo_datos = [
                        'variedad_id' => $variedad_id,
                        'cantidad' => $_POST['promo_cantidad'],
                        'precio_promocional' => $_POST['promo_precio']
                    ];
                    $this->promocionModel->crear($promo_datos);
                }
                
                $_SESSION['mensaje'] = 'Variedad creada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=variedad&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al crear la variedad';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $productos_padre = $this->productoPadreModel->getAll();
        
        $this->render('variedades/crear', [
            'productos_padre' => $productos_padre
        ]);
    }
    
    public function editar($id) {
        $variedad = $this->variedadModel->getById($id);
        
        if (!$variedad) {
            header('Location: index.php?c=variedad&a=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $precio_compra_total = $_POST['precio_compra_total'];
            $cantidad_comprada = $_POST['cantidad_comprada'];
            $precio_costo_unitario = $this->variedadModel->calcularPrecioCostoUnitario($precio_compra_total, $cantidad_comprada);
            
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio_compra_total' => $precio_compra_total,
                'cantidad_comprada' => $cantidad_comprada,
                'precio_costo_unitario' => $precio_costo_unitario,
                'precio_pack3_tarjeta' => $_POST['precio_pack3_tarjeta'] ?? 0,
                'precio_pack3_efectivo' => $_POST['precio_pack3_efectivo'] ?? 0,
                'precio_unidad_tarjeta' => $_POST['precio_unidad_tarjeta'] ?? 0,
                'precio_unidad_efectivo' => $_POST['precio_unidad_efectivo'] ?? 0,
                'precio_venta_unitario' => $_POST['precio_venta_unitario'] ?? 0,
                'stock' => $_POST['stock'],
                'stock_minimo' => $_POST['stock_minimo'] ?? 10,
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'unidades_por_pack' => $_POST['unidades_por_pack'] ?? 3
            ];
            
            if ($this->variedadModel->actualizar($id, $datos)) {
                $_SESSION['mensaje'] = 'Variedad actualizada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=variedad&a=index');
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar la variedad';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $promociones = $this->promocionModel->getByVariedad($id);
        
        $this->render('variedades/editar', [
            'variedad' => $variedad,
            'promociones' => $promociones
        ]);
    }
    
    public function eliminar($id) {
        if ($this->variedadModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Variedad eliminada exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar la variedad';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=variedad&a=index');
        exit;
    }
    
    public function calcularPrecioCosto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $precio_total = floatval($_POST['precio_total']);
            $cantidad = intval($_POST['cantidad']);
            
            $precio_unitario = $this->variedadModel->calcularPrecioCostoUnitario($precio_total, $cantidad);
            
            echo json_encode(['precio_unitario' => $precio_unitario]);
            exit;
        }
    }
    
    public function calcularPreciosVenta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $costo_unitario = floatval($_POST['costo_unitario']);
            $producto_padre_id = intval($_POST['producto_padre_id']);
            
            $precios = $this->variedadModel->calcularPreciosVenta($costo_unitario, $producto_padre_id);
            
            echo json_encode($precios);
            exit;
        }
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