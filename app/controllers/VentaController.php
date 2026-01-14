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

    public function exportarExcel() {
    // Obtener los mismos filtros que en index
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
    $estadisticas = $this->ventaModel->getEstadisticas(
        $filtros['fecha_desde'] ?? null,
        $filtros['fecha_hasta'] ?? null
    );
    
    // Generar nombre del archivo
    $fecha = date('Y-m-d_H-i-s');
    $filename = "ventas_export_{$fecha}.csv";
    
    // Headers para descarga
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para Excel UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados
    fputcsv($output, ['ID', 'Fecha', 'Método de Pago', 'Total', 'Descuento Aplicado']);
    
    // Datos
    foreach ($ventas as $venta) {
        fputcsv($output, [
            $venta['id'],
            date('d/m/Y H:i', strtotime($venta['fecha'])),
            ucfirst($venta['metodo_pago']),
            number_format($venta['total'], 2),
            number_format($venta['descuento_aplicado'], 2)
        ]);
    }
    
    // Línea en blanco
    fputcsv($output, []);
    
    // Totales
    fputcsv($output, ['ESTADÍSTICAS']);
    fputcsv($output, ['Total Ventas', number_format($estadisticas['total_vendido'] ?? 0, 2)]);
    fputcsv($output, ['Cantidad de Ventas', $estadisticas['total_ventas'] ?? 0]);
    fputcsv($output, ['Promedio por Venta', number_format($estadisticas['promedio_venta'] ?? 0, 2)]);
    fputcsv($output, ['Total Efectivo', number_format($estadisticas['total_efectivo'] ?? 0, 2)]);
    fputcsv($output, ['Total Tarjeta/Transferencia', number_format($estadisticas['total_tarjeta_combinado'] ?? 0, 2)]);
    
    fclose($output);
    exit;
}
}
?>