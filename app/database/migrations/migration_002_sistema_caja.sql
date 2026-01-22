-- =====================================================
-- MIGRACION: Sistema de Caja y Movimientos
-- =====================================================

-- Tabla de movimientos de caja
CREATE TABLE IF NOT EXISTS movimientos_caja (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo VARCHAR(20) NOT NULL, -- 'compra', 'venta', 'egreso', 'ajuste'
    referencia_id INTEGER, -- ID de la venta, compra o egreso relacionado
    monto_costo DECIMAL(10,2) NOT NULL, -- Monto a precio de costo
    monto_venta DECIMAL(10,2) DEFAULT 0, -- Monto de venta (si aplica)
    descripcion TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Índices para mejor rendimiento
CREATE INDEX IF NOT EXISTS idx_movimientos_tipo ON movimientos_caja(tipo);
CREATE INDEX IF NOT EXISTS idx_movimientos_fecha ON movimientos_caja(fecha);

-- =====================================================
-- VISTAS para facilitar consultas
-- =====================================================

-- Vista: Total en caja (a precio de costo)
CREATE VIEW IF NOT EXISTS vista_total_caja AS
SELECT 
    SUM(CASE 
        WHEN tipo = 'compra' OR tipo = 'ajuste' THEN monto_costo
        WHEN tipo = 'venta' OR tipo = 'egreso' THEN -monto_costo
        ELSE 0
    END) as total_caja
FROM movimientos_caja;

-- Vista: Desglose de movimientos
CREATE VIEW IF NOT EXISTS vista_movimientos_detalle AS
SELECT 
    m.id,
    m.tipo,
    m.fecha,
    m.descripcion,
    m.monto_costo,
    m.monto_venta,
    CASE 
        WHEN m.tipo = 'venta' AND m.monto_venta > 0 
        THEN m.monto_venta - m.monto_costo 
        ELSE 0 
    END as ganancia,
    v.nombre_cliente,
    v.metodo_pago
FROM movimientos_caja m
LEFT JOIN ventas v ON m.tipo = 'venta' AND m.referencia_id = v.id
ORDER BY m.fecha DESC;

-- =====================================================
-- TRIGGER: Registrar compra de variedad en caja
-- =====================================================
CREATE TRIGGER IF NOT EXISTS trigger_compra_variedad
AFTER INSERT ON variedades
BEGIN
    INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion)
    VALUES (
        'compra',
        NEW.id,
        NEW.precio_compra_total,
        'Compra de variedad: ' || NEW.nombre || ' (Stock: ' || NEW.stock || ')'
    );
END;

-- =====================================================
-- TRIGGER: Registrar venta en caja
-- =====================================================
CREATE TRIGGER IF NOT EXISTS trigger_venta_caja
AFTER INSERT ON ventas
WHEN NEW.estado = 'completada'
BEGIN
    -- Calcular costo total de los productos vendidos
    INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion)
    SELECT 
        'venta',
        NEW.id,
        SUM(vd.cantidad * v.precio_costo_unitario),
        NEW.total,
        'Venta #' || NEW.id || CASE 
            WHEN NEW.nombre_cliente IS NOT NULL 
            THEN ' - ' || NEW.nombre_cliente 
            ELSE '' 
        END
    FROM venta_detalles vd
    JOIN variedades v ON vd.variedad_id = v.id
    WHERE vd.venta_id = NEW.id;
END;

-- =====================================================
-- TRIGGER: Registrar formalización de pre-venta
-- =====================================================
CREATE TRIGGER IF NOT EXISTS trigger_formalizar_preventa
AFTER UPDATE ON ventas
WHEN OLD.estado = 'preventa' AND NEW.estado = 'completada'
BEGIN
    INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion)
    SELECT 
        'venta',
        NEW.id,
        SUM(vd.cantidad * v.precio_costo_unitario),
        NEW.total,
        'Venta #' || NEW.id || ' (Pre-venta formalizada)' || CASE 
            WHEN NEW.nombre_cliente IS NOT NULL 
            THEN ' - ' || NEW.nombre_cliente 
            ELSE '' 
        END
    FROM venta_detalles vd
    JOIN variedades v ON vd.variedad_id = v.id
    WHERE vd.venta_id = NEW.id;
END;

-- =====================================================
-- TRIGGER: Revertir venta cancelada
-- =====================================================
CREATE TRIGGER IF NOT EXISTS trigger_cancelar_venta
AFTER UPDATE ON ventas
WHEN NEW.estado = 'cancelada' AND OLD.estado != 'cancelada'
BEGIN
    -- Si era una venta completada, revertir el movimiento
    INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion)
    SELECT 
        'ajuste',
        NEW.id,
        SUM(vd.cantidad * v.precio_costo_unitario), -- Positivo para devolver a caja
        0,
        'Cancelación de Venta #' || NEW.id
    FROM venta_detalles vd
    JOIN variedades v ON vd.variedad_id = v.id
    WHERE vd.venta_id = NEW.id
    AND OLD.estado = 'completada';
END;

-- =====================================================
-- TRIGGER: Registrar egresos en caja
-- =====================================================
CREATE TRIGGER IF NOT EXISTS trigger_egreso_caja
AFTER INSERT ON egresos
BEGIN
    INSERT INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion)
    VALUES (
        'egreso',
        NEW.id,
        NEW.monto,
        'Egreso: ' || NEW.descripcion
    );
END;

-- =====================================================
-- Procedimiento: Inicializar caja con stock existente
-- =====================================================
-- Insertar movimientos iniciales para variedades existentes
INSERT OR IGNORE INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion, fecha)
SELECT 
    'compra',
    id,
    precio_compra_total,
    'Inventario inicial: ' || nombre,
    fecha_creacion
FROM variedades
WHERE id NOT IN (SELECT referencia_id FROM movimientos_caja WHERE tipo = 'compra');

-- Insertar movimientos de ventas completadas existentes
INSERT OR IGNORE INTO movimientos_caja (tipo, referencia_id, monto_costo, monto_venta, descripcion, fecha)
SELECT 
    'venta',
    v.id,
    (SELECT SUM(vd.cantidad * var.precio_costo_unitario)
     FROM venta_detalles vd
     JOIN variedades var ON vd.variedad_id = var.id
     WHERE vd.venta_id = v.id),
    v.total,
    'Venta #' || v.id || CASE 
        WHEN v.nombre_cliente IS NOT NULL 
        THEN ' - ' || v.nombre_cliente 
        ELSE '' 
    END,
    v.fecha
FROM ventas v
WHERE v.estado = 'completada'
AND v.id NOT IN (SELECT referencia_id FROM movimientos_caja WHERE tipo = 'venta');

-- Insertar egresos existentes
INSERT OR IGNORE INTO movimientos_caja (tipo, referencia_id, monto_costo, descripcion, fecha)
SELECT 
    'egreso',
    id,
    monto,
    'Egreso: ' || descripcion,
    fecha
FROM egresos
WHERE id NOT IN (SELECT referencia_id FROM movimientos_caja WHERE tipo = 'egreso');