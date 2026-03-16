<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/css/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Colegio Pagos</a>
        <div class="collapse navbar-collapse show">
            <ul class="navbar-nav me-auto">
                <?php if (isset($_SESSION['auth'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/alumnos">Alumnos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cuotas">Cuotas</a></li>
                    <li class="nav-item"><a class="nav-link" href="/reportes/general">PDF General</a></li>
                <?php endif; ?>
            </ul>
            <?php if (isset($_SESSION['auth'])): ?>
                <form method="post" action="/logout">
                    <button class="btn btn-light btn-sm">Salir</button>
                </form>
            <?php else: ?>
                <a class="btn btn-light btn-sm" href="/login">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">
    <?php if (!empty($_SESSION['flash_ok'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
