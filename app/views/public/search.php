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
                <table class="table table-striped">
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
    </div>
</div>
