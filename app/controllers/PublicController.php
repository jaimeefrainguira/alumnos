<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Abono;
use App\Models\Alumno;
use App\Models\Cuota;

final class PublicController extends Controller
{
    public function home(): void
    {
        $this->view('public/search', ['results' => []]);
    }

    public function search(): void
    {
        $term = trim($_GET['q'] ?? '');
        $anio = (int) ($_GET['anio'] ?? date('Y'));
        $results = [];

        if ($term !== '') {
            $results = (new Alumno())->search($term);
            $totals = (new Abono())->totalsMatrix($anio);
            $cuota = (new Cuota())->getByYear($anio);
            $valorCuota = (float) ($cuota['valor'] ?? 0);

            foreach ($results as &$result) {
                $paid = array_sum($totals[(int) $result['id']] ?? []);
                $expected = $valorCuota * 12;
                $result['estado'] = $paid >= $expected && $expected > 0 ? 'Pagado' : ($paid > 0 ? 'Parcial' : 'Pendiente');
            }
            unset($result);
        }

        $this->view('public/search', compact('results', 'term', 'anio'));
    }
}
