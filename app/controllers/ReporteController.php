<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Abono;
use App\Models\Alumno;
use App\Models\Cuota;
use Throwable;

final class ReporteController extends Controller
{
    private function guard(): void
    {
        if (!isset($_SESSION['auth'])) {
            $this->redirect('/login');
        }
    }

    public function dashboard(): void
    {
        $this->guard();
        $anio = (int) ($_GET['anio'] ?? date('Y'));

        $abonoModel = new Abono();
        $alumnoModel = new Alumno();

        try {
            $statsByMonth = $abonoModel->statsByYear($anio);
            $totalPayments = $abonoModel->totalPayments($anio);
            $paymentsToday = $abonoModel->paymentsToday();
            $morosos = $alumnoModel->getMorosos($anio);
        } catch (Throwable $exception) {
            $statsByMonth = array_fill(1, 12, 0.0);
            $totalPayments = 0.0;
            $paymentsToday = 0.0;
            $morosos = [];
            $_SESSION['flash_error'] = 'No se pudo cargar toda la información del dashboard. Verifica la configuración de cuotas para el año seleccionado.';
        }

        $this->view('dashboard/index', compact('anio', 'statsByMonth', 'totalPayments', 'paymentsToday', 'morosos'));
    }

    public function alumnoPdf(): void
    {
        $alumnoId = (int) ($_GET['id'] ?? 0);
        $anio = (int) ($_GET['anio'] ?? date('Y'));

        $alumno = (new Alumno())->find($alumnoId);
        if ($alumno === null) {
            http_response_code(404);
            exit('Alumno no encontrado');
        }

        $totals = (new Abono())->totalsByAlumno($alumnoId, $anio);
        $cuotas = (new Cuota())->getByYear($anio);

        require __DIR__ . '/../../pdf/alumno.php';
    }

    public function generalPdf(): void
    {
        $this->guard();
        $anio = (int) ($_GET['anio'] ?? date('Y'));

        $alumnos = (new Alumno())->all();
        $totals = (new Abono())->totalsMatrix($anio);

        require __DIR__ . '/../../pdf/general.php';
    }
}
