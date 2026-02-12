<?php
// app/controllers/VentaController.php
class VentaController {
    private $ventaModel;
    private $variedadModel;
    private $productoPadreModel;
    private $promocionModel;
    private $cajaModel;
    
    public function __construct() {
        $this->ventaModel = new Venta();
        $this->variedadModel = new Variedad();
        $this->productoPadreModel = new ProductoPadre();
        $this->promocionModel = new Promocion();
        $this->cajaModel = new MovimientoCaja();
    }
    
    public function index() {
        // Obtener filtros con valores por defecto del mes actual
        $filtros = [];
        
        if (empty($_GET['fecha_desde']) && empty($_GET['fecha_hasta']) && empty($_GET['metodo_pago']) && empty($_GET['estado'])) {
            $filtros['fecha_desde'] = date('Y-m-01');
            $filtros['fecha_hasta'] = date('Y-m-t');
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
        
        // Obtener estadísticas
        $total_ventas = $this->ventaModel->getTotalVentas($filtros);
        $estadisticas = $this->ventaModel->getEstadisticas(
            $filtros['fecha_desde'] ?? null,
            $filtros['fecha_hasta'] ?? null
        );
        
        // CAMBIO: Obtener el total de caja (a precio de costo)
        $total_stock_disponible = $this->cajaModel->getTotalCaja();
        
        // Calcular ganancia del período
        $filtros_caja = [];
        if (!empty($filtros['fecha_desde'])) {
            $filtros_caja['fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $filtros_caja['fecha_hasta'] = $filtros['fecha_hasta'];
        }
        $ganancia_periodo = $this->cajaModel->getGananciaTotal(
            $filtros_caja['fecha_desde'] ?? null,
            $filtros_caja['fecha_hasta'] ?? null
        );
        
        // Calcular total de egresos en el mismo período
        $egresoModel = new Egreso();
        $total_egresos = $egresoModel->getTotalEgresos($filtros_caja);
        
        // Calcular balance (ganancia - egresos)
        $balance = $ganancia_periodo - $total_egresos;
        
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
            'ganancia_periodo' => $ganancia_periodo,
            'total_egresos' => $total_egresos,
            'balance' => $balance,
            'filtros' => $filtros,
            'paginacion' => $paginacion
        ]);
    }
    
public function nueva() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Decodificar JSON
        $productos_venta = json_decode($_POST['productos_json'], true);
        
        if (!$productos_venta || count($productos_venta) === 0) {
            $_SESSION['mensaje'] = 'Error: No hay productos en la venta';
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: index.php?c=venta&a=nueva');
            exit;
        }
        
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
            
            $precio_unitario = floatval($item['precio_unitario']);
            $subtotal = floatval($item['subtotal']);
            $total += $subtotal;
            
            $detalles[] = [
                'variedad_id' => $item['variedad_id'],
                'cantidad' => $item['cantidad'],
                'tipo_venta' => $item['tipo_venta'],
                'precio_unitario' => $precio_unitario,
                'descuento_unitario' => 0,
                'subtotal' => $subtotal
            ];
        }
        
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
            header('Location: index.php?c=venta&a=nueva');
            exit;
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
    
    // Calcular adelantos y saldo restante para preventas
    $adelantos_registrados = 0;
    $saldo_restante = $venta['total'];
    $historial_adelantos = [];
    
    if ($venta['estado'] == 'preventa') {
        $adelantosData = $this->ventaModel->getAdelantosRegistrados($id);
        $adelantos_registrados = $adelantosData['total'];
        $historial_adelantos = $adelantosData['historial'];
        $saldo_restante = $venta['total'] - $adelantos_registrados;
    }
    
    $this->render('ventas/detalle', [
        'venta' => $venta,
        'detalles' => $detalles,
        'adelantos_registrados' => $adelantos_registrados,
        'saldo_restante' => $saldo_restante,
        'historial_adelantos' => $historial_adelantos
    ]);
}
    
    public function formalizarPreventa($id) {
        $venta = $this->ventaModel->getById($id);
        $montoRestante = floatval($_GET['montoRestante'] ?? 0);
        if (!$venta || $venta['estado'] != 'preventa') {
            $_SESSION['mensaje'] = 'Esta venta no es una pre-venta válida';
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: index.php?c=venta&a=index');
            exit;
        }
        
        if ($this->ventaModel->formalizarPreventa($id, $montoRestante)) {
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
    public function registrarAdelanto($id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?c=venta&a=detalle&id=' . $id);
        exit;
    }
    
    $venta = $this->ventaModel->getById($id);
    
    if (!$venta || $venta['estado'] != 'preventa') {
        $_SESSION['mensaje'] = 'Esta venta no es una pre-venta válida';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=venta&a=index');
        exit;
    }
    
    $monto_adelanto = floatval($_POST['monto_adelanto'] ?? 0);
    
    // Calcular saldo restante
    $adelantosData = $this->ventaModel->getAdelantosRegistrados($id);
    $adelantos_previos = $adelantosData['total'];
    $saldo_restante = $venta['total'] - $adelantos_previos;
    
    if ($monto_adelanto <= 0 || $monto_adelanto > $saldo_restante) {
        $_SESSION['mensaje'] = 'Monto de adelanto inválido. Saldo restante: ' . FormatHelper::precio($saldo_restante);
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=venta&a=detalle&id=' . $id);
        exit;
    }
    
    if ($this->ventaModel->registrarAdelanto($id, $monto_adelanto)) {
        $_SESSION['mensaje'] = '✅ Adelanto de ' . FormatHelper::precio($monto_adelanto) . ' registrado exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al registrar el adelanto';
        $_SESSION['tipo_mensaje'] = 'error';
    }
    
    header('Location: index.php?c=venta&a=detalle&id=' . $id);
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