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
        
        <?php if ($venta['fecha_formalizacion']): ?>
        <div>
            <strong>Fecha de Formalización:</strong><br>
            <?= date('d/m/Y H:i', strtotime($venta['fecha_formalizacion'])) ?>
        </div>
        <?php endif; ?>
        
        <div>
            <strong>Total:</strong><br>
            <span style="color: #27ae60; font-size: 24px; font-weight: bold;"><?= FormatHelper::precio($venta['total']) ?></span>
        </div>
    </div>
    
    <?php if ($venta['estado'] == 'preventa'): ?>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
        <div>
            <strong>Monto Total:</strong><br>
            <span style="color: #3498db; font-size: 20px; font-weight: bold;"><?= FormatHelper::precio($venta['total']) ?></span>
        </div>
        <div>
            <strong>Adelantos Registrados:</strong><br>
            <span style="color: #27ae60; font-size: 20px; font-weight: bold;">
                <?= FormatHelper::precio($adelantos_registrados ?? 0) ?>
            </span>
        </div>
        <div>
            <strong>Saldo Pendiente:</strong><br>
            <span style="color: #e74c3c; font-size: 24px; font-weight: bold;">
                <?= FormatHelper::precio($saldo_restante ?? $venta['total']) ?>
            </span>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<!-- Acciones para PRE-VENTAS -->
<?php if ($venta['estado'] == 'preventa'): ?>
<div class="card" style="margin-bottom: 20px; background: #fff3cd; border: 2px solid #f39c12;">
    <h3 style="margin-bottom: 15px; color: #856404;">⏳ Acciones de Pre-venta</h3>
        
    <!-- Historial de adelantos -->
    <?php if (!empty($historial_adelantos) && count($historial_adelantos) > 0): ?>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
        <h4 style="margin-bottom: 10px;">📋 Historial de Adelantos</h4>
        <table style="width: 100%; font-size: 14px;">
            <thead>
                <tr style="background: #e9ecef;">
                    <th style="padding: 8px; text-align: left;">Fecha</th>
                    <th style="padding: 8px; text-align: right;">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial_adelantos as $adelanto): ?>
                <tr>
                    <td style="padding: 8px;"><?= date('d/m/Y H:i', strtotime($adelanto['fecha'])) ?></td>
                    <td style="padding: 8px; text-align: right; color: #27ae60; font-weight: bold;">
                        <?= FormatHelper::precio($adelanto['monto_venta']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Formulario de adelanto -->
    <?php 
    $monto_restante = $saldo_restante ?? $venta['total'];
    if ($monto_restante > 0): 
    ?>
    <div style="background: #e8f5e9; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
        <h4 style="margin-bottom: 10px;">💵 Registrar Adelanto</h4>
        <form method="POST" action="index.php?c=venta&a=registrarAdelanto&id=<?= $venta['id'] ?>" 
            onsubmit="return validarAdelanto(<?= $monto_restante ?>)">
            <div style="margin-bottom: 15px;">
                <label for="monto_adelanto">Monto del Adelanto:</label>
                <input type="number" 
                    id="monto_adelanto" 
                    name="monto_adelanto" 
                    step="0.01" 
                    min="0.01" 
                    max="<?= $monto_restante ?>"
                    placeholder="Ingrese el monto"
                    required
                    style="width: 100%; padding: 10px; font-size: 16px;">
                <small style="color: #666;">Máximo: <?= FormatHelper::precio($monto_restante) ?></small>
            </div>
            <div style="text-align: center;">
                <button type="submit" class="btn btn-success">
                    ➕ Registrar Adelanto
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Botones de acción -->
    <p style="margin-bottom: 15px; color: #666;">
        <?php if ($monto_restante <= 0): ?>
            ✅ <strong>El saldo está completo.</strong> Puede formalizar la venta.
        <?php else: ?>
            Esta pre-venta aún tiene saldo pendiente de <?= FormatHelper::precio($monto_restante) ?>. 
            Puede registrar adelantos parciales o formalizar el total restante.
        <?php endif; ?>
    </p>
    
    <div style="display: flex; gap: 10px;">
        <form method="POST" 
              action="index.php?c=venta&a=formalizarPreventa&id=<?= $venta['id'] ?>&montoRestante=<?= $monto_restante ?>" 
              style="display: inline;" 
              onsubmit="return confirm('¿Confirma FORMALIZAR esta pre-venta?\n\n<?php 
                  if ($monto_restante > 0) {
                      echo "Se registrará el pago del saldo restante de " . FormatHelper::precio($monto_restante);
                  } else {
                      echo "La venta ya está totalmente pagada.";
                  }
              ?>')">
            <button type="submit" class="btn btn-success">
                ✅ Formalizar Venta <?= $monto_restante > 0 ? '(' . FormatHelper::precio($monto_restante) . ')' : '' ?>
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

function validarAdelanto(montoMaximo) {
    const monto = parseFloat(document.getElementById('monto_adelanto').value);
    
    if (isNaN(monto) || monto <= 0) {
        alert('Ingrese un monto válido mayor a cero');
        return false;
    }
    
    if (monto > montoMaximo) {
        alert('El monto no puede ser mayor al saldo restante de ' + new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(montoMaximo));
        return false;
    }
    
    return confirm('¿Confirma registrar un adelanto de ' + new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS'
    }).format(monto) + '?');
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
            <th>Producto</th>
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