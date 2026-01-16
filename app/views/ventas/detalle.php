<!-- app/views/ventas/detalle.php -->
<div style="margin-bottom: 20px;">
    <a href="index.php?c=venta&a=index" class="btn btn-secondary">← Volver al Historial</a>
</div>

<div class="card" style="margin-bottom: 20px;">
    <h2>Detalle de Venta #<?= $venta['id'] ?></h2>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
        <div>
            <strong>Fecha:</strong><br>
            <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?>
        </div>
        <div>
            <strong>Cliente:</strong><br>
            <?php if ($venta['nombre_cliente']): ?>
                <span style="font-size: 16px;">👤 <?= htmlspecialchars($venta['nombre_cliente']) ?></span>
            <?php else: ?>
                <span style="color: #95a5a6;">Anónimo</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Estado:</strong><br>
            <?php if ($venta['estado'] == 'completada'): ?>
                <span class="badge" style="background: #27ae60; font-size: 14px;">✅ Completada</span>
            <?php elseif ($venta['estado'] == 'preventa'): ?>
                <span class="badge" style="background: #f39c12; font-size: 14px;">⏳ Pre-venta</span>
            <?php else: ?>
                <span class="badge" style="background: #e74c3c; font-size: 14px;">❌ Cancelada</span>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
        <div>
            <strong>Método de Pago:</strong><br>
            <?php if ($venta['metodo_pago'] == 'efectivo'): ?>
                <span class="badge" style="background: #27ae60;">💵 Efectivo</span>
            <?php else: ?>
                <span class="badge" style="background: #3498db;">💳 Tarjeta</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Descuento Aplicado:</strong><br>
            <?php if ($venta['descuento_aplicado'] > 0): ?>
                <span style="color: #e74c3c; font-size: 18px;"><?= FormatHelper::precio($venta['descuento_aplicado']) ?></span>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin descuento</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Total:</strong><br>
            <span style="color: #27ae60; font-size: 24px; font-weight: bold;"><?= FormatHelper::precio($venta['total']) ?></span>
        </div>
    </div>
    
    <?php if ($venta['fecha_formalizacion']): ?>
    <div style="margin-top: 15px; padding: 10px; background: #e8f5e9; border-radius: 4px;">
        <strong>Fecha de Formalización:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha_formalizacion'])) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Acciones para PRE-VENTAS -->
<?php if ($venta['estado'] == 'preventa'): ?>
<div class="card" style="margin-bottom: 20px; background: #fff3cd; border: 2px solid #f39c12;">
    <h3 style="margin-bottom: 15px; color: #856404;">⏳ Acciones de Pre-venta</h3>
    <p style="margin-bottom: 15px; color: #666;">
        Esta es una pre-venta. El stock está reservado pero el ingreso no se ha registrado todavía.
    </p>
    <div style="display: flex; gap: 10px;">
        <form method="POST" action="index.php?c=venta&a=formalizarPreventa&id=<?= $venta['id'] ?>" style="display: inline;" onsubmit="return confirm('¿Confirma FORMALIZAR esta pre-venta?\n\nSe registrará el ingreso de <?= FormatHelper::precio($venta['total']) ?>')">
            <button type="submit" class="btn btn-success">
                ✅ Formalizar Venta
            </button>
        </form>
        
        <button type="button" class="btn btn-danger" onclick="mostrarConfirmacionCancelacion()">
            ❌ Cancelar Pre-venta
        </button>
    </div>
</div>

<!-- Modal de confirmación de cancelación -->
<div id="modal_cancelacion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; min-width: 500px;">
        <h3 style="margin-bottom: 15px; color: #e74c3c;">⚠️ Confirmar Cancelación</h3>
        <p style="margin-bottom: 20px; line-height: 1.6;">
            ¿Está seguro de <strong>CANCELAR</strong> esta pre-venta?
        </p>
        <div style="background: #f8d7da; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <strong>⚠️ Esta acción:</strong>
            <ul style="margin: 10px 0 0 20px; line-height: 1.8;">
                <li>Devolverá el stock reservado</li>
                <li>NO registrará ningún ingreso</li>
                <li><strong>NO se podrá revertir</strong></li>
            </ul>
        </div>
        <form method="POST" action="index.php?c=venta&a=cancelarPreventa&id=<?= $venta['id'] ?>">
            <input type="hidden" name="confirmar_cancelacion" value="1">
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalCancelacion()">
                    Volver
                </button>
                <button type="submit" class="btn btn-danger">
                    Sí, Cancelar Pre-venta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarConfirmacionCancelacion() {
    document.getElementById('modal_cancelacion').style.display = 'block';
}

function cerrarModalCancelacion() {
    document.getElementById('modal_cancelacion').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal_cancelacion')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalCancelacion();
    }
});
</script>
<?php endif; ?>

<!-- Información adicional para ventas CANCELADAS -->
<?php if ($venta['estado'] == 'cancelada'): ?>
<div class="card" style="margin-bottom: 20px; background: #f8d7da; border: 2px solid #e74c3c;">
    <h3 style="margin-bottom: 10px; color: #721c24;">❌ Venta Cancelada</h3>
    <p style="color: #721c24;">
        Esta pre-venta fue cancelada. El stock fue devuelto y no se registró ningún ingreso.
    </p>
</div>
<?php endif; ?>

<h3 style="margin-bottom: 15px;">Productos Vendidos</h3>

<table>
    <thead>
        <tr>
            <th>Producto Padre</th>
            <th>Variedad</th>
            <th>Cantidad</th>
            <th>Precio Unit.</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detalles as $detalle): ?>
            <tr>
                <td>
                    <span style="color: #7f8c8d; font-size: 12px;"><?= htmlspecialchars($detalle['producto_padre_nombre']) ?></span>
                </td>
                <td><strong><?= htmlspecialchars($detalle['variedad_nombre']) ?></strong></td>
                <td><?= $detalle['cantidad'] ?></td>
                <td><?= FormatHelper::precio($detalle['precio_unitario']) ?></td>
                <td><strong><?= FormatHelper::precio($detalle['subtotal']) ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>