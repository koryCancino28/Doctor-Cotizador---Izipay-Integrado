@extends('adminlte::page')

@section('title', 'Doctor Proforma')

@section('content_header')
<div></div>
@stop

@section('content')
<div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    @include('messages')
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Proforma del Doctor
        <i class="fas fa-user-md"></i>
    </h1>
    <!-- Tabla de cotizaciones -->
    <div class="table-responsive mt-3">
        <table id="clientes-table" class="table table-striped table-bordered shadow-sm rounded">
            <thead style="background-color: #fe495f; color: white;">
                <tr>
                    <th>Doctor</th>
                    <th>Total</th>
                    <th>Tipo de pago</th>
                    <th>Proforma</th> <!-- Columna para el botón de descarga -->
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    @foreach ($cliente->cotizaciones as $cotizacion)
                        <tr class="cotizacion-row" data-cliente-id="{{ $cliente->id }}">
                            <td style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $cliente->nombre }}</td>
                            <td>S/ {{ number_format($cotizacion->total, 2) }}</td>
                            <td>{{ ucfirst($cotizacion->tipo_pago) }}</td>
                            <td>
                                @php
                                    $pdfFilename = $cotizacion->pdf_filename;
                                    $pdfPath = public_path('pdf/' . $pdfFilename);
                                @endphp
                                @if($pdfFilename && file_exists($pdfPath))
                                    <a href="{{ asset('pdf/' . $pdfFilename) }}"
                                    class="btn btn-s btn-sm"
                                    target="_blank">
                                        <i class="fas fa-download"></i> Descargar PDF
                                    </a>
                                @else
                                    <button class="btn btn-warning btn-sm" disabled>PDF no disponible</button>
                                    @if($pdfFilename)
                                        <small class="text-muted">(Archivo no encontrado)</small>
                                    @endif
                                @endif
                                @php
                                    $esTransferencia = $cotizacion->tipo_pago === 'transferencia';
                                    $esContraEntrega = $cotizacion->tipo_pago === 'contra_entrega';
                                    $sinVoucher = empty($cotizacion->voucher); // Asumiendo campo 'voucher' o similar
                                    $confirmacion = $cotizacion->confirmacion;
                                    $yaConfirmado = $confirmacion && $confirmacion->archivo;
                                @endphp
                                @if (($esTransferencia && $sinVoucher) || $esContraEntrega)
                                    <button class="btn btn-sm"
                                            data-toggle="modal"
                                            data-target="#confirmacionModal"
                                            data-cotizacion="{{ $cotizacion->id }}"
                                            data-total="{{ number_format($cotizacion->total, 2) }}"
                                            data-tipo_pago="{{ ucfirst($cotizacion->tipo_pago) }}"
                                            data-archivo="{{ $confirmacion->archivo ?? '' }}"
                                            data-tipo_archivo="{{ $confirmacion->tipo_archivo ?? '' }}"  
                                            data-doctor="{{ $cliente->nombre }}"
                                            style="{{ $yaConfirmado ? 'background-color:rgb(194, 255, 124); color: rgb(101, 149, 99); border-color: aquamarine;' : 'background-color:#cce5ff; color:#004085;'  }}">
                                        <i class="fas {{ $yaConfirmado ? 'fa-check-circle' : 'fa-dollar-sign' }} mr-1"></i>
                                        {{ $yaConfirmado ? 'Pago Registrado' : 'Confirmar Pago' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
    <!-- Modal -->
    <div class="modal fade" id="confirmacionModal" tabindex="-1" role="dialog" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="confirmacionForm" method="POST" action="{{ route('confirmacion.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="cotizacion_id" id="cotizacion_id_modal">
        <div class="modal-content shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background-color: #fe495f; color: white;">
            <h5 class="modal-title font-weight-bold">
                <i class="fas fa-file-upload mr-2"></i> Subir Confirmación de Pago
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body px-4 py-3">
                <!-- Info -->
                <div class="row mb-3">
                    <div class="col-md-6" style="overflow-wrap: break-word; white-space: normal;">
                    <p class="mb-1">
                        <strong class="text-danger">
                        <i class="fas fa-user-md mr-1"></i> Doctor:
                        </strong>
                        <span id="modal-doctor-name-text" class="text-dark"></span>
                    </p>
                    </div>
                    <div class="col-md-6">
                    <p class="mb-1">
                        <strong class="text-danger">
                        <i class="fas fa-dollar-sign mr-1"></i> Total:
                        </strong>
                        S/ <span id="modal-total-text" class="text-dark"></span>
                    </p>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="mb-1">
                    <strong class="text-danger">
                        <i class="fas fa-credit-card mr-1 text-info"></i> Medio de Pago:
                    </strong>
                    <span id="modal-tipo-pago-text" class="text-dark"></span>
                    </p>
                </div>

                <!-- Archivo existente -->
                <div id="archivo-preview" class="mb-3" style="display: none;">
                    <p class="mb-2 text-danger font-weight-bold">
                    <i class="fas fa-file-alt mr-1"></i> Archivo existente:
                    </p>
                    <div id="archivo-preview-content" class="text-center"></div>
                </div>

                <!-- Subir archivo -->
                <div class="input-group justify-content-center" id="archivo-input-group">
                    <label class="input-group-text bg-white border border-1 rounded-pill px-4 py-2" for="voucher" style="cursor: pointer; color: #fe495f; font-weight: bold;">
                        <i class="fas fa-cloud-upload-alt mr-2 text-secondary"></i> Elegir archivo
                    </label>
                    <input type="file" name="archivo" class="d-none" id="voucher" accept="image/*,application/pdf">
                </div> 

                <div class="input-group justify-content-center mt-2"  id="archivo-input-group2">
                    <small class="text-muted">
                        Archivos permitidos: JPG, PNG y PDF. (máx. 5 MB).
                    </small>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-between px-4 py-3" style="background-color: #fef2f4;">
            <button type="submit" class="btn btn-info" id="btn-enviar-confirmacion">
                <i class="fas fa-paper-plane mr-1"></i> Enviar
            </button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                <i class="fas fa-times-circle mr-1"></i> Cancelar
            </button>
            </div>
        </div>
        </form>
    </div>
    </div>
@stop

@section('css')
    <style>
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table-bordered {
            border-color: #fe495f;
        }
        .table thead th {
            background-color: #fe495f;
            color: white;
            font-size: 1.1rem;
        }

        .btn {
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 10px;
            font-weight: bold;
            white-space: nowrap;
        }

        .btn-s{
            background-color: #fe495f;
            color: white;"
            border: none;
        }
        
        .btn:hover {
            transform: scale(1.05); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.2s ease; /* Transición suave */
        }

        .container {
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(212, 206, 206, 0.86);
        }

        /* Estilos personalizados para la tabla DataTables */
        table#clientes-table {
            background-color:rgb(255, 200, 200);
        }

        table#clientes-table th {
            background-color: #fe495f;
            color: white;
        }

        table#clientes-table td {
            background-color: rgb(255, 249, 249);
        }

        table#clientes-table thead th {
            text-align: center;
            font-weight: bold;
        }

        table#clientes-table tfoot td {
            font-weight: bold;
        }

    </style>
@stop

@section('js')
<script>
        $(document).ready(function() {
        $('#clientes-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
            },
            responsive: true,
            ordering: false,
            pageLength: 10,
            lengthChange: false,
            info: false
        });
        $('.dataTables_filter').addClass('mb-3 mt-3');
    });

  document.addEventListener('DOMContentLoaded', function () {
        $('#confirmacionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var btnEnviar = $('#btn-enviar-confirmacion');
            var cotizacionId = button.data('cotizacion');
            var total = button.data('total');
            var tipoPago = button.data('tipo_pago');
            var archivo = button.data('archivo');
            var tipoArchivo = button.data('tipo_archivo');
            var doctorName = button.data('doctor');
            $('#cotizacion_id_modal').val(cotizacionId);
            $('#modal-doctor-name-text').text(doctorName);
            $('#modal-cotizacion-id-text').text(cotizacionId);
            $('#modal-total-text').text(total);
            $('#modal-tipo-pago-text').text(tipoPago);

            var previewWrapper = $('#archivo-preview');
            var previewContent = $('#archivo-preview-content');
            var archivoInputGroup = $('#archivo-input-group');
            var archivoInputGroup2 = $('#archivo-input-group2');


            if (archivo) {
                previewWrapper.show();
                archivoInputGroup.hide(); // Ocultar input
                archivoInputGroup2.hide(); // Ocultar input
                btnEnviar.hide();
                let filePath = '/' + archivo;

                if (tipoArchivo === 'imagen') {
                    previewContent.html(`
                        <img src="${filePath}" alt="Imagen" style="max-width: 45%; border-radius: 8px; display: block; margin: 0 auto;" />
                    `);
                } else if (tipoArchivo === 'pdf') {
                    previewContent.html(`
                        <a href="${filePath}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
                        </a>
                    `);
                } else {
                    previewContent.html(`<span class="text-muted">Archivo subido (formato desconocido)</span>`);
                }
            } else {
                previewWrapper.hide();
                previewContent.empty();
                archivoInputGroup.show();
                archivoInputGroup2.show();
                btnEnviar.show();
            }
        });
    });
    </script>
@stop
@section('plugins.Datatables', true)
