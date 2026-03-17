<div class="dashboard-modern">
    <!-- Header with Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold mb-0">Tablero de Control</h2>
            <p class="text-muted mb-0">Resumen académico y financiero del ciclo lectivo.</p>
        </div>
        <div class="filter-glass p-2">
            <form method="get" action="/dashboard" class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-primary ms-2">Ciclo:</span>
                <input type="number" name="anio" class="form-control form-control-sm border-0 bg-transparent fw-bold" value="<?= $anio; ?>" style="width: 80px;">
                <span class="text-muted">- <?= $anio + 1; ?></span>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm ms-2">Actualizar</button>
            </form>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card glass-blue p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="label text-uppercase small ls-1">Total Ingresos</span>
                        <h3 class="value mt-1 fw-bold">$<?= number_format($totalPayments, 2); ?></h3>
                    </div>
                    <div class="icon-circle bg-white text-primary shadow-sm">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="progress mt-3 bg-white-10" style="height: 4px;">
                    <div class="progress-bar bg-white" style="width: 75%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card glass-green p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="label text-uppercase small ls-1">Recaudado Hoy</span>
                        <h3 class="value mt-1 fw-bold">$<?= number_format($paymentsToday, 2); ?></h3>
                    </div>
                    <div class="icon-circle bg-white text-success shadow-sm">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-3 small text-white-50">Fecha: <?= date('d/m/Y'); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card glass-red p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="label text-uppercase small ls-1">Pendientes de Pago</span>
                        <h3 class="value mt-1 fw-bold"><?= count($morosos); ?></h3>
                    </div>
                    <div class="icon-circle bg-white text-danger shadow-sm">
                        <i class="bi bi-person-exclamation"></i>
                    </div>
                </div>
                <div class="mt-3 small text-white-50">Alumnos en mora en el ciclo actual.</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Chart Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 py-3 ps-4 pt-4">
                    <h5 class="fw-bold mb-0">Flujo de Ingresos</h5>
                    <small class="text-muted">Procesado mensual para el periodo <?= $anio; ?>-<?= $anio + 1; ?></small>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 300px;">
                        <canvas id="chartIngresos" data-series='<?= json_encode(array_values($statsByMonth)); ?>'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Section: Morosos -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 ps-4 pt-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Alumnos en Mora</h5>
                    <span class="badge bg-danger-subtle text-danger rounded-pill"><?= count($morosos); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 380px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light-subtle small text-uppercase text-muted sticky-top">
                                <tr>
                                    <th class="ps-4 py-3">Alumno</th>
                                    <th class="py-3">Periodo</th>
                                    <th class="pe-4 py-3 text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($morosos, 0, 10) as $m): ?>
                                <tr class="border-bottm-light">
                                    <td class="ps-4 py-3">
                                        <div class="fw-medium text-dark"><?= htmlspecialchars($m['nombre']); ?></div>
                                    </td>
                                    <td><span class="badge bg-light text-dark fw-normal"><?= htmlspecialchars($m['mes_nombre']); ?></span></td>
                                    <td class="pe-4 text-end fw-bold text-danger">$<?= number_format((float) $m['pendiente'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (count($morosos) == 0): ?>
                                <tr><td colspan="3" class="text-center py-5 text-muted">No hay deudas registradas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($morosos) > 10): ?>
                    <div class="p-3 text-center border-top">
                        <small class="text-muted">Mostrando los primeros 10 de <?= count($morosos); ?></small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Settings Section -->
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0 border-end-md">
                            <h5 class="fw-bold"><i class="bi bi-shield-lock me-2 text-primary"></i>Configuración de Acceso</h5>
                            <p class="text-muted small mb-0">Actualiza tus credenciales corporativas para mayor seguridad.</p>
                        </div>
                        <div class="col-md-8 ps-md-4">
                            <form method="post" action="/ajustes/credenciales" class="row g-3 align-items-end">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Usuario Admin</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control bg-light" name="nuevo_usuario" placeholder="<?= htmlspecialchars($_SESSION['auth']['usuario'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Contraseña</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                                        <input type="password" class="form-control bg-light" name="nueva_password" placeholder="Nueva clave">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-pill py-2">Aplicar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-modern { padding: 1rem 0; }
.ls-1 { letter-spacing: 0.5px; }
.bg-white-10 { background-color: rgba(255, 255, 255, 0.2); }
.filter-glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 50px; border: 1px solid rgba(0,0,0,0.05); }
.stat-card { border-radius: 1.5rem; color: white; position: relative; overflow: hidden; transition: transform 0.3s ease; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.stat-card:hover { transform: translateY(-5px); }
.glass-blue { background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); }
.glass-green { background: linear-gradient(135deg, #198754 0%, #0c4a2d 100%); }
.glass-red { background: linear-gradient(135deg, #dc3545 0%, #8b101c 100%); }
.icon-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.border-end-md { border-right: 1px solid #eee; }
@media (max-width: 767px) { .border-end-md { border-right: none; } }
.sticky-top { background: white; z-index: 10; }
</style>
