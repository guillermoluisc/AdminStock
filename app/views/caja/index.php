<!-- app/views/caja/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>💰 Caja</h2>
    <a href="index.php?c=caja&a=altaMes" class="btn btn-success">+ Dar de Alta Caja del Mes</a>
</div>

<?php
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM ventas WHERE estado = 'caja' ORDER BY fecha DESC");
$entradas_caja = $stmt->fetchAll();
$total_caja = array_sum(array_column($entradas_caja, 'total'));
?>

<!-- <div class="card" style="background: #e8f5e9; border: 2px solid #27ae60; margin-bottom: 20px;">
    <h3 style="color: #27ae60;">Total ingresado en caja: <?= FormatHelper::precio($total_caja) ?></h3>
</div> -->

<?php if (empty($entradas_caja)): ?>
    <div class="card">
        <p style="text-align: center; color: #95a5a6; padding: 20px;">No hay entradas de caja registradas.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entradas_caja as $entrada): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($entrada['fecha'])) ?></td>
                    <td><?= htmlspecialchars($entrada['nombre_cliente']) ?></td>
                    <td><strong style="color: #27ae60;"><?= FormatHelper::precio($entrada['total']) ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>