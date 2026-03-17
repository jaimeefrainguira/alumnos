<?php

declare(strict_types=1);

use App\Core\PdfFactory;

$dompdf = PdfFactory::make();
$meses = [
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio'
];
$totalPagado = array_sum($totals);
$totalEsperado = array_sum($cuotas);
$saldoPendiente = max($totalEsperado - $totalPagado, 0);

$html = '<h2 style="text-align:center">Reporte de Pagos</h2>';
$html .= '<p><strong>Alumno:</strong> ' . htmlspecialchars($alumno['nombre']) . '</p>';
$html .= '<p><strong>Ciclo Lectivo:</strong> ' . (int)$anio . ' - ' . ((int)$anio + 1) . '</p>';
$html .= '<table border="1" cellspacing="0" cellpadding="6" width="100%">
            <tr style="background:#f2f2f2"><th>Mes</th><th>Pagado</th></tr>';
foreach ($meses as $m => $nombreMes) {
    $html .= '<tr><td>' . $nombreMes . '</td><td>$' . number_format((float) ($totals[$m] ?? 0), 2) . '</td></tr>';
}
$html .= '</table>';
$html .= '<p><strong>Total pagado:</strong> $' . number_format((float) $totalPagado, 2) . '</p>';
$html .= '<p><strong>Saldo pendiente:</strong> $' . number_format((float) $saldoPendiente, 2) . '</p>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('reporte-alumno-' . $alumno['codigo'] . '.pdf', ['Attachment' => true]);
exit;
