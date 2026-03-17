<?php

declare(strict_types=1);

use App\Core\PdfFactory;

$dompdf = PdfFactory::make();
$meses = [
    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
    1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun'
];

$html = '
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
    h2 { text-align: center; color: #0056b3; font-size: 20px; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background-color: #f2f2f2; color: #333; font-weight: bold; padding: 6px; border: 1px solid #999; text-align: center; }
    td { padding: 5px; border: 1px solid #ccc; text-align: center; }
    .alumno-name { text-align: left; font-weight: bold; white-space: nowrap; }
    .status-paid { background-color: #d4edda; color: #155724; }
    .status-pending { background-color: #f8d7da; color: #721c24; }
    .status-partial { background-color: #fff3cd; color: #856404; }
</style>

<h2>Reporte General de Pagos - Ciclo ' . (int)$anio . '-' . ((int)$anio + 1) . '</h2>

<table>
    <thead>
        <tr>
            <th style="width: 200px;">Alumno</th>';
foreach ($meses as $m => $label) {
    $html .= '<th>' . $label . '</th>';
}
$html .= '</tr>
    </thead>
    <tbody>';

foreach ($alumnos as $alumno) {
    $html .= '<tr><td class="alumno-name">' . htmlspecialchars($alumno['nombre']) . '</td>';
    foreach ($meses as $m => $label) {
        $paid = (float) ($totals[(int)$alumno['id']][$m] ?? 0);
        $quota = (float) ($cuotas[$m] ?? 0);
        
        $statusClass = '';
        if ($quota > 0) {
            if ($paid >= $quota) {
                $statusClass = 'status-paid';
            } elseif ($paid > 0) {
                $statusClass = 'status-partial';
            } else {
                $statusClass = 'status-pending';
            }
        }
        
        $html .= '<td class="' . $statusClass . '">$' . number_format($paid, 2) . '</td>';
    }
    $html .= '</tr>';
}

$html .= '
    </tbody>
</table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('reporte-general-' . $anio . '.pdf', ['Attachment' => false]);
exit;
