@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<h1></h1>
@stop

@section('content')
<div class="container" style="background-color:rgb(255, 255, 255); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(254,73,95,0.2);">
<h1 style="color: #fe495f;text-align: center;"><strong>Mis Formulaciones</strong></h1>
    @unless(auth()->user()->cliente)
        <p class="text-danger">No tienes formulaciones registradas o no estás logueado como Doctor.</p>
    @else
    <form id="cotizacion-form" action="{{ route('cotizacion.store') }}" method="POST">
        @csrf
        
        <!-- Sección de Datos de Envío -->
        <div class="card mb-4" style="border-color: #fe495f;">
            <div class="card-header text-white" style="background-color: #fe495f;">
                <h3 class="card-title">Datos de Envío</h3>
            </div>
            <div class="card-body" style="background-color: rgb(255, 255, 255);">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono" style="color: #fe495f;">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                value="{{ old('telefono', auth()->user()->cliente->telefono) }}" required
                                pattern="[0-9]+" maxlength="15" 
                                style="border-color: #fe495f;"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_delivery" style="color: #fe495f;">Tipo de Delivery</label>
                            <select class="form-control" id="tipo_delivery" name="tipo_delivery" required
                                    style="border-color: #fe495f;">
                                <option value="">Seleccione una opción</option>
                                <option value="Recojo en tienda" {{ old('tipo_delivery', auth()->user()->cliente->tipo_delivery) == 'Recojo en tienda' ? 'selected' : '' }}>
                                    Recojo en tienda
                                </option>
                                <option value="Entrega a domicilio" {{ old('tipo_delivery', auth()->user()->cliente->tipo_delivery) == 'Entrega a domicilio' ? 'selected' : '' }} >
                                    Entrega a domicilio
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="direccion" style="color: #fe495f;">Dirección (para envío a domicilio)</label>
                    <textarea class="form-control" id="direccion" name="direccion" rows="3" required
                              style="border-color: #fe495f;">{{ old('direccion', auth()->user()->cliente->direccion) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="observacion" style="color: #fe495f;">Observación</label>
                    <textarea class="form-control" id="observacion" name="observacion" rows="3" 
                            style="border-color: #fe495f;" placeholder="Indique si tiene un color o sabor de preferencia, y si desea con azúcar o sin azúcar.">{{ old('observacion') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Tabla de Formulaciones -->
        <table class="table table-bordered table-centered" style="background-color: rgb(255, 255, 255); border-color: #fe495f;">
            <thead>
                <tr style="background-color: #fe495f; color: white;">
                    <th>Nombre</th>
                    <th>Precio Unidad <br>(De 7 a menos)</th>
                    <th>Precio Unidad <br>(De 8 a más)</th>
                    <th>Cantidad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            @foreach($formulaciones as $f)

            <tr data-id="{{ $f->id }}" data-nombre="{{ $f->name }}" data-precio="{{ $f->precio_publico }}"
                data-precio-medico="{{ $f->precio_medico }}" style="background-color: rgb(255, 255, 255);">
                <td>{{ $f->name }}</td>
                <td>S/ {{ number_format($f->precio_publico, 2) }}</td>
                <td>S/ {{ number_format($f->precio_medico, 2) }}</td>
                <td><input type="number" class="form-control cantidad-input" value="1" min="1" style="width:80px; border-color: #fe495f; text-align: center; display: block; margin: 0 auto;"></td>
                <td><button type="button" class="btn btn-primary btn-sm btn-agregar" style="background-color: #767c94; border: none;">Agregar</button></td>
            </tr>
            @endforeach

            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {!! $formulaciones->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
        </div> 
        <button type="submit" class="btn btn-success" style="background-color: #767c94; border: none;">Guardar Cotización</button>
    
    </form>

    <h3 class="mt-4" style="color: #fe495f;">Resumen</h3>
    <table class="table table-striped table-centered" style="background-color: rgb(255, 255, 241); border-color: #fe495f;">
        <thead>
            <tr style="background-color: #fe495f; color: white;">
                <th>Formulación</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
                <th style="width: 100px; text-align: center;">Acción</th>
            </tr>
        </thead>
        <tbody id="resumen-body"></tbody>
        <tfoot>
            <tr style="background-color: #fe9d97; color: white;">
                <th colspan="3">Total</th>
                <th id="total">S/ 0.00</th>
            </tr>
        </tfoot>
    </table>
    @endunless
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .btn-agregar:hover {
        background-color: #5d5f7d !important;
    }
    
    .btn-success:hover {
        background-color: #5d5f7d !important;
    }
    
    .form-control:focus {
        border-color: #fe495f;
        box-shadow: 0 0 0 0.2rem rgba(254, 73, 95, 0.25);
    }
    
    .card {
        box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);
    }
    /*centra celdas de formulaciones*/
    .table-centered th,
    .table-centered td {
        text-align: center;
        vertical-align: middle;
    }
    /* Asegurar que el texto largo en la columna "Nombre" se ajuste y no se desborde */
    .table-centered td:nth-child(1) {
        word-wrap: break-word;          
        overflow-wrap: break-word;      
        white-space: normal;            
        max-width: 200px;               
        word-break: break-word;   /* se dividen las palabras largas */
    }

</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    const cotizador = {};  // Objeto que almacenará las formulaciones seleccionadas
    const $form = $('#cotizacion-form');
    
    // Inicialización de SweetAlert2 con los botones personalizados
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });
    
    // Función para actualizar el resumen de la cotización
    const actualizarResumen = () => {
        let html = '', total = 0;
        
     const observacion = $('#observacion').val().trim();
    // Guarda observación junto con el cotizador
    localStorage.setItem('cotizadorData', JSON.stringify({
    items: cotizador, observacion: observacion}));

        $.each(cotizador, (id, item) => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;
            html += `<tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>S/ ${item.precio.toFixed(2)}</td>
                <td>S/ ${subtotal.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${id}" style="border: none">Eliminar</button></td>
            </tr>`;
        });
        
        $('#resumen-body').html(html);
        $('#total').text(`S/ ${total.toFixed(2)}`);
    };

    // Manejo del botón "+" para agregar el producto
    $('.btn-agregar').click(function() {
        const $row = $(this).closest('tr');
        const cantidad = parseInt($row.find('.cantidad-input').val()) || 0;
        const precioPublico = parseFloat($row.data('precio'));
        const precioMedico = parseFloat($row.data('precioMedico')); // Obtener el precio médico
        const idProducto = $row.data('id');

        // Verificar si ya existe el producto en el carrito
        if (cotizador[idProducto]) {
            // Actualizar cantidad total (acumulada)
            cotizador[idProducto].cantidad += cantidad;

            // Si la cantidad total llega a 8 o más, usar el precio médico
            if (cotizador[idProducto].cantidad >= 8) {
                cotizador[idProducto].precio = precioMedico;
            } else {
                cotizador[idProducto].precio = precioPublico;
            }
        } else {
            // Si no existe, agregar el producto con la cantidad inicial
            cotizador[idProducto] = {
                id: idProducto,
                nombre: $row.data('nombre'),
                precio: cantidad >= 8 ? precioMedico : precioPublico, // Lógica para asignar el precio
                cantidad: cantidad
            };
        }
        
        // Actualizar el resumen después de agregar o modificar el producto
        actualizarResumen();
    });

    const savedData = localStorage.getItem('cotizadorData');
    if (savedData) {
        try {
            const parsedData = JSON.parse(savedData);
            if (parsedData.items) {
                Object.assign(cotizador, parsedData.items);
            }
            if (parsedData.observacion) {
                $('#observacion').val(parsedData.observacion);
            }
            actualizarResumen();
        } catch (e) {
            console.error('Error al leer datos del localStorage');
        }
    }
    // Manejo del botón de eliminar
    $(document).on('click', '.btn-eliminar', function() {
        const idProducto = $(this).data('id');
        
        // Eliminar el producto del carrito
        delete cotizador[idProducto];
        
        // Actualizar el resumen después de eliminar el producto
        actualizarResumen();
    });

    // Enviar formulario al servidor
    $form.submit(function(e) {
        e.preventDefault();

        swalWithBootstrapButtons.fire({
            title: "¿Seguro que quieres guardar esta cotización?",
            text: "¡No podrás revertir esta acción!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, guardar",
            cancelButtonText: "No, cancelar",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const telefono = $('#telefono').val().trim();
                const tipoDelivery = $('#tipo_delivery').val();
                const direccion = $('#direccion').val().trim();
                const observacion = $('#observacion').val().trim();

                if (!telefono || !tipoDelivery || !direccion) {
                    swalWithBootstrapButtons.fire({
                        title: "Error",
                        text: "Por favor complete todos los datos de envío.",
                        icon: "error"
                    });
                    return;
                }

                $.post($form.attr('action'), {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    items: Object.values(cotizador),
                    telefono: telefono,
                    tipo_delivery: tipoDelivery,
                    direccion: direccion,
                    observacion: observacion 
                }).done(res => {
                    localStorage.removeItem('cotizadorData');

                    swalWithBootstrapButtons.fire({
                        title: "¡Guardado!",
                        text: "¡Cotización guardada exitosamente!",
                        icon: "success"
                    }).then(() => {
                        // URL del PDF generado en el backend
                        const pdfUrl = res.pdf_url;
                        
                        // Crear un enlace para forzar la descarga
                        const link = document.createElement('a');
                        link.href = pdfUrl;
                        link.download = 'Cotizacion Grobdi.pdf';  // Nombre que se dará al archivo descargado
                        link.click();  // Simula el clic en el enlace para descargar el archivo

                        // Limpiar el cotizador
                        Object.keys(cotizador).forEach(k => delete cotizador[k]);
                        actualizarResumen();
                    });
                    
                }).fail(err => {
                    swalWithBootstrapButtons.fire({
                        title: "Error",
                        text: err.responseJSON?.message || 'Error al guardar',
                        icon: "error"
                    });
                });
            }
        });
    });

    });

</script>
@stop