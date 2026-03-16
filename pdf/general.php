<?php

declare(strict_types=1);

use App\Core\PdfFactory;

$dompdf = PdfFactory::make();
$html = '<h2>Reporte general de pagos ' . $anio . '</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="4" width="100%"><tr><th>Alumno</th>';
for ($m = 1; $m <= 12; $m++) {
    $html .= '<th>' . $m . '</th>';
}
$html .= '</tr>';

foreach ($alumnos as $alumno) {
    $html .= '<tr><td>' . htmlspecialchars($alumno['nombre']) . '</td>';
    for ($m = 1; $m <= 12; $m++) {
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
