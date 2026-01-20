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
    
// REEMPLAZA el método index() en tu VentaController.php con este:

public function index() {
    // Obtener filtros con valores por defecto del mes actual
    $filtros = [];
    
    // Si no hay filtros en GET, usar mes actual por defecto
    if (empty($_GET['fecha_desde']) && empty($_GET['fecha_hasta']) && empty($_GET['metodo_pago']) && empty($_GET['estado'])) {
        $filtros['fecha_desde'] = date('Y-m-01'); // Primer día del mes
        $filtros['fecha_hasta'] = date('Y-m-t');   // Último día del mes
    } else {
        if (!empty($_GET['fecha_desde'])) {
            $filtros['fecha_desde'] = $_GET['fecha_desde'];
        }
        if (!empty($_GET['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
        }
        if (!empty($_GET['metodo_pago'])) {
            $filtros['metodo_pago'] = $_GET['metodo_pago'];
        }
        if (!empty($_GET['estado'])) {
            $filtros['estado'] = $_GET['estado'];
        }
    }
    
    // Configuración de paginación
    $pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $por_pagina = isset($_GET['por_pagina']) ? max(10, intval($_GET['por_pagina'])) : 10;
    $offset = ($pagina_actual - 1) * $por_pagina;
    
    // Contar total de registros
    $total_registros = $this->ventaModel->countAll($filtros);
    $total_paginas = ceil($total_registros / $por_pagina);
    
    // Obtener ventas con paginación
    $ventas = $this->ventaModel->getAll($filtros, $por_pagina, $offset);
    
    // Obtener estadísticas (sin paginación)
    $total_ventas = $this->ventaModel->getTotalVentas($filtros);
    $estadisticas = $this->ventaModel->getEstadisticas(
        $filtros['fecha_desde'] ?? null,
        $filtros['fecha_hasta'] ?? null
    );
    
    // Calcular total stock disponible
    $total_stock_disponible = $this->variedadModel->getTotalStockDisponible();
    
    // Calcular total de egresos en el mismo período
    $egresoModel = new Egreso();
    $filtros_egresos = [];
    if (!empty($filtros['fecha_desde'])) {
        $filtros_egresos['fecha_desde'] = $filtros['fecha_desde'];
    }
    if (!empty($filtros['fecha_hasta'])) {
        $filtros_egresos['fecha_hasta'] = $filtros['fecha_hasta'];
    }
    $total_egresos = $egresoModel->getTotalEgresos($filtros_egresos);
    
    // Calcular balance (ingresos - egresos)
    $balance = ($estadisticas['total_vendido'] ?? 0) - $total_egresos;
    
    // Preparar datos de paginación
    $paginacion = [
        'pagina_actual' => $pagina_actual,
        'por_pagina' => $por_pagina,
        'total' => $total_registros,
        'total_paginas' => $total_paginas
    ];
    
    $this->render('ventas/index', [
        'ventas' => $ventas,
        'total_ventas' => $total_ventas,
        'estadisticas' => $estadisticas,
        'total_stock_disponible' => $total_stock_disponible,
        'total_egresos' => $total_egresos,
        'balance' => $balance,
        'filtros' => $filtros,
        'paginacion' => $paginacion
    ]);
}
    
public function nueva() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productos_venta = json_decode($_POST['productos_json'], true);
        $nombre_cliente = !empty($_POST['nombre_cliente']) ? trim($_POST['nombre_cliente']) : null;
        $es_preventa = isset($_POST['es_preventa']) && $_POST['es_preventa'] == '1';
        
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
            
            // El precio ya viene calculado desde el frontend según los 4 precios guardados
            $precio_unitario = floatval($item['precio_unitario']);
            $subtotal = $precio_unitario;
            $total += $subtotal;
            
            $detalles[] = [
                'variedad_id' => $item['variedad_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $precio_unitario,
                'descuento_unitario' => 0,
                'subtotal' => $subtotal
            ];
        }
        
        // El método de pago se toma del primer producto (todos deberían tener el mismo)
        $metodo_pago = $productos_venta[0]['metodo_pago'] ?? 'efectivo';
        
        $venta_id = $this->ventaModel->crear($total, $metodo_pago, $descuento_total, $detalles, $nombre_cliente, $es_preventa);
        
        if ($venta_id) {
            if ($es_preventa) {
                $_SESSION['mensaje'] = '⏳ Pre-venta guardada exitosamente. Total: ' . FormatHelper::precio($total);
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = '✅ Venta registrada exitosamente. Total: ' . FormatHelper::precio($total);
                $_SESSION['tipo_mensaje'] = 'success';
            }
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

public function formalizarPreventa($id) {
    $venta = $this->ventaModel->getById($id);
    
    if (!$venta || $venta['estado'] != 'preventa') {
        $_SESSION['mensaje'] = 'Esta venta no es una pre-venta válida';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=venta&a=index');
        exit;
    }
    
    if ($this->ventaModel->formalizarPreventa($id)) {
        $_SESSION['mensaje'] = '✅ Pre-venta formalizada exitosamente. Total: ' . FormatHelper::precio($venta['total']);
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al formalizar la pre-venta';
        $_SESSION['tipo_mensaje'] = 'error';
    }
    
    header('Location: index.php?c=venta&a=detalle&id=' . $id);
    exit;
}

public function cancelarPreventa($id) {
    $venta = $this->ventaModel->getById($id);
    
    if (!$venta || $venta['estado'] != 'preventa') {
        $_SESSION['mensaje'] = 'Esta venta no es una pre-venta válida';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=venta&a=index');
        exit;
    }
    
    if (!empty($_POST['confirmar_cancelacion'])) {
        if ($this->ventaModel->cancelarPreventa($id)) {
            $_SESSION['mensaje'] = '🔄 Pre-venta cancelada. El stock fue devuelto.';
            $_SESSION['tipo_mensaje'] = 'success';
            header('Location: index.php?c=venta&a=index');
            exit;
        } else {
            $_SESSION['mensaje'] = 'Error al cancelar la pre-venta';
            $_SESSION['tipo_mensaje'] = 'error';
        }
    }
    
    header('Location: index.php?c=venta&a=detalle&id=' . $id);
    exit;
}
}
?>