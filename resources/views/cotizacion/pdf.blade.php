<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color:white;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            width: 100%;
            margin-bottom: 20px;
        }

        .header-section {
            text-align: center;
            font-size: 12px;
            color: #999;
        }

        .logo {
            text-align: left;
        }

        .center {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        .right {
            text-align: right;
        }


        .title {
            font-size: 28px;
            font-weight: bold;
            color: #fe495f;
        }

        .subtitle {
            font-size: 12px;
            font-weight: normal;
            white-space: pre-line; 
            color: #999;
        }

        .section {
            margin: 10px;
        }

        .section-title {
            background-color: #fe495f;
            border-radius: 10px;
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
        }

        .info {
            padding: 8px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #fe495f;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
            background-color: rgb(251, 147, 159);
            color: white;
            padding: 10px;
            text-align: right;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 30px;
            color: #999;
        }

        .footer p {
            margin: 5px 0;
        }

        .observacion-table {
            width: 100%;
            margin-top: 5px;
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .observacion-table td, .observacion-table th {
            padding: 5px;
            text-align: left;
        }

        .message-container {
            background-color: #fff;
            padding: 5px;
            margin-top: 5px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>
<body>

<div class="header">
    <div class="header-section logo">
        <img src="{{ $logo }}" alt="Logo Grobdi" style="height: 30px;">
        <div class="subtitle">RUC:20602806023</div>
    </div>

    <div class="header-section center">
        <div class="title">PROFORMA DR. {{ strtoupper(Str::of($cliente->user->name)->explode(' ')->first()) }}</div>
    </div>

    <div class="header-section right">
        <div>Lima, {{ $fecha }}</div>
    </div>
</div>

<!-- Datos del Doctor -->
<div class="section">
    <div class="section-title">DATOS DEL DOCTOR</div>
    <div class="info">
        <table>
            <tr>
                <td><strong style="color:rgb(244, 89, 107);">Doctor:</strong> {{ $cliente->user->name }} {{ $cliente->user->last_name }}</td>
                <td><strong style="color:rgb(244, 89, 107);">CMP:</strong> {{ $cliente->cmp }}</td>
            </tr>
            <tr>
                <td><strong style="color:rgb(244, 89, 107);">Teléfono:</strong> {{ $cliente->telefono }}</td>
                <td><strong style="color:rgb(244, 89, 107);">Dirección:</strong> {{ $cliente->direccion }}</td>
            </tr>
            <tr>
                <td><strong style="color:rgb(244, 89, 107);">Tipo de entrega:</strong> {{ $cliente->tipo_delivery }}</td>
                <td></td> <!-- Celda vacía para mantener la estructura -->
            </tr>
        </table>

        <!-- Condicional para Observación -->
        @if($observacion)
            <div class="observacion-table">
                <p><strong style="color:rgb(244, 89, 107);">Observación:</strong></p>
                <table>
                    <tr>
                        <td>{{ $observacion }}</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Mensaje al Cliente -->
<div class="section">
    <div class="section-title">MENSAJE DE GROBDI</div>
    <div class="message-container">
        <p>Estimado Doctor,</p>
        <p>Nos es grato dirigirnos a usted para presentarle la información detallada acerca de las formulaciones que ponemos a su disposición.</p>
        <p>A continuación, encontrará un desglose completo de las cantidades, descripciones y precios correspondientes a las formulaciones cotizadas.</p>
    </div>
</div>

<!-- Detalles de la Cotización -->
<div class="section">
    <div class="section-title">DETALLE DE LA PROFORMA</div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Formulación</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['item'] }}</td>
                    <td>{{ $item['nombre'] }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>S/ {{ number_format($item['precio'], 2) }}</td>
                    <td>S/ {{ number_format($item['subtotal'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="total">TOTAL:</td>
                <td>S/ {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    <p><strong>Gracias por su preferencia.</strong></p>
    <p><strong>Nota:</strong> Los precios incluyen el IGV. 
        Entrega: Según coordinación previa.</p>
    <p>© {{ date('Y') }} - Todos los derechos reservados.</p>
    <p style="font-weight: bold; white-space: pre-line;">
        {!! $infoPago !!}
    </p>
</div>

</body>
</html>
