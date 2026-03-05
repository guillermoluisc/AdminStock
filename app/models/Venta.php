<?php
// app/models/Venta.php
class Venta {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    

// REEMPLAZA completamente el método getAll() en Venta.php:

public function getAll($filtros = [], $limit = null, $offset = null) {
    $sql = "SELECT * FROM ventas WHERE 1=1";
    $params = [];
    
    // Filtro por fecha
    if (!empty($filtros['fecha_desde'])) {
        $sql .= " AND DATE(fecha) >= ?";
        $params[] = $filtros['fecha_desde'];
    }
    
    if (!empty($filtros['fecha_hasta'])) {
        $sql .= " AND DATE(fecha) <= ?";
        $params[] = $filtros['fecha_hasta'];
    }
    
    // Filtro por método de pago
    if (!empty($filtros['metodo_pago'])) {
        $sql .= " AND metodo_pago = ?";
        $params[] = $filtros['metodo_pago'];
    }
    
    // Filtro por estado
    if (!empty($filtros['estado'])) {
        $sql .= " AND estado = ?";
        $params[] = $filtros['estado'];
    } else {
        // Por defecto no mostrar canceladas
        $sql .= " AND estado != 'cancelada'";
    }
    
    $sql .= " AND estado != 'caja'";
    $sql .= " ORDER BY fecha DESC";
    
    // Preparar el statement
    $stmt = $this->db->prepare($sql);
    
    // Bind de parámetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value);
    }
    
    // Agregar paginación si se especifica
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        
        // Re-bind de parámetros anteriores
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        
        // Bind de LIMIT y OFFSET como enteros
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// REEMPLAZA el método countAll() en Venta.php:

public function countAll($filtros = []) {
    $sql = "SELECT COUNT(*) as total FROM ventas WHERE 1=1";
    $params = [];
    
    // Filtro por fecha
    if (!empty($filtros['fecha_desde'])) {
        $sql .= " AND DATE(fecha) >= ?";
        $params[] = $filtros['fecha_desde'];
    }
    
    if (!empty($filtros['fecha_hasta'])) {
        $sql .= " AND DATE(fecha) <= ?";
        $params[] = $filtros['fecha_hasta'];
    }
    
    // Filtro por método de pago
    if (!empty($filtros['metodo_pago'])) {
        $sql .= " AND metodo_pago = ?";
        $params[] = $filtros['metodo_pago'];
    }
    
    // Filtro por estado
    if (!empty($filtros['estado'])) {
        $sql .= " AND estado = ?";
        $params[] = $filtros['estado'];
    } else {
        // Por defecto no mostrar canceladas
        $sql .= " AND estado != 'cancelada'";
    }

    $sql .= " AND estado != 'caja'";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return (int)($result['total'] ?? 0);
}
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ventas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getDetalles($venta_id) {
        $sql = "SELECT vd.*, v.nombre as variedad_nombre, p.nombre as producto_padre_nombre
                FROM venta_detalles vd
                JOIN variedades v ON vd.variedad_id = v.id
                JOIN productos_padre p ON v.producto_padre_id = p.id
                WHERE vd.venta_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll();
    }
    
    public function getVentasPorProducto($variedad_id, $fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT SUM(vd.cantidad) as total_vendido, COUNT(DISTINCT vd.venta_id) as cantidad_ventas
                FROM venta_detalles vd
                JOIN ventas v ON vd.venta_id = v.id
                WHERE vd.variedad_id = ? AND v.estado != 'cancelada'";
        
        $params = [$variedad_id];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(v.fecha) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(v.fecha) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
public function crear($total, $metodo_pago, $descuento_aplicado, $detalles, $nombre_cliente = null, $es_preventa = false) {
    try {
        $this->db->beginTransaction();
        
        // Determinar estado inicial
        $estado = $es_preventa ? 'preventa' : 'completada';
        $fecha_formalizacion = $es_preventa ? null : date('Y-m-d H:i:s');
        $fecha_creacion = date('Y-m-d H:i:s');
        
        // Crear venta
        $stmt = $this->db->prepare("INSERT INTO ventas (total, metodo_pago, descuento_aplicado, nombre_cliente, estado, fecha, fecha_formalizacion) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$total, $metodo_pago, $descuento_aplicado, $nombre_cliente, $estado, $fecha_creacion, $fecha_formalizacion]);
        $venta_id = $this->db->lastInsertId();
        
        // Variables para el movimiento de caja
        $total_costo = 0;
        
        // Insertar detalles y actualizar stock
        $stmtDetalle = $this->db->prepare(
            "INSERT INTO venta_detalles (venta_id, variedad_id, cantidad, precio_unitario, descuento_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $stmtStock = $this->db->prepare("UPDATE variedades SET stock = stock - ? WHERE id = ?");
        
        foreach ($detalles as $detalle) {
            // Insertar detalle
            $stmtDetalle->execute([
                $venta_id,
                $detalle['variedad_id'],
                $detalle['cantidad'],
                $detalle['precio_unitario'],
                $detalle['descuento_unitario'] ?? 0,
                $detalle['subtotal']
            ]);
            
            // Descontar stock
            $stmtStock->execute([
                $detalle['cantidad'],
                $detalle['variedad_id']
            ]);
            
            // Obtener el costo unitario de la variedad
            $stmtCosto = $this->db->prepare("SELECT precio_costo_unitario FROM variedades WHERE id = ?");
            $stmtCosto->execute([$detalle['variedad_id']]);
            $variedad = $stmtCosto->fetch();
            
            // Obtener el unidades por pack
            $stmtUnidades = $this->db->prepare("SELECT unidades_por_pack FROM variedades WHERE id = ?");
            $stmtUnidades->execute([$detalle['variedad_id']]);
            $variedadUnidad = $stmtUnidades->fetch();
            
            // pack de 3 o dos
            if($detalle['tipo_venta'] == 'Pack x3'){
                echo "Entro al tipo de venta pack";
                $precioUnitarioPorPack = $variedad['precio_costo_unitario'];
            }else{
                echo "Entro al tipo de venta unidad";
                $precioUnitarioPorPack = ($variedad['precio_costo_unitario'] / $variedadUnidad['unidades_por_pack'])* $detalle['cantidad'];
            }

            // Acumular el costo total
            $total_costo += $precioUnitarioPorPack;
        }
        
        // REGISTRAR MOVIMIENTO DE CAJA (solo si es venta completada)
        $descripcion = ($es_preventa ? 'Pre-venta #' : 'Venta #') . $venta_id;
                if ($nombre_cliente) {
                    $descripcion .= ' - ' . $nombre_cliente;
                }
                
                $stmtCaja = $this->db->prepare(
                    "INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion) VALUES (?, ?, ?, ?, ?)"
                );
                
                if ($estado == 'completada') {
                    // VENTA NORMAL: descuenta costo Y suma venta
                    $stmtCaja->execute([
                        'venta',
                        $venta_id,
                        -$total_costo,  // NEGATIVO para restar del stock
                        $total,         // Total suma a vendido
                        $descripcion
                    ]);
                } else {
                    // PRE-VENTA: descuenta SOLO el costo (refleja en Total en Stock)
                    // NO suma a Total Vendido (eso se hace con adelantos)
                    $stmtCaja->execute([
                        'preventa',
                        $venta_id,
                        -$total_costo,  // NEGATIVO para restar del stock
                        0,              // NO suma a vendido aún
                        $descripcion
                    ]);
                }
        
        $this->db->commit();
        return $venta_id;
    } catch (PDOException $e) {
        $this->db->rollBack();
        error_log("Error en Venta::crear() - " . $e->getMessage());
        return false;
    }
}
    
public function formalizarPreventa($id, $montoRestante) {
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        $venta = $this->getById($id);
        if (!$venta || $venta['estado'] != 'preventa') {
            throw new Exception('Venta no válida');
        }
        
        // Calcular cuánto falta pagar
        $adelantosData = $this->getAdelantosRegistrados($id);
        $adelantos_previos = $adelantosData['total'];
        $monto_restante = $montoRestante;
        
        // Solo registrar movimiento si hay saldo pendiente
        if ($monto_restante > 0) {
            $query = "INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion) 
                      VALUES ('venta', :referencia_id, :monto_costo, :monto_venta, :descripcion)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':referencia_id' => $id,
                ':monto_costo' => 0,  // CERO: el costo ya se descontó al crear la preventa
                ':monto_venta' => $monto_restante,  // Solo suma el saldo a Total Vendido
                ':descripcion' => 'Formalización de pre-venta #' . $id . ' (saldo restante)'
            ]);
        }
        
        // Actualizar estado a completada
        $queryDelete = "DELETE FROM movimientos_caja 
                        WHERE referencia_id = :referencia_id 
                        AND descripcion LIKE '%Adelanto%'";
        $stmtDelete = $db->prepare($queryDelete);
        $stmtDelete->execute([':referencia_id' => $id]);

        $query = "UPDATE ventas 
                SET estado = 'completada', 
                    fecha = CURRENT_TIMESTAMP,
                    fecha_formalizacion = CURRENT_TIMESTAMP
          WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $id]);
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error al formalizar pre-venta: " . $e->getMessage());
        return false;
    }
}
    
public function cancelarPreventa($id) {
    try {
        $this->db->beginTransaction();
        
        // Obtener detalles de la venta ANTES de cancelar
        $venta = $this->getById($id);
        $detalles = $this->getDetalles($id);
        
        // Devolver stock
        $stmtStock = $this->db->prepare("UPDATE variedades SET stock = stock + ? WHERE id = ?");
        foreach ($detalles as $detalle) {
            $stmtStock->execute([$detalle['cantidad'], $detalle['variedad_id']]);
        }
        
        // Marcar venta como cancelada
        $stmt = $this->db->prepare(
            "UPDATE ventas SET estado = 'cancelada' WHERE id = ? AND estado = 'preventa'"
        );
        $stmt->execute([$id]);
        
        // Si la pre-venta ya había sido formalizada antes (no debería pasar, pero por seguridad)
        // NO registramos movimiento porque las pre-ventas NO afectan caja hasta formalizarse
        
        $this->db->commit();
        return true;
    } catch (PDOException $e) {
        $this->db->rollBack();
        error_log("Error en cancelarPreventa: " . $e->getMessage());
        return false;
    }
}
    
    public function getTotalVentas($filtros = []) {
        $sql = "SELECT SUM(total) as total FROM ventas WHERE estado != 'cancelada'";
        $params = [];
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($filtros['metodo_pago'])) {
            $sql .= " AND metodo_pago = ?";
            $params[] = $filtros['metodo_pago'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
    
public function getEstadisticas($fecha_desde = null, $fecha_hasta = null) {
    // 1. Estadísticas de ventas completadas
    $sql = "SELECT 
            COUNT(*) as total_ventas,
            SUM(total) as total_vendido,
            AVG(total) as promedio_venta,
            SUM(CASE WHEN metodo_pago = 'efectivo' THEN total ELSE 0 END) as total_efectivo,
            SUM(CASE WHEN metodo_pago = 'tarjeta' THEN total ELSE 0 END) as total_tarjeta,
            SUM(CASE WHEN metodo_pago = 'transferencia' THEN total ELSE 0 END) as total_transferencia
            FROM ventas WHERE estado = 'completada' AND estado != 'caja'";
    
    $params = [];
    
    if ($fecha_desde) {
        $sql .= " AND DATE(fecha) >= ?";
        $params[] = $fecha_desde;
    }
    
    if ($fecha_hasta) {
        $sql .= " AND DATE(fecha) <= ?";
        $params[] = $fecha_hasta;
    }
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    
    // 2. SUMAR ADELANTOS DE PRE-VENTAS a Total Vendido
    $sql_adelantos = "SELECT COALESCE(SUM(monto_venta), 0) as total_adelantos
                      FROM movimientos_caja 
                      WHERE tipo = 'venta' 
                        AND descripcion LIKE '%Adelanto de pre-venta%'";
    
    $params_adelantos = [];
    
    if ($fecha_desde) {
        $sql_adelantos .= " AND DATE(fecha) >= ?";
        $params_adelantos[] = $fecha_desde;
    }
    
    if ($fecha_hasta) {
        $sql_adelantos .= " AND DATE(fecha) <= ?";
        $params_adelantos[] = $fecha_hasta;
    }
    
    $stmt_adelantos = $this->db->prepare($sql_adelantos);
    $stmt_adelantos->execute($params_adelantos);
    $total_adelantos = $stmt_adelantos->fetchColumn();
    
    // 3. Combinar resultados
    if ($result) {
        // Sumar adelantos al total vendido
        $result['total_vendido'] = ($result['total_vendido'] ?? 0) + $total_adelantos;
        
        // Combinar tarjeta y transferencia
        $result['total_tarjeta_combinado'] = ($result['total_tarjeta'] ?? 0) + ($result['total_transferencia'] ?? 0);
    }
    
    return $result;
}
    /**
 * Obtiene el total de adelantos registrados para una pre-venta
 */
public function getAdelantosRegistrados($venta_id) {
    try {
        $db = Database::getInstance()->getConnection();

        
        // Buscar movimientos de caja que sean adelantos de esta venta
        $query = "SELECT 
                    SUM(monto_venta) as total_adelantos,
                    monto_venta,
                    fecha
                  FROM movimientos_caja 
                  WHERE tipo = 'venta' 
                    AND referencia_id = :venta_id
                    AND descripcion LIKE '%Adelanto de pre-venta%'
                  GROUP BY monto_venta, fecha
                  ORDER BY fecha ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':venta_id' => $venta_id]);
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular total
        $query_total = "SELECT COALESCE(SUM(monto_venta), 0) as total
                        FROM movimientos_caja 
                        WHERE tipo = 'venta' 
                          AND referencia_id = :venta_id
                          AND descripcion LIKE '%Adelanto de pre-venta%'";
        
        $stmt_total = $db->prepare($query_total);
        $stmt_total->execute([':venta_id' => $venta_id]);
        $total = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
        
        return [
            'total' => floatval($total),
            'historial' => $historial
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener adelantos: " . $e->getMessage());
        return [
            'total' => 0,
            'historial' => []
        ];
    }
}

/**
 * Registra un adelanto para una pre-venta
 */
public function registrarAdelanto($venta_id, $monto_adelanto) {
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        // Obtener venta
        $venta = $this->getById($venta_id);
        if (!$venta || $venta['estado'] != 'preventa') {
            throw new Exception('Venta no válida');
        }
        
        // Registrar en movimientos de caja
        // IMPORTANTE: tipo = 'venta' para que getAdelantosRegistrados lo encuentre
        // monto_costo = 0 porque el costo YA se descontó al crear la preventa
        $query = "INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion) 
                  VALUES ('venta', :referencia_id, :monto_costo, :monto_venta, :descripcion)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':referencia_id' => $venta_id,
            ':monto_costo' => 0,  // CERO: el costo ya se descontó completo al crear la preventa
            ':monto_venta' => $monto_adelanto,  // Este monto suma a Total Vendido
            ':descripcion' => 'Adelanto de pre-venta #' . $venta_id
        ]);
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error al registrar adelanto: " . $e->getMessage());
        return false;
    }
}
}
?>