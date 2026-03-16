<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Abono;
use App\Models\Alumno;
use App\Models\Cuota;

final class AlumnoController extends Controller
{
    private function guard(): void
    {
        if (!isset($_SESSION['auth'])) {
            $this->redirect('/login');
        }
    }

    public function index(): void
    {
        $this->guard();
        $anio = (int) ($_GET['anio'] ?? date('Y'));

        $alumnos = (new Alumno())->all();
        $cuota = (new Cuota())->getByYear($anio);
        $valorCuota = (float) ($cuota['valor'] ?? 0);
        $totals = (new Abono())->totalsMatrix($anio);

        $this->view('alumnos/index', compact('alumnos', 'totals', 'anio', 'valorCuota'));
    }

    public function show(): void
    {
        $this->guard();
        $alumnoId = (int) ($_GET['id'] ?? 0);
        $anio = (int) ($_GET['anio'] ?? date('Y'));

        $alumno = (new Alumno())->find($alumnoId);
        if ($alumno === null) {
            http_response_code(404);
            exit('Alumno no encontrado.');
        }

        $abonoModel = new Abono();
        $detallePagos = $abonoModel->detailsByAlumno($alumnoId, $anio);
        $totals = $abonoModel->totalsByAlumno($alumnoId, $anio);
        $cuota = (new Cuota())->getByYear($anio);
        $valorCuota = (float) ($cuota['valor'] ?? 0);

        $this->view('alumnos/show', compact('alumno', 'anio', 'detallePagos', 'totals', 'valorCuota'));
    }

    public function store(): void
    {
        $this->guard();
        if (!$this->validateCsrfToken($_POST['csrf'] ?? null)) {
            http_response_code(419);
            exit('CSRF inválido.');
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
        ];

        if ($data['nombre'] === '') {
            $_SESSION['flash_error'] = 'El nombre es obligatorio.';
            $this->redirect('/alumnos');
        }

        (new Alumno())->create($data);
        $_SESSION['flash_ok'] = 'Alumno creado correctamente.';
        $this->redirect('/alumnos');
    }
}
