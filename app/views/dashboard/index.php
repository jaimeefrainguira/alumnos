<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3"><small>Total pagos</small><h4>$<?= number_format($totalPayments, 2); ?></h4></div></div>
    <div class="col-md-3"><div class="card p-3"><small>Pagos del día</small><h4>$<?= number_format($paymentsToday, 2); ?></h4></div></div>
    <div class="col-md-6"><div class="card p-3"><small>Alumnos con deuda</small><h4><?= count($morosos); ?></h4></div></div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <h5>Ingresos por mes (<?= $anio; ?>)</h5>
        <canvas id="chartIngresos" data-series='<?= json_encode(array_values($statsByMonth)); ?>'></canvas>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h5>Lista de morosos</h5>
        <table class="table table-sm">
            <thead><tr><th>Alumno</th><th>Mes</th><th>Monto pendiente</th></tr></thead>
            <tbody>
            <?php foreach ($morosos as $m): ?>
                <tr><td><?= htmlspecialchars($m['nombre']); ?></td><td><?= htmlspecialchars($m['mes_nombre'] ?? (string) $m['mes']); ?></td><td>$<?= number_format((float) $m['pendiente'], 2); ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
