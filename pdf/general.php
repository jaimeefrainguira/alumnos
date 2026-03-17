<?php

declare(strict_types=1);

use App\Core\PdfFactory;

$dompdf = PdfFactory::make();
$meses = [
    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
    1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun'
];
$html = '<h2>Reporte General de Pagos - Ciclo ' . (int)$anio . '-' . ((int)$anio + 1) . '</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="4" width="100%" style="font-size:10px"><tr><th>Alumno</th>';
foreach ($meses as $m => $label) {
    $html .= '<th>' . $label . '</th>';
}
$html .= '</tr>';

foreach ($alumnos as $alumno) {
    $html .= '<tr><td>' . htmlspecialchars($alumno['nombre']) . '</td>';
    foreach ($meses as $m => $label) {
        $amount = (float) ($totals[$alumno['id']][$m] ?? 0);
        $html .= '<td>$' . number_format($amount, 2) . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('reporte-general-' . $anio . '.pdf', ['Attachment' => true]);
exit;
