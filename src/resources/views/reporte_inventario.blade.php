<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Inventario</title>
  <style>
    /* Quita márgenes automáticos de la página */
    @page {
      margin: 0;
    }
    /* Repetir el contenido de <thead> en cada salto de página */
    thead {
      display: table-header-group;
    }

    /* Estilos básicos del documento */
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      margin: 0;
      padding: 0;
    }

    /* Tabla principal del reporte */
    .reporte-table {
      width: 90%;              /* Ajusta el ancho de la tabla (90% del ancho de la hoja) */
      margin: 0 auto;          /* Centra la tabla horizontalmente */
      border-collapse: collapse;
    }
    .reporte-table th,
    .reporte-table td {
      border: 1px solid #000;  /* Bordes */
      padding: 5px;
      text-align: center;
    }
    .reporte-table th {
      background-color: #f2f2f2;
    }
    .firmas-table {
        width: 100%;
        margin-top: 30px;
        border-collapse: collapse;
        text-align: center;
        /* Ajusta si necesitas un borde alrededor */
        /* border: 1px solid black; */
    }
    .firmas-table td {
        /* Si no deseas un borde, comenta la siguiente línea */
        /* border: 1px solid black; */
        padding: 10px;
        vertical-align: bottom; /* Hace que la línea de firma quede abajo */
    }
    .firmas-table td:first-child { 
      width: 60%; 
    }
    .firmas-table td:last-child { 
      width: 40%; 
    }
  </style>
</head>
<body>
  <!-- Tabla principal: encabezado y contenido de inventario -->
  <table class="reporte-table">
    <thead>
      <!-- Primera fila: Logo y Título -->
      <tr>
        <td colspan="10" style="padding-bottom: 10px;">
          <!-- Imagen del encabezado -->
          <img src="{{ public_path('header.jpg') }}" alt="Encabezado" style="width: 100%; height: auto;">

          <!-- Título centrado -->
          <h3 style="margin: 5px 0; text-align: center;">REPORTE DE INVENTARIO</h3>

          <!-- Fecha/Hora y Generado por (sin bordes) -->
          <div style="text-align: center;">
            <span style="display: inline-block; margin-right: 20px;">
              <strong>Fecha y Hora:</strong> {{ $fecha }}
            </span>
            <span style="display: inline-block;">
              <strong>Generado por:</strong> 
              {{ $user->primer_nombre }} {{ $user->segundo_nombre }} {{ $user->primer_apellido }} {{ $user->segundo_apellido }}
            </span>
          </div>
        </td>
      </tr>

      <!-- Segunda fila: Nombres de columnas -->
      <tr>
        <th>#</th>
        <th>Clave (Clasificación)</th>
        <th>Nombre/Descripción</th>
        <th>Marca/Autor</th>
        <th>Unidad</th>
        <th>Existencias</th>
        <th>Costo por Unidad</th>
        <th>Sub-total</th>
        <th>IVA (16%)</th>
        <th>Monto Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($inventario as $index => $producto)
        @php
          $subtotal = $producto->cantidad * $producto->precio;
          $iva = $subtotal * 0.16;
          $total = $subtotal + $iva;
        @endphp
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $producto->codigo }}</td>
          <td>{{ $producto->descripcion_producto }}</td>
          <td>{{ $producto->marca }}</td>
          <td>{{ $producto->unidad->tipo_unidad ?? 'No definida' }}</td>
          <td>{{ $producto->cantidad }}</td>
          <td>${{ number_format($producto->precio, 2) }}</td>
          <td>${{ number_format($subtotal, 2) }}</td>
          <td>${{ number_format($iva, 2) }}</td>
          <td>${{ number_format($total, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <!-- Firma -->
  <table class="firmas-table" style="width: 100%; text-align: center;">
    <tr>
        <!-- Espacio para la firma -->
        <td>
            <!-- Deja saltos de línea si deseas un espacio en blanco antes de la línea -->
            <br><br><br><br><br>

            <!-- Línea encima del texto (inline-block) -->
            <span style="
                display: inline-block;
                border-top: 1px solid black; 
                padding-top: 5px;">
                <strong>{{ $user->primer_nombre }} {{ $user->segundo_nombre }} {{ $user->primer_apellido }} {{ $user->segundo_apellido }}</strong>
            </span>
        </td>
    </tr>
    </table>
</body>
</html>
