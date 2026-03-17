<?php $meses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Matriz de pagos <?= $anio; ?></h4>
    <a class="btn btn-outline-primary" href="/reportes/general?anio=<?= $anio; ?>">Descargar PDF General</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card"><div class="card-body py-2">
            <label class="form-label mb-1"><small class="text-muted fw-bold">Filtrar por Año</small></label>
            <form method="get" action="/alumnos" class="d-flex gap-1">
                <input type="number" name="anio" class="form-control form-control-sm" value="<?= $anio; ?>" required>
                <button class="btn btn-sm btn-primary">Ver</button>
            </form>
        </div></div>
    </div>
    <?php if (isset($_SESSION['auth'])): ?>
    <div class="col-md-9">
        <div class="card"><div class="card-body py-2">
            <label class="form-label mb-1"><small class="text-muted fw-bold">Registrar Nuevo Alumno</small></label>
            <form method="post" action="/alumnos" class="row g-2">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                <div class="col-md-4"><input class="form-control form-control-sm" name="nombre" placeholder="Nombre completo" required></div>
                <div class="col-md-3"><input class="form-control form-control-sm" name="telefono" placeholder="Teléfono"></div>
                <div class="col-md-3"><input class="form-control form-control-sm" name="direccion" placeholder="Dirección"></div>
                <div class="col-md-2"><button class="btn btn-sm btn-success w-100">Crear Alumno</button></div>
            </form>
        </div></div>
    </div>
    <?php endif; ?>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle payment-matrix">
<thead><tr><th>Alumno</th><?php foreach($meses as $m): ?><th><?= $m; ?></th><?php endforeach; ?></tr></thead>
<tbody>
<?php foreach ($alumnos as $alumno): ?>
<tr>
    <td>
        <a href="/alumnos/ver?id=<?= (int) $alumno['id']; ?>&anio=<?= $anio; ?>" class="text-decoration-none fw-bold">
            <?= htmlspecialchars($alumno['nombre']); ?>
        </a><br>
        <small><?= htmlspecialchars($alumno['codigo']); ?></small>
    </td>
    <?php foreach ($meses as $numero => $nombreMes):
        $paid = (float) ($totals[$alumno['id']][$numero] ?? 0);
        $quota = (float) ($cuotas[$numero] ?? 0);
        $status = $paid >= $quota && $quota > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending');
    ?>
        <td>
            <button class="btn btn-sm w-100 status-<?= $status; ?> payment-cell"
                    data-alumno-id="<?= (int) $alumno['id']; ?>"
                    data-alumno-nombre="<?= htmlspecialchars($alumno['nombre']); ?>"
                    data-mes="<?= $numero; ?>"
                    data-anio="<?= $anio; ?>">
                $<?= number_format($paid, 2); ?>
            </button>
        </td>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Detalles de Abonos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="mb-3">Historial de Pagos del Mes</h6>
        <ul id="abonoHistory" class="list-group mb-4">
          <!-- Populated by JS -->
        </ul>
        
        <?php if (isset($_SESSION['auth'])): ?>
        <h6 class="mb-3">Añadir Nuevo Abono</h6>
        <form id="abonoForm">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
          <input type="hidden" name="alumno_id" id="formAlumnoId">
          <input type="hidden" name="mes" id="formMes">
          <input type="hidden" name="anio" id="formAnio">
          <div class="mb-3">
            <label for="fecha_abono" class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha_abono" id="fecha_abono" value="<?= date('Y-m-d'); ?>" required>
          </div>
          <div class="mb-3">
            <label for="valor" class="form-label">Valor ($)</label>
            <input type="number" step="0.01" class="form-control" name="valor" id="valor" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Registrar Abono</button>
          </div>
        </form>
        <?php else: ?>
        <div class="alert alert-info py-2 mb-0">
            <small>Inicie sesión para registrar abonos.</small>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
