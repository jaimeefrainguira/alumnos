<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Resumen General</h5>
    <form method="get" action="/dashboard" class="d-flex gap-2">
        <input type="number" name="anio" class="form-control form-control-sm" value="<?= $anio; ?>" style="width: 100px;">
        <button class="btn btn-sm btn-primary">Filtrar</button>
    </form>
</div>
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
<div class="card mt-3 mb-4">
    <div class="card-body">
        <h5 class="mb-3">Ajustes de cuenta</h5>
        <form method="post" action="/ajustes/credenciales" class="row g-3 align-items-end">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
            <div class="col-md-4">
                <label class="form-label">Nuevo usuario</label>
                <input type="text" class="form-control" name="nuevo_usuario" placeholder="<?= htmlspecialchars($_SESSION['auth']['usuario'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" class="form-control" name="nueva_password" placeholder="Dejar en blanco para mantener actual">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-warning w-100">Actualizar Credenciales</button>
            </div>
            <div class="col-12">
                <small class="text-muted">Nota: Solo se actualizarán los campos que llenes; si dejas algo en blanco, mantendrá su valor actual.</small>
            </div>
        </form>
    </div>
</div>
