<?php $meses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Matriz de pagos <?= $anio; ?></h4>
    <a class="btn btn-outline-primary" href="/reportes/general?anio=<?= $anio; ?>">Descargar PDF General</a>
</div>
<div class="card mb-4"><div class="card-body">
<form method="post" action="/alumnos" class="row g-2">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
    <div class="col-md-4"><input class="form-control" name="nombre" placeholder="Nombre" required></div>
    <div class="col-md-3"><input class="form-control" name="telefono" placeholder="Teléfono"></div>
    <div class="col-md-3"><input class="form-control" name="direccion" placeholder="Dirección"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Crear alumno</button></div>
    <div class="col-12"><small class="text-muted">El código del alumno se genera automáticamente.</small></div>
</form></div></div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle payment-matrix">
<thead><tr><th>Alumno</th><?php foreach($meses as $m): ?><th><?= $m; ?></th><?php endforeach; ?><th>Detalle</th></tr></thead>
<tbody>
<?php foreach ($alumnos as $alumno): ?>
<tr>
    <td><?= htmlspecialchars($alumno['nombre']); ?><br><small><?= htmlspecialchars($alumno['codigo']); ?></small></td>
    <?php foreach ($meses as $numero => $nombreMes):
        $paid = (float) ($totals[$alumno['id']][$numero] ?? 0);
        $status = $paid >= $valorCuota && $valorCuota > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending');
    ?>
        <td><span class="status-pill status-<?= $status; ?>">$<?= number_format($paid, 2); ?></span></td>
    <?php endforeach; ?>
    <td>
        <a class="btn btn-outline-secondary btn-sm w-100" href="/alumnos/ver?id=<?= (int) $alumno['id']; ?>&anio=<?= (int) $anio; ?>">Ver</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
