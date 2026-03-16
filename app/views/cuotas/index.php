<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Configurar cuota anual</h5>
                <form method="post" action="/cuotas" class="row g-2">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']); ?>">
                    <div class="col-6"><input class="form-control" type="number" name="anio" value="<?= $year; ?>" required></div>
                    <div class="col-6"><input class="form-control" type="number" step="0.01" min="0.01" name="valor" value="<?= htmlspecialchars((string) ($cuota['valor'] ?? '')); ?>" required></div>
                    <div class="col-12"><button class="btn btn-primary w-100">Guardar cuota</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
