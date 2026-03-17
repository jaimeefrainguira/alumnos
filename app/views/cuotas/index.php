<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Configurar cuota mensual</h5>
                <form method="post" action="/cuotas" class="row g-2">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                    <div class="col-6">
                        <label class="form-label mb-0"><small>Año</small></label>
                        <input class="form-control" type="number" name="anio" value="<?= $year; ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-0"><small>Mes</small></label>
                        <select name="mes" class="form-select" required>
                            <?php 
                            $meses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
                            foreach($meses as $num => $nombre): 
                            ?>
                                <option value="<?= $num; ?>" <?= ($cuota['mes'] ?? date('n')) == $num ? 'selected' : ''; ?>><?= $nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-0"><small>Valor</small></label>
                        <input class="form-control" type="number" step="0.01" min="0.01" name="valor" value="<?= htmlspecialchars((string) ($cuota['valor'] ?? '')); ?>" required>
                    </div>
                    <div class="col-12 mt-3"><button class="btn btn-primary w-100">Guardar cuota</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
