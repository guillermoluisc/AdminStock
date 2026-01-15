<!-- app/views/ventas/detalle.php -->
<div style="margin-bottom: 20px;">
    <a href="index.php?c=venta&a=index" class="btn btn-secondary">← Volver al Historial</a>
</div>

<div class="card" style="margin-bottom: 20px;">
    <h2>Detalle de Venta #<?= $venta['id'] ?></h2>
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px;">
        <div>
            <strong>Fecha:</strong><br>
            <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?>
        </div>
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
                <span style="color: #e74c3c; font-size: 18px;">-$<?= number_format($venta['descuento_aplicado'], 2) ?></span>
            <?php else: ?>
                <span style="color: #95a5a6;">Sin descuento</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Total:</strong><br>
            <span style="color: #27ae60; font-size: 24px; font-weight: bold;">$<?= number_format($venta['total'], 2) ?></span>
        </div>
    </div>
</div>

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
                <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                <td><strong>$<?= number_format($detalle['subtotal'], 2) ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>