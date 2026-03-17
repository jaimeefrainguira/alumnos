<?php $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre']; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-4">Configurar Cuota</h5>
                <form method="post" action="/cuotas" class="row g-3">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                    
                    <div class="col-12">
                        <label class="form-label mb-1 small text-muted">Año Lectivo</label>
                        <input class="form-control" type="number" name="anio" value="<?= $year; ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label mb-1 small text-muted">Mes</label>
                        <select name="mes" class="form-select" required>
                            <?php foreach($meses as $num => $nombre): ?>
                                <option value="<?= $num; ?>" <?= date('n') == $num ? 'selected' : ''; ?>><?= $nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label mb-1 small text-muted">Valor de la Cuota ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">$</span>
                            <input class="form-control" type="number" step="0.01" min="0.01" name="valor" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button class="btn btn-primary w-100 py-2 fw-bold">Guardar Configuración</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="alert alert-info mt-3 border-0 shadow-sm">
            <small><i class="bi bi-info-circle me-1"></i> Si actualizas un mes que ya tiene cuota, el valor se reemplazará.</small>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Listado de Cuotas - <?= $year; ?></h5>
                    <div class="d-flex gap-2">
                        <form method="get" action="/cuotas" class="d-flex gap-1">
                            <input type="number" name="anio" class="form-control form-control-sm" value="<?= $year; ?>" style="width: 80px;">
                            <button class="btn btn-sm btn-outline-secondary">Cambiar</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mes</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalYear = 0;
                            foreach($meses as $num => $nombre): 
                                $valor = (float) ($cuotas[$num] ?? 0);
                                $totalYear += $valor;
                            ?>
                            <tr>
                                <td class="fw-bold"><?= $nombre; ?></td>
                                <td>
                                    <?php if ($valor > 0): ?>
                                        <span class="text-dark fw-bold">$<?= number_format($valor, 2); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted italic small">No configurada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($valor > 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($valor > 0): ?>
                                        <button class="btn btn-sm btn-light border" onclick="editCuota(<?= $num; ?>, <?= $valor; ?>)" title="Editar">
                                            <small>Editar</small>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>TOTAL ANUAL</td>
                                <td colspan="3" class="text-primary">$<?= number_format($totalYear, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editCuota(mes, valor) {
    document.querySelector('select[name="mes"]').value = mes;
    document.querySelector('input[name="valor"]').value = valor;
    document.querySelector('button[type="submit"]').textContent = 'Actualizar Cuota';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
