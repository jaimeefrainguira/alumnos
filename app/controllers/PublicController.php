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
        $anio = (int) date('Y');
        $results = $this->buildResults('', $anio);

        $this->view('public/search', compact('results', 'anio'));
    }

    public function search(): void
    {
        $term = trim($_GET['q'] ?? '');
        $anio = (int) ($_GET['anio'] ?? date('Y'));
        $results = $this->buildResults($term, $anio);

        $this->view('public/search', compact('results', 'term', 'anio'));
    }

    private function buildResults(string $term, int $anio): array
    {
        $alumnoModel = new Alumno();
        $results = $term === '' ? $alumnoModel->all() : $alumnoModel->search($term);

        $totals = (new Abono())->totalsMatrix($anio);
        $cuota = (new Cuota())->getByYear($anio);
        $valorCuota = (float) ($cuota['valor'] ?? 0);

        foreach ($results as &$result) {
            $paid = array_sum($totals[(int) $result['id']] ?? []);
            $expected = $valorCuota * 12;
            $result['estado'] = $paid >= $expected && $expected > 0 ? 'Pagado' : ($paid > 0 ? 'Parcial' : 'Pendiente');
        }
        unset($result);

        return $results;
    }
}
