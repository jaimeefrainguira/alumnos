<?php

declare(strict_types=1);

use App\Core\PdfFactory;

$dompdf = PdfFactory::make();
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
];
$totalPagado = array_sum($totals);
$saldoPendiente = max(($valorCuota * 12) - $totalPagado, 0);

$html = '<h2>Reporte de alumno</h2>';
$html .= '<p><strong>Alumno:</strong> ' . htmlspecialchars($alumno['nombre']) . '</p>';
$html .= '<table border="1" cellspacing="0" cellpadding="6" width="100%"><tr><th>Mes</th><th>Pagado</th></tr>';
for ($m = 1; $m <= 12; $m++) {
    $html .= '<tr><td>' . $meses[$m] . '</td><td>$' . number_format((float) $totals[$m], 2) . '</td></tr>';
}
$html .= '</table>';
$html .= '<p><strong>Total pagado:</strong> $' . number_format((float) $totalPagado, 2) . '</p>';
$html .= '<p><strong>Saldo pendiente:</strong> $' . number_format((float) $saldoPendiente, 2) . '</p>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('reporte-alumno-' . $alumno['codigo'] . '.pdf', ['Attachment' => true]);
exit;
