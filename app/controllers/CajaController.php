<?php
class CajaController {
    private $cajaModel;

    public function __construct() {
        $this->cajaModel = new MovimientoCaja();
    }

    public function index() {
        $this->render('caja/index', []);
    }

        public function altaMes() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $monto = floatval($_POST['monto']);
            $descripcion = 'Caja del mes ' . date('m/Y');
            if (!empty($_POST['descripcion_extra'])) {
                $descripcion .= ' — ' . trim($_POST['descripcion_extra']);
            }
            if ($monto > 0) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare(
                    "INSERT INTO ventas (total, metodo_pago, descuento_aplicado, nombre_cliente, estado, fecha, fecha_formalizacion) 
                    VALUES (?, 'caja', 0, ?, 'caja', ?, ?)"
                );
                $stmt->execute([$monto, $descripcion, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
                $_SESSION['mensaje'] = 'Caja del mes registrada correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            }
            header('Location: index.php?c=caja&a=index');
            exit;
        }
        $this->render('caja/alta_mes', []);
    }

    private function render($vista, $datos) {
        extract($datos);
        ob_start();
        require __DIR__ . '/../views/' . $vista . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }
}