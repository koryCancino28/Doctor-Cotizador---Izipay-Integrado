@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<h1></h1>
@stop

@section('content')
<div class="container" style="background-color:rgb(255, 255, 255); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(254,73,95,0.2);">
<h1 style="color: #fe495f;">Mis Formulaciones</h1>
    @unless(auth()->user()->cliente)
        <p class="text-danger">No tienes formulaciones registradas o no estás logueado como cliente.</p>
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
                            <input type="text" class="form-control" id="telefono" name="telefono" 
                                   value="{{ old('telefono', auth()->user()->cliente->telefono) }}" required
                                   style="border-color: #fe495f;">
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
                                <option value="Entrega a domicilio" {{ old('tipo_delivery', auth()->user()->cliente->tipo_delivery) == 'Entrega a domicilio' ? 'selected' : '' }}>
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
            </div>
        </div>

        <!-- Tabla de Formulaciones -->
        <table class="table table-bordered" style="background-color: rgb(255, 255, 255); border-color: #fe495f;">
            <thead>
                <tr style="background-color: #fe495f; color: white;">
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach(auth()->user()->cliente->formulaciones as $f)
                <tr data-id="{{ $f->id }}" data-nombre="{{ $f->name }}" data-precio="{{ $f->precio_publico }}"
                    style="background-color: rgb(255, 255, 255);">
                    <td>{{ $f->name }}</td>
                    <td>S/ {{ number_format($f->precio_publico, 2) }}</td>
                    <td><input type="number" class="form-control cantidad-input" value="1" min="1" style="width:80px; border-color: #fe495f;"></td>
                    <td><button type="button" class="btn btn-primary btn-sm btn-agregar" style="background-color: #767c94; border: none;">+</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success" style="background-color: #767c94; border: none;">Guardar Cotización</button>
    </form>

    <h3 class="mt-4" style="color: #fe495f;">Resumen</h3>
    <table class="table table-striped" style="background-color: rgb(255, 255, 241); border-color: #fe495f;">
        <thead>
            <tr style="background-color: #fe495f; color: white;">
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
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
</style>
@stop

@section('js')
<script>
$(function() {
    const cotizador = {};
    const $form = $('#cotizacion-form');
    
    const actualizarResumen = () => {
        let html = '', total = 0;
        
        $.each(cotizador, (id, item) => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;
            html += `<tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>S/ ${item.precio.toFixed(2)}</td>
                <td>S/ ${subtotal.toFixed(2)}</td>
            </tr>`;
        });
        
        $('#resumen-body').html(html);
        $('#total').text(`S/ ${total.toFixed(2)}`);
    };

    $('.btn-agregar').click(function() {
        const $row = $(this).closest('tr');
        const item = {
            id: $row.data('id'),
            nombre: $row.data('nombre'),
            precio: parseFloat($row.data('precio')),
            cantidad: parseInt($row.find('.cantidad-input').val()) || 0
        };
        
        cotizador[item.id] = cotizador[item.id] || {...item, cantidad: 0};
        cotizador[item.id].cantidad += item.cantidad;
        actualizarResumen();
    });

    $form.submit(function(e) {
        e.preventDefault();
        
        // Validar campos de envío
        const telefono = $('#telefono').val().trim();
        const tipoDelivery = $('#tipo_delivery').val();
        const direccion = $('#direccion').val().trim();
        
        if(!telefono || !tipoDelivery || !direccion) {
            alert('Por favor complete todos los datos de envío');
            return;
        }
        
        $.post($(this).attr('action'), {
            _token: $('meta[name="csrf-token"]').attr('content'),
            items: Object.values(cotizador),
            telefono: telefono,
            tipo_delivery: tipoDelivery,
            direccion: direccion
        }).done(res => {
            alert(res.message);
            Object.keys(cotizador).forEach(k => delete cotizador[k]);
            actualizarResumen();
            // Opcional: Recargar la página para ver los cambios en los datos del cliente
            window.location.reload();
        }).fail(err => {
            alert(err.responseJSON?.message || 'Error al guardar');
        });
    });
});
</script>
@stop