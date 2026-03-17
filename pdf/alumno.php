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

$html = '
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #333; }
    h2 { text-align: center; color: #0056b3; font-size: 24px; margin-bottom: 20px; }
    .header-info { margin-bottom: 20px; font-size: 16px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 16px; }
    th { background-color: #f2f2f2; color: #333; font-weight: bold; padding: 12px; border: 1px solid #ddd; text-align: left; }
    td { padding: 10px; border: 1px solid #ddd; }
    .status-paid { background-color: #d4edda; color: #155724; font-weight: bold; text-align: center; }
    .status-pending { background-color: #f8d7da; color: #721c24; font-weight: bold; text-align: center; }
    .status-partial { background-color: #fff3cd; color: #856404; font-weight: bold; text-align: center; }
    .footer-totals { font-size: 18px; margin-top: 20px; border-top: 2px solid #333; padding-top: 10px; }
    .footer-totals p { margin: 5px 0; }
</style>

<h2>Reporte de Pagos del Alumno</h2>

<div class="header-info">
    <p><strong>Alumno:</strong> ' . htmlspecialchars($alumno['nombre']) . '</p>
    <p><strong>Ciclo Lectivo:</strong> ' . (int)$anio . ' - ' . ((int)$anio + 1) . '</p>
</div>

<table>
    <thead>
        <tr>
            <th>Mes</th>
            <th style="text-align: right;">Abonado</th>
            <th style="text-align: center;">Estado</th>
        </tr>
    </thead>
    <tbody>';

foreach ($meses as $m => $nombreMes) {
    $paid = (float) ($totals[$m] ?? 0);
    $quota = (float) ($cuotas[$m] ?? 0);
    
    if ($quota <= 0) {
        $statusClass = '';
        $statusText = 'N/A';
    } elseif ($paid >= $quota) {
        $statusClass = 'status-paid';
        $statusText = 'PAGADO';
    } elseif ($paid > 0) {
        $statusClass = 'status-partial';
        $statusText = 'PARCIAL';
    } else {
        $statusClass = 'status-pending';
        $statusText = 'PENDIENTE';
    }

    $html .= '<tr>
                <td>' . $nombreMes . '</td>
                <td style="text-align: right;">$' . number_format($paid, 2) . '</td>
                <td class="' . $statusClass . '">' . $statusText . '</td>
              </tr>';
}

$html .= '
    </tbody>
</table>

<div class="footer-totals">
    <p><strong>Total pagado:</strong> <span style="color: #155724;">$' . number_format((float) $totalPagado, 2) . '</span></p>
    <p><strong>Saldo pendiente:</strong> <span style="color: #721c24;">$' . number_format((float) $saldoPendiente, 2) . '</span></p>
</div>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('reporte-alumno-' . $alumno['id'] . '.pdf', ['Attachment' => false]);
exit;
