@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
@stop

@section('content')
<div class="container mt-2" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;"><i class="fa-solid fa-file-lines"></i>
    Mis Cotizaciones</h1>

    @if($cotizaciones->count() > 0)
        <table class="table table-bordered" id="table_cotizacion">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Formulaciones</th>
                    <th>Total</th>
                    <th>PDF</th>
                    <th>Vista Previa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cotizaciones as $pdf => $items)
                    @php
                        $fecha = $items->first()->created_at->format('d/m/Y H:i');
                        $total = $items->sum('total');
                        $formulaciones = $items->pluck('formulacion.name')->filter()->unique()->join(', ');
                    @endphp
                    <tr>
                        <td>{{ $fecha }}</td>
                        <td class="observaciones">{{ $formulaciones }}</td>
                        <td>S/ {{ number_format($total, 2) }}</td>
                        <td>
                            <a class="btn btn_crear" href="{{ asset('storage/pdf/' . $pdf) }}" target="_blank" download><i class="fa-solid fa-download"></i>
                            Descargar PDF</a>
                        </td>
                        <td>
                            <button class="btn btn-sm" data-pdf="{{ asset('storage/pdf/' . $pdf) }}" onclick="openPdfPreview(this)"><i class="fa-solid fa-eye"></i>
                            Ver</button>
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
        <h5 class="modal-title" id="pdfPreviewModalLabel" style="font-weight: bold;"><i class="fa-solid fa-file"></i>
         Vista Previa de Cotización</h5>
      </div>
      <div class="modal-body p-0" style="background-color: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
        <iframe id="pdfPreviewIframe" src="" frameborder="0" style="width: 100%; height: 80vh; border-radius: 0 0 12px 12px;"></iframe>
      </div>
    </div>
  </div>
</div>


<style>
        table td.observaciones {
            max-width: 100px; 
            overflow: hidden; 
            text-overflow: ellipsis; /* Muestra '...' */
            white-space: nowrap; 
        }
        .btn_crear {
            border: 1px solid#fe495f !important;
            background-color:rgb(255, 113, 130); 
            color: white;
            margin: 0 auto;
        }
        .btn-sm {
            border: 1px solid#fe495f !important;
            background-color:rgba(251, 209, 209, 0.81); 
            color: darkred;
            margin: 0 auto;
        }
        .btn:hover {
            transform: scale(1.07);
            transition: transform 0.6s ease;
        }

        .btn_crear i {
            margin-right: 4px; /* Espaciado entre el icono y el texto */
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

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
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
            document.getElementById('pdfPreviewIframe').src = pdfUrl;
            new bootstrap.Modal(document.getElementById('pdfPreviewModal')).show();
        }
         $(document).ready(function() {
            $('#table_cotizacion').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
                    },
                    ordering: false,
                    responsive: true,
                    // quitamos "l" del DOM para eliminar el selector de cantidad de registros
                    dom: '<"row"<"col-sm-12 col-md-12"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    pageLength: 10,
                    initComplete: function() {
                        $('.dataTables_filter')
                            .addClass('mb-2')
                            .find('input')
                            .attr('placeholder', 'Buscar datos en la tabla') 
                            .end()
                            .find('label')
                            .contents().filter(function() {
                                return this.nodeType === 3;
                            }).remove()
                            .end()
                            .prepend('Buscar:');
                    }
                });
            });
    </script>
@stop
