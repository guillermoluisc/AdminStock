<?php
// app/controllers/VentaController.php
class VentaController {
    private $ventaModel;
    private $variedadModel;
    private $productoPadreModel;
    private $promocionModel;
    
    public function __construct() {
        $this->ventaModel = new Venta();
        $this->variedadModel = new Variedad();
        $this->productoPadreModel = new ProductoPadre();
        $this->promocionModel = new Promocion();
    }
    
    public function index() {
        // Obtener filtros
        $filtros = [];
        if (!empty($_GET['fecha_desde'])) {
            $filtros['fecha_desde'] = $_GET['fecha_desde'];
        }
        if (!empty($_GET['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
        }
        if (!empty($_GET['metodo_pago'])) {
            $filtros['metodo_pago'] = $_GET['metodo_pago'];
        }
        
        $ventas = $this->ventaModel->getAll($filtros);
        $total_ventas = $this->ventaModel->getTotalVentas($filtros);
        $estadisticas = $this->ventaModel->getEstadisticas(
            $filtros['fecha_desde'] ?? null,
            $filtros['fecha_hasta'] ?? null
        );
        
        $this->render('ventas/index', [
            'ventas' => $ventas,
            'total_ventas' => $total_ventas,
            'estadisticas' => $estadisticas,
            'filtros' => $filtros
        ]);
    }
    
    public function nueva() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productos_venta = json_decode($_POST['productos_json'], true);
            $metodo_pago = $_POST['metodo_pago'];
            $total = 0;
            $descuento_total = 0;
            
            $detalles = [];
            foreach ($productos_venta as $item) {
                $variedad = $this->variedadModel->getById($item['variedad_id']);
                
                // Verificar stock
                if ($variedad['stock'] < $item['cantidad']) {
                    $_SESSION['mensaje'] = 'Stock insuficiente para: ' . $variedad['nombre'];
                    $_SESSION['tipo_mensaje'] = 'error';
                    header('Location: index.php?c=venta&a=nueva');
                    exit;
                }
                
                // Calcular precio según cantidad y método de pago
                $costo = $variedad['precio_costo_unitario'];
                $producto_padre = $this->productoPadreModel->getById($variedad['producto_padre_id']);
                
                $precio_calculado = 0;
                
                if ($item['cantidad'] == 3) {
                    // Pack x3
                    $porcentaje = $metodo_pago == 'efectivo' 
                        ? $producto_padre['porcentaje_pack3_efectivo'] 
                        : $producto_padre['porcentaje_pack3_tarjeta'];
                    
                    $precio_pack = $costo * 3 + (($costo * 3) * $porcentaje / 100);
                    $precio_calculado = $precio_pack / 3; // Precio unitario del pack
                } else {
                    // Por unidad
                    $porcentaje = $metodo_pago == 'efectivo' 
                        ? $producto_padre['porcentaje_unidad_efectivo'] 
                        : $producto_padre['porcentaje_unidad_tarjeta'];
                    
                    $precio_calculado = $costo + ($costo * $porcentaje / 100);
                }
                
                $subtotal = $precio_calculado * $item['cantidad'];
                $total += $subtotal;
                
                $detalles[] = [
                    'variedad_id' => $item['variedad_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precio_calculado,
                    'descuento_unitario' => 0,
                    'subtotal' => $subtotal
                ];
            }
            
            $venta_id = $this->ventaModel->crear($total, $metodo_pago, $descuento_total, $detalles);
            
            if ($venta_id) {
                $_SESSION['mensaje'] = 'Venta registrada exitosamente. Total: $' . number_format($total, 2);
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=venta&a=detalle&id=' . $venta_id);
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al registrar la venta';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $variedades = $this->variedadModel->getAll(true);
        
        $this->render('ventas/nueva', [
            'variedades' => $variedades
        ]);
    }
    
    public function detalle($id) {
        $venta = $this->ventaModel->getById($id);
        
        if (!$venta) {
            header('Location: index.php?c=venta&a=index');
            exit;
        }
        
        $detalles = $this->ventaModel->getDetalles($id);
        
        $this->render('ventas/detalle', [
            'venta' => $venta,
            'detalles' => $detalles
        ]);
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