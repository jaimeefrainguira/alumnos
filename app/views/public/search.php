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
                                    $quota = (float) ($cuotas[$numero] ?? 0);
                                    $status = $paid >= $quota && $quota > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending');
                                ?>
                                    <td>
                                        <button class="btn btn-sm w-100 status-<?= $status; ?> payment-cell-public"
                                                data-alumno-id="<?= (int) $alumno['id']; ?>"
                                                data-mes="<?= $numero; ?>"
                                                data-anio="<?= (int) ($anio ?? date('Y')); ?>">
                                            $<?= number_format($paid, 2); ?>
                                        </button>
                                    </td>
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

<!-- Modal Público (Solo Lectura) -->
<div class="modal fade" id="paymentModalPublic" tabindex="-1" aria-labelledby="paymentModalPublicLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalPublicLabel">Detalle de Abonos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul id="abonoHistoryPublic" class="list-group">
          <!-- Populated by JS -->
        </ul>
      </div>
    </div>
  </div>
</div>
