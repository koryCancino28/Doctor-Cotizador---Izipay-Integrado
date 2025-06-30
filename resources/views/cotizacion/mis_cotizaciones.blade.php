@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
@stop

@section('content')
<div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Mis Cotizaciones <i class="fa-solid fa-hand-holding-dollar"></i></h1>

    @if($cotizaciones->count() > 0)
        <table class="table table-bordered table-responsive" id="table_cotizacion">
            <thead>
                <tr>
                    <th>Fecha <i class="fa-solid fa-calendar-days"></i></th>
                    <th>Formulaciones <i class="fa-solid fa-atom"></i></th>
                    <th>Total <i class="fa-solid fa-money-check-dollar"></i></th>
                    <th>PDF <i class="fa-solid fa-file-lines"></i></th>
                    <th>Vista Previa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cotizaciones as $cotizacion)
                    @php
                        $fecha = $cotizacion->created_at->format('d/m/Y H:i');
                        $formulaciones = $cotizacion->detalles
                            ->map(fn($d) => $d->formulacion->name)
                            ->unique()
                            ->implode(', ');
                    @endphp
                    <tr>
                        <td>{{ $fecha }}</td>
                        <td class="observaciones">{{ $formulaciones }}</td>
                        <td>S/ {{ number_format($cotizacion->total, 2) }}</td>
                        <td>
                            <a class="btn btn_crear" href="{{ asset('pdf/' . $cotizacion->pdf_filename) }}" target="_blank" download>
                                <i class="fa-solid fa-download"></i> Descargar PDF
                            </a>
                        </td>
                        <td>
                            <button class="btn btn-sm" data-pdf="{{ asset('pdf/' . $cotizacion->pdf_filename) }}" onclick="openPdfPreview(this)">
                                <i class="fa-solid fa-eye"></i> Ver
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No tienes cotizaciones realizadas.</p>
    @endif
</div>

<!-- Modal PDF Estilizado -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 0 6px rgba(243, 163, 163, 0.73);">
      <div class="modal-header" style="background-color: #fe495f; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
        <h5 class="modal-title" id="pdfPreviewModalLabel" style="font-weight: bold;"><i class="fa-solid fa-file"></i> Vista Previa de Cotización</h5>
      </div>
      <div class="modal-body p-0" style="background-color: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
        <iframe 
            id="pdfPreviewIframe" 
            src="" 
            frameborder="0" 
            style="width: 100%; height: 80vh; border-radius: 0 0 12px 12px;">
        </iframe>
      </div>
    </div>
  </div>
</div>

<style>
    table td.observaciones {
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .btn_crear {
        border: 1px solid #fe495f !important;
        background-color: rgb(255, 113, 130);
        color: white;
        margin: 0 auto;
    }
    .btn-sm {
        border: 1px solid #fe495f !important;
        background-color: rgba(251, 209, 209, 0.81);
        color: darkred;
        margin: 0 auto;
    }
    .btn:hover {
        transform: scale(1.07);
        transition: transform 0.6s ease;
    }
    .btn_crear i {
        margin-right: 4px;
    }
    .w {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
    table thead th {
        background-color: #fe495f;
        color: white;
    }
    table tbody td {
        background-color: rgb(255, 249, 249);
    }
    .table-bordered {
        border-color: #fe495f;
    }
    table th, table td {
        text-align: center;
    }
    td {
        width: 1%;
        white-space: nowrap;
    }
</style>
@stop

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    function openPdfPreview(button) {
        const pdfUrl = button.getAttribute('data-pdf');
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        if (isMobile) {
            // Abrir directamente
            window.open(pdfUrl, '_blank');
        } else {
        const pdfUrl = button.getAttribute('data-pdf');
            document.getElementById('pdfPreviewIframe').src = pdfUrl;
            new bootstrap.Modal(document.getElementById('pdfPreviewModal')).show();
        }
    }

    $(document).ready(function() {
        $('#table_cotizacion').DataTable({
            "ordering": false, // Deshabilitar el ordenamiento por columna
            "searching": true, // Habilitar la barra de búsqueda
            "paging": true,    // Habilitar la paginación
            "info": true,      // Habilitar la información de la tabla
            "lengthChange": false,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" // Cargar idioma español
            }
        });
    });
</script>
@stop
@section('plugins.Datatables', true)
