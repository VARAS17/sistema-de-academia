<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Pago</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .info-section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table th { bg-color: #f5f5f5; }
        .footer { margin-top: 50px; font-size: 10px; text-align: center; color: #777; }
        .status { font-weight: bold; padding: 5px; color: white; background: #28a745; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BOLETA DE PAGO</h1>
        <p>ACADEMIA PACIFICO - Sistema de Matrícula</p>
    </div>

    <div class="info-section">
        <p><strong>Nro. Operación:</strong> #000{{ $pago->id }}</p>
        <p><strong>Fecha Emisión:</strong> {{ $fecha_emision }}</p>
    </div>

    <table class="table">
        <tr>
            <th colspan="2">DATOS DEL ALUMNO</th>
        </tr>
        <tr>
            <td><strong>Nombre:</strong></td>
            <td>{{ $pago->matricula->alumno->user->name }}</td>
        </tr>
        <tr>
            <td><strong>DNI:</strong></td>
            <td>{{ $pago->matricula->alumno->dni }}</td>
        </tr>
        <tr>
            <td><strong>Carrera:</strong></td>
            <td>{{ $pago->matricula->alumno->carrera->nombre ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="table">
        <tr>
            <th>CONCEPTO</th>
            <th>CICLO</th>
            <th>MONTO</th>
        </tr>
        <tr>
            <!-- Cambiamos $pago->concepto por la variable calculada -->
            <td>{{ $concepto_texto }}</td> 
            <td>{{ $pago->matricula->ciclo->nombre }}</td>
            <td>S/ {{ number_format($pago->monto, 2) }}</td>
        </tr>
    </table>

    <div style="margin-top: 30px; width: 200px;">
        <div class="status">ESTADO: {{ strtoupper($pago->estado) }}</div>
    </div>

    <div class="footer">
        Este documento es un comprobante de operación interna. <br>
        Generado automáticamente por el sistema de Gestión Académica.
    </div>
</body>
</html>