<?php
// app/controllers/PedidoController.php
class PedidoController {
    private $pedidoModel;
    private $variedadModel;
    
    public function __construct() {
        $this->pedidoModel = new Pedido();
        $this->variedadModel = new Variedad();
    }
    
    public function index() {
        // Obtener filtros con valores por defecto del mes actual
        $filtros = [];
        
if (empty($_GET['fecha_desde']) && empty($_GET['fecha_hasta']) && empty($_GET['estado']) && empty($_GET['buscar'])) {
            $filtros['fecha_desde'] = date('Y-m-01');
            $filtros['fecha_hasta'] = date('Y-m-t');
        } else {
            if (!empty($_GET['fecha_desde'])) {
                $filtros['fecha_desde'] = $_GET['fecha_desde'];
            }
            if (!empty($_GET['fecha_hasta'])) {
                $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
            }
            if (!empty($_GET['estado'])) {
                $filtros['estado'] = $_GET['estado'];
            }
            if (!empty($_GET['buscar'])) {
                $filtros['buscar'] = $_GET['buscar'];
            }
        }
        
        $pedidos = $this->pedidoModel->getAll($filtros);
        
        $this->render('pedidos/index', [
            'pedidos' => $pedidos,
            'filtros' => $filtros
        ]);
    }
    
    public function nuevo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productos_pedido = json_decode($_POST['productos_json'], true);
            $observaciones = $_POST['observaciones'] ?? '';
            
            $detalles = [];
            foreach ($productos_pedido as $item) {
                $variedad = $this->variedadModel->getById($item['variedad_id']);
                
                $detalles[] = [
                    'variedad_id' => $item['variedad_id'],
                    'cantidad_solicitada' => $item['cantidad'],
                    'precio_estimado' => $variedad['precio_costo_unitario'] * $item['cantidad'],
                    'observaciones' => $item['observaciones'] ?? ''
                ];
            }
            
            $pedido_id = $this->pedidoModel->crear($observaciones, $detalles);
            
            if ($pedido_id) {
                $_SESSION['mensaje'] = 'Pedido creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?c=pedido&a=detalle&id=' . $pedido_id);
                exit;
            } else {
                $_SESSION['mensaje'] = 'Error al crear el pedido';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        $variedades = $this->variedadModel->getAll(true);
        $variedades_stock_bajo = $this->variedadModel->getStockBajo();
        
        $this->render('pedidos/nuevo', [
            'variedades' => $variedades,
            'variedades_stock_bajo' => $variedades_stock_bajo
        ]);
    }
    
    public function detalle($id) {
        $pedido = $this->pedidoModel->getById($id);
        
        if (!$pedido) {
            header('Location: index.php?c=pedido&a=index');
            exit;
        }
        
        $detalles = $this->pedidoModel->getDetalles($id);
        
        $this->render('pedidos/detalle', [
            'pedido' => $pedido,
            'detalles' => $detalles
        ]);
    }
    
    public function marcarRealizado($id) {
        if ($this->pedidoModel->marcarComoRealizado($id)) {
            $_SESSION['mensaje'] = 'Pedido marcado como realizado';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar el pedido';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=pedido&a=detalle&id=' . $id);
        exit;
    }
    
    public function marcarFaltante($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $observaciones = $_POST['observaciones'] ?? '';
            
            if ($this->pedidoModel->marcarComoFaltante($id, $observaciones)) {
                $_SESSION['mensaje'] = 'Pedido marcado con faltantes';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el pedido';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        header('Location: index.php?c=pedido&a=detalle&id=' . $id);
        exit;
    }
    
    public function actualizarDetalle($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $detalle_id = $_POST['detalle_id'];
            $cantidad_recibida = $_POST['cantidad_recibida'];
            $observaciones = $_POST['observaciones'] ?? '';
            
            if ($this->pedidoModel->actualizarDetalle($detalle_id, $cantidad_recibida, $observaciones)) {
                $_SESSION['mensaje'] = 'Detalle actualizado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el detalle';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        }
        
        header('Location: index.php?c=pedido&a=detalle&id=' . $id);
        exit;
    }
    
    public function eliminar($id) {
        if ($this->pedidoModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Pedido eliminado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el pedido';
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?c=pedido&a=index');
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