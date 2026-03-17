<?php
$meses = [9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre',1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio'];
$totalPagado = array_sum($totals);
$totalEsperado = array_sum($cuotas);
$saldoPendiente = max($totalEsperado - $totalPagado, 0);

// Para mostrar una "cuota base" o indicar que es variable
$cuotasUnicas = array_unique($cuotas);
$displayCuota = count($cuotasUnicas) === 1 ? '$' . number_format(reset($cuotasUnicas), 2) : 'Variable';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Detalle de pagos de <?= htmlspecialchars($alumno['nombre']); ?></h4>
        <small class="text-muted">Ciclo Lectivo Sierra: <?= (int) $anio; ?> - <?= (int) $anio + 1; ?></small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="get" action="/alumnos/ver" class="d-flex gap-1">
            <input type="hidden" name="id" value="<?= (int) $alumno['id']; ?>">
            <input type="number" name="anio" class="form-control form-control-sm" value="<?= (int) $anio; ?>" style="width: 80px;">
            <button class="btn btn-sm btn-outline-secondary">Ver año</button>
        </form>
        <a class="btn btn-outline-primary" href="/reportes/alumno?id=<?= (int) $alumno['id']; ?>&anio=<?= (int) $anio; ?>">PDF alumno</a>
        <a class="btn btn-outline-secondary" href="/alumnos?anio=<?= (int) $anio; ?>">Volver</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card p-3"><small>Total pagado</small><h5>$<?= number_format((float) $totalPagado, 2); ?></h5></div></div>
    <div class="col-md-4"><div class="card p-3"><small>Cuota mensual</small><h5><?= $displayCuota; ?></h5></div></div>
    <div class="col-md-4"><div class="card p-3"><small>Saldo pendiente</small><h5>$<?= number_format((float) $saldoPendiente, 2); ?></h5></div></div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Datos del alumno</h5>
                <form method="post" action="/alumnos/editar" class="mb-3">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                    <input type="hidden" name="id" value="<?= (int) $alumno['id']; ?>">
                    <div class="mb-2">
                        <label class="form-label mb-0"><small>Nombre</small></label>
                        <input class="form-control form-control-sm" name="nombre" value="<?= htmlspecialchars($alumno['nombre']); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-0"><small>Teléfono</small></label>
                        <input class="form-control form-control-sm" name="telefono" value="<?= htmlspecialchars($alumno['telefono']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-0"><small>Dirección</small></label>
                        <input class="form-control form-control-sm" name="direccion" value="<?= htmlspecialchars($alumno['direccion']); ?>">
                    </div>
                    <button class="btn btn-sm btn-primary w-100">Guardar Cambios</button>
                </form>
                <form method="post" action="/alumnos/eliminar" onsubmit="return confirm('¿Seguro que deseas eliminar este alumno? Esta acción no se puede deshacer y borrará todos sus abonos.');">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                    <input type="hidden" name="id" value="<?= (int) $alumno['id']; ?>">
                    <button class="btn btn-sm btn-outline-danger w-100">Eliminar Alumno</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5>Movimientos por mes</h5>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead><tr><th>Mes</th><th>Total abonado</th><th>Detalle</th></tr></thead>
                <tbody>
                <?php foreach ($meses as $mes => $nombreMes):
                    $totalMes = (float) ($totals[$mes] ?? 0);
                ?>
                    <tr>
                        <td><?= $meses[$mes]; ?></td>
                        <td>$<?= number_format($totalMes, 2); ?></td>
                        <td>
                            <?php
                            $detalleMes = array_values(array_filter(
                                $detallePagos,
                                static fn (array $item): bool => (int) $item['mes_numero'] === $mes
                            ));
                            ?>
                            <?php if ($detalleMes === []): ?>
                                <span class="text-muted">Sin pagos</span>
                            <?php else: ?>
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($detalleMes[0]['items'] as $pago): ?>
                                        <li><?= htmlspecialchars($pago['fecha_abono']); ?> - <strong>$<?= number_format((float) $pago['valor'], 2); ?></strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
