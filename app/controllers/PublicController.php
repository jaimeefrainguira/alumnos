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
        $term = '';
        $results = $this->buildResults($term, $anio);
        $totals = (new Abono())->totalsMatrix($anio);
        $cuotas = (new Cuota())->getByYear($anio);

        $this->view('public/search', compact('results', 'term', 'anio', 'totals', 'cuotas'));
    }

    public function search(): void
    {
        $term = trim($_GET['q'] ?? '');
        $anio = (int) ($_GET['anio'] ?? date('Y'));
        $results = $this->buildResults($term, $anio);
        $totals = (new Abono())->totalsMatrix($anio);
        $cuotas = (new Cuota())->getByYear($anio);

        $this->view('public/search', compact('results', 'term', 'anio', 'totals', 'cuotas'));
    }

    private function buildResults(string $term, int $anio): array
    {
        $alumnoModel = new Alumno();
        $results = $term === '' ? $alumnoModel->all() : $alumnoModel->search($term);

        $totals = (new Abono())->totalsMatrix($anio);
        $cuotas = (new Cuota())->getByYear($anio);
        $expected = array_sum($cuotas);

        foreach ($results as &$result) {
            $paid = array_sum($totals[(int) $result['id']] ?? []);
            $result['estado'] = $paid >= $expected && $expected > 0 ? 'Pagado' : ($paid > 0 ? 'Parcial' : 'Pendiente');
        }
        unset($result);

        return $results;
    }
}
