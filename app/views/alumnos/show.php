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
                                        <li class="d-flex justify-content-between align-items-center mb-1">
                                            <span><?= htmlspecialchars($pago['fecha_abono']); ?> - <strong>$<?= number_format((float) $pago['valor'], 2); ?></strong></span>
                                            <?php if (isset($_SESSION['auth'])): ?>
                                            <div class="btn-group btn-group-sm ms-2">
                                                <button class="btn btn-sm btn-outline-primary py-0 px-1 edit-abono" 
                                                        data-id="<?= $pago['id']; ?>" 
                                                        data-valor="<?= $pago['valor']; ?>" 
                                                        data-fecha="<?= $pago['fecha_abono']; ?>"
                                                        title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger py-0 px-1 delete-abono" 
                                                        data-id="<?= $pago['id']; ?>"
                                                        title="Borrar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </li>
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

<!-- Modal para editar abono -->
<div class="modal fade" id="editAbonoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Editar Abono</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAbonoForm">
                    <input type="hidden" name="id" id="editAbonoId">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                    <div class="mb-2">
                        <label class="form-label mb-0"><small>Fecha</small></label>
                        <input type="date" class="form-control form-control-sm" name="fecha_abono" id="editAbonoFecha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-0"><small>Valor ($)</small></label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="valor" id="editAbonoValor" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editAbonoModal'));
    
    // Abrir modal de edición
    document.querySelectorAll('.edit-abono').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editAbonoId').value = this.dataset.id;
            document.getElementById('editAbonoValor').value = this.dataset.valor;
            document.getElementById('editAbonoFecha').value = this.dataset.fecha;
            editModal.show();
        });
    });

    // Manejar envío de edición
    document.getElementById('editAbonoForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const res = await fetch('/abonos/editar', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.ok) {
                location.reload();
            } else {
                alert(data.message || 'Error al actualizar');
            }
        } catch (err) {
            alert('Error de conexión');
        }
    });

    // Manejar eliminación
    document.querySelectorAll('.delete-abono').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('¿Seguro que deseas eliminar este abono?')) return;
            
            const formData = new FormData();
            formData.append('id', this.dataset.id);
            formData.append('csrf', '<?= $_SESSION['csrf']; ?>');
            
            try {
                const res = await fetch('/abonos/eliminar', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.ok) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al eliminar');
                }
            } catch (err) {
                alert('Error de conexión');
            }
        });
    });
});
</script>
