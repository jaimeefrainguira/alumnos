<?php $meses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic']; ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card mb-3">
            <div class="card-body">
                <h4>Consulta pública de pagos</h4>
                <form method="get" action="/buscar" class="row g-2">
                    <div class="col-md-9"><input class="form-control" name="q" value="<?= htmlspecialchars($term ?? ''); ?>" placeholder="Buscar por nombre, teléfono o código"></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="anio" value="<?= htmlspecialchars((string) ($anio ?? date('Y'))); ?>"></div>
                    <div class="col-md-1"><button class="btn btn-primary w-100">Buscar</button></div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Alumno</th><th>Código</th><th>Estado</th><th>Acción</th></tr></thead>
                    <tbody>
                    <?php foreach (($results ?? []) as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['nombre']); ?></td>
                            <td><?= htmlspecialchars($r['codigo']); ?></td>
                            <td><?= htmlspecialchars($r['estado']); ?></td>
                            <td><a class="btn btn-outline-secondary btn-sm" href="/reportes/alumno?id=<?= (int) $r['id']; ?>&anio=<?= (int) ($anio ?? date('Y')); ?>">PDF</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Matriz de pagos <?= (int) ($anio ?? date('Y')); ?></h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle payment-matrix mb-0">
                        <thead><tr><th>Alumno</th><?php foreach ($meses as $m): ?><th><?= $m; ?></th><?php endforeach; ?></tr></thead>
                        <tbody>
                        <?php foreach (($results ?? []) as $alumno): ?>
                            <tr>
                                <td><?= htmlspecialchars($alumno['nombre']); ?><br><small><?= htmlspecialchars($alumno['codigo']); ?></small></td>
                                <?php foreach ($meses as $numero => $nombreMes):
                                    $paid = (float) (($totals[(int) $alumno['id']][$numero] ?? 0));
                                    $status = $paid >= ($valorCuota ?? 0) && ($valorCuota ?? 0) > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending');
                                ?>
                                    <td><span class="status-pill status-<?= $status; ?>">$<?= number_format($paid, 2); ?></span></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
