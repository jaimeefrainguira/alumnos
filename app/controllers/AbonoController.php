<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Abono;

final class AbonoController extends Controller
{
    private function guard(): void
    {
        if (!isset($_SESSION['auth'])) {
            $this->redirect('/login');
        }
    }

    public function store(): void
    {
        $this->guard();

        if (!$this->validateCsrfToken($_POST['csrf'] ?? null)) {
            $this->json(['ok' => false, 'message' => 'CSRF inválido'], 419);
            return;
        }

        $mes = (int) ($_POST['mes'] ?? 0);
        $anio = (int) ($_POST['anio'] ?? 0);

        // Si el mes es de la segunda parte del ciclo (Ene-Jun), 
        // almacenamos el año siguiente al que se envió (que es el año de inicio del ciclo)
        if ($mes >= 1 && $mes <= 6) {
            $anio++;
        }

        $data = [
            'alumno_id' => (int) ($_POST['alumno_id'] ?? 0),
            'mes' => $mes,
            'anio' => $anio,
            'valor' => (float) ($_POST['valor'] ?? 0),
            'fecha_abono' => $_POST['fecha_abono'] ?? date('Y-m-d'),
        ];

        if ($data['alumno_id'] < 1 || $data['mes'] < 1 || $data['mes'] > 12 || $data['valor'] <= 0) {
            $this->json(['ok' => false, 'message' => 'Datos inválidos'], 422);
            return;
        }

        (new Abono())->create($data);
        $this->json(['ok' => true, 'message' => 'Abono registrado']);
    }

    public function history(): void
    {
        $alumnoId = (int) ($_GET['alumno_id'] ?? 0);
        $mes = (int) ($_GET['mes'] ?? 0);
        $anio = (int) ($_GET['anio'] ?? date('Y'));

        if ($mes >= 1 && $mes <= 6) {
            $anio++;
        }

        $data = (new Abono())->monthHistory($alumnoId, $mes, $anio);
        $this->json(['ok' => true, 'data' => $data]);
    }
}
