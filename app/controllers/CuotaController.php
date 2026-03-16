<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cuota;

final class CuotaController extends Controller
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
        $year = (int) ($_GET['anio'] ?? date('Y'));
        $cuota = (new Cuota())->getByYear($year);
        $this->view('cuotas/index', compact('year', 'cuota'));
    }

    public function store(): void
    {
        $this->guard();
        if (!$this->validateCsrfToken($_POST['csrf'] ?? null)) {
            http_response_code(419);
            exit('CSRF inválido');
        }

        $year = (int) ($_POST['anio'] ?? date('Y'));
        $valor = (float) ($_POST['valor'] ?? 0);
        if ($valor <= 0) {
            $_SESSION['flash_error'] = 'Valor inválido.';
            $this->redirect('/cuotas');
        }

        (new Cuota())->upsert($year, $valor);
        $_SESSION['flash_ok'] = 'Cuota guardada.';
        $this->redirect('/cuotas?anio=' . $year);
    }
}
