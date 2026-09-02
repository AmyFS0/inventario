<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de movimientos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        .subtitulo { color: #555; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f0f0f0; text-align: left; padding: 6px 8px; border-bottom: 2px solid #ccc; font-size: 10px; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .texto-der { text-align: right; }
        .total { margin-top: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Reporte de movimientos de inventario</h1>
    <p class="subtitulo">Generado el {{ $fecha }}</p>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Ítem</th>
                <th>SKU</th>
                <th class="texto-der">Cantidad</th>
                <th>Área origen</th>
                <th>Área destino</th>
                <th>Usuario</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    <td>{{ $fila['fecha'] }}</td>
                    <td>{{ $fila['tipo'] }}</td>
                    <td>{{ $fila['item'] }}</td>
                    <td>{{ $fila['sku'] }}</td>
                    <td class="texto-der">{{ number_format((float) $fila['cantidad'], 0) }}</td>
                    <td>{{ $fila['area_origen'] }}</td>
                    <td>{{ $fila['area_destino'] }}</td>
                    <td>{{ $fila['usuario'] }}</td>
                    <td>{{ $fila['motivo'] }}</td>
                </tr>
            @empty
                <tr><td colspan="9">Sin registros.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="total">Total de registros: {{ count($filas) }}</p>
</body>
</html>