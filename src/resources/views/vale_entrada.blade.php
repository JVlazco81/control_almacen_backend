<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vale de Entrada de Almacén</title>
    <style>
        /* -----------------------
        ESTILOS BÁSICOS
        ------------------------ */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            margin-left: -20px; /* Mueve el contenedor 20px hacia la izquierda */
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100%;
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
        }

        /* -----------------------
        TABLA DE INFORMACIÓN
        ------------------------ */
        .info-table,
        .proveedor-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Aplica negrita a la primera celda (texto fijo) de cada fila */
        .info-table td:first-child,
        .proveedor-table td:first-child {
            font-weight: bold;
        }

        /* Anchos específicos para la info-table */
        .info-table tr td:nth-child(1),
        .info-table tr td:nth-child(3) {
            width: 15%;
            white-space: nowrap; /* Evita quiebres de línea */
        }
        .info-table tr td:nth-child(2),
        .info-table tr td:nth-child(4) {
            width: 35%;
            text-align: center;
            white-space: nowrap;
        }
        .info-table td {
            /* 10px arriba y abajo, 5px izquierda y derecha */
            padding: 10px 10px;
        }

        /* -----------------------
        TABLA DE PROVEEDOR
        ------------------------ */
        .proveedor-table td {
            padding: 5px;
            text-align: center;
            border: none;
        }

        /* -----------------------
        TABLA DE ARTÍCULOS
        ------------------------ */
        .articulos-table {
            width: 100%;
            border-collapse: collapse; /* Importante para controlar bordes compartidos */
        }

        /* Encabezado: borde completo para la fila de columnas */
        .articulos-table thead tr th {
            border: 1px solid black;
            padding: 5px;
            text-align: center; /* Cabecera centrada */
        }

        /* Cuerpo: solo líneas verticales (izquierda y derecha), 
        sin líneas horizontales entre filas */
        .articulos-table tbody tr td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: 5px;
            text-align: center; /* Ajusta según tu preferencia (izquierda, derecha, etc.) */
        }

        /* Primera fila del tbody: borde superior */
        .articulos-table tbody tr:first-child td {
            border-top: 1px solid black;
        }

        /* Última fila del tbody: borde inferior */
        .articulos-table tbody tr:last-child td {
            border-bottom: 1px solid black;
        }

        /* Para alinear el contenido de la primera columna (ARTÍCULO) a la izquierda */
        .articulos-table td:first-child {
            width: 55%;
            text-align: left;
        }
        /* Alinea a la derecha el contenido de las columnas PRECIO y TOTAL en el tbody de la tabla de artículos */
        .articulos-table tbody td:nth-child(3),
        .articulos-table tbody td:nth-child(4) {
            text-align: right;
        }
        /* -----------------------
        TABLA DE TOTALES
        ------------------------ */
        .total-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        /* Alinea verticalmente arriba las celdas (NOTA, etc.) */
        .total-table td {
            vertical-align: top;
        }
        /* Ajusta el ancho y centra la 2a y 3a columna */
        .total-table tr td:nth-child(2),
        .total-table tr td:nth-child(3) {
            width: 12%;
            text-align: center;
        }
        /* Clase para forzar el centrado cuando sea necesario */
        .force-center {
            text-align: center !important;
        }

        .total-table td:last-child {
            text-align: right !important;
        }

        /* -----------------------
        TABLA DE FIRMAS
        ------------------------ */
        .firmas-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .firmas-table td {
            border: 1px solid black;
            text-align: center;
        }
        /* Ancho para la primera y última columna (ej. 45% cada una) */
        .firmas-table td:first-child,
        .firmas-table td:last-child {
            width: 45%;
        }
        /* Quita líneas horizontales internas, manteniendo el borde exterior */
        .firmas-table tr:nth-child(1) td {
            border-bottom: none !important;
        }
        .firmas-table tr:nth-child(2) td {
            border-top: none !important;
            border-bottom: none !important;
        }
        .firmas-table tr:nth-child(3) td {
            border-top: none !important;
        }
        /* Clases para remover bordes superior/inferior si se requiere */
        .force-erase-top {
            border-top: none !important;
        }
        .force-erase-bottom {
            border-bottom: none !important;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Cabecera -->
    <div class="header">
        <img src="{{ public_path('header.jpg') }}" alt="Encabezado">
    </div>

    <!-- Título principal -->
    <h3 class="titulo">VALE DE ENTRADA DE ALMACÉN</h3>

    <!-- Información General -->
    <table class="info-table">
        <tr>
            <td><strong>FOLIO:</strong></td>
            <td>{{ $entrada->folio }}</td>
            <td><strong>ENTRADA N°:</strong></td>
            <td>{{ $entrada->entrada_anual }}</td>
        </tr>
        <tr>
            <td><strong>FECHA DE FACTURA:</strong></td>
            <td>{{ date('d/m/Y', strtotime($entrada->fecha_factura)) }}</td>
            <td><strong>FECHA ENTRADA:</strong></td>
            <td>{{ date('d/m/Y', strtotime($entrada->fecha_entrada)) }}</td>
        </tr>
    </table>

    <br>

    <!-- Tabla Proveedor-->
    <table class="proveedor-table">
        <tr>
            <td><strong>PROVEEDOR:</strong></td>
            <td>{{ $entrada->proveedor->nombre_proveedor ?? 'Desconocido' }}</td>
        </tr>
    </table>

    <br>

    <!-- Tabla Articulos-->
    <table class="articulos-table">
        <thead>
            <tr>
                <th>ARTÍCULO</th>
                <th>CANTIDAD</th>
                <th>PRECIO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entrada->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->codigo }} - {{ $detalle->producto->descripcion_producto }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>$ {{ number_format($detalle->producto->precio, 2) }}</td>
                <td>$ {{ number_format($detalle->cantidad * $detalle->producto->precio, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Nota y Totales -->
    <table class="total-table">
        <tr>
            <td><strong>NOTA:</strong></td>
            <td><strong>SUB-TOTAL</strong></td>
            <td>$ {{ number_format($entrada->detalles->sum(fn($d) => $d->cantidad * $d->producto->precio), 2) }}</td>
        </tr>
        <tr>
            <td rowspan="2">{{ $entrada->nota }}</td>
            <td><strong>IVA 16%</strong></td>
            <td>$ {{ number_format($entrada->detalles->sum(fn($d) => $d->cantidad * $d->producto->precio) * 0.16, 2) }}</td>
        </tr>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td>$ {{ number_format($entrada->detalles->sum(fn($d) => $d->cantidad * $d->producto->precio) * 1.16, 2) }}</td>
        </tr>
    </table>

        <!-- Firmas -->
    <table class="firmas-table">
            <tr>
                <td><strong>ENTREGA</strong></td>
                <td class="force-erase-top"></td>
                <td><strong>RECIBE</strong></td>
            </tr>
            <tr>
                <td><br><br><br><br><br></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>{{ $entrada->proveedor->nombre_proveedor ?? 'Desconocido' }}</strong></td>
                <td class="force-erase-bottom"></td>
                <td><strong>C.P. Claudio Alfonso Alamilla Magaña</strong></td><!--RECIBE-->
            </tr>
        </table>
    </div>

</body>
</html>