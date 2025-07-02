<!--MODIFICACION CON TODO Y EL MODAL-->
@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
@stop

@section('content')
    <div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <h1 style="color: #fe495f;text-align: center;"><strong><i class="fas fa-flask-vial"></i>
        Mis Formulaciones</strong></h1>
        @unless(auth()->user()->cliente)
            <p class="text-danger"><i class="fas fa-flask"></i>
            No tienes formulaciones registradas o no estás logueado como Doctor.</p>
        @else
        <form id="cotizacion-form" action="{{ route('cotizacion.store') }}" method="POST">
            @csrf
                
                <!-- Sección de Datos de Envío -->
                <div class="card mb-4" style="border-color: #fe495f;">
                    <div class="card-header text-white" style="background-color: #fe495f;">
                        <h3 class="card-title"><b><i class="fa-solid fa-circle-info" style="margin-right: 3px;"></i>
                        Datos de Envío</b></h3>
                    </div>
                    <div class="card-body" style="background-color: rgb(255, 255, 255);">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono" style="color: #fe495f;"><i class="fas fa-phone-volume"></i>
                                    Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono"
                                        value="{{ old('telefono', auth()->user()->cliente->telefono) }}"
                                        pattern="[9][0-9]{8}" maxlength="9" placeholder="Ejemplo: 987654321 (9 dígitos)" required 
                                        style="border-color:  #b4b4b4;"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_delivery" style="color: #fe495f;"><i class="fas fa-motorcycle"></i>
                                    Tipo de Delivery</label>
                                    <select class="form-control" id="tipo_delivery" name="tipo_delivery" required
                                            style="border-color:  #b4b4b4">
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
                            <label for="direccion" style="color: #fe495f;"><i class="fas fa-house"></i>
                            Dirección (para envío a domicilio)</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" required
                                    style="border-color:  #b4b4b4">{{ old('direccion', auth()->user()->cliente->direccion) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="observacion" style="color: #fe495f;"> <i class="fas fa-clipboard me-1"></i>
                            Observación</label>
                            <textarea class="form-control" id="observacion" name="observacion" rows="3" 
                                    style="border-color:  #b4b4b4;">{{ old('observacion') }}</textarea>
                        </div>
                    </div>
                </div>

                <div style="overflow-x: auto; width: 100%; border-radius: 25px;">
                    <!-- Tabla de Formulaciones -->
                    <table class="table table-bordered table-centered" id="formulaciones" style="background-color: rgb(255, 255, 255); border-color: #fe495f;">
                        <thead>
                            <tr style="background-color: #fe495f; color: white;">
                                <th>Item</th>
                                <th>Formulación</th>
                                <th>Precio Unidad <br><small>(De 7 a menos)</small></th>
                                <th>Precio Unidad <br><small>(De 8 a más)</small></th>
                                <th>Cantidad</th>
                                <th>Agregar</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($formulaciones as $f)

                        <tr data-id="{{ $f->id }}" data-nombre="{{ $f->name }}" data-precio="{{ $f->precio_publico }}"
                            data-precio-medico="{{ $f->precio_medico }}" style="background-color: rgb(255, 255, 255);">
                            <td>{{ $f->item }}</td>
                            <td>{{ $f->name }}</td>
                            <td>S/ {{ number_format($f->precio_publico, 2) }}</td>
                            <td>S/ {{ number_format($f->precio_medico, 2) }}</td>
                            <td><input type="number" class="form-control cantidad-input" value="1" min="1" style="width:80px; border-color: #fe495f; text-align: center; display: block; margin: 0 auto;"></td>
                            <td><button type="button" class="btn btn-primary btn-sm btn-agregar" style="background-color: #767c94; border: none;"><i class="fa-solid fa-circle-plus" style="margin-right: 3px;"></i></button></td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
                
              <!-- Primera fila: Select de pago + Botón de resumen + botones dinámicos -->
            <div class="row align-items-center mb-3">
                <div class="col-md-4 mb-3">
                    <button type="button" class="btn btn_crear btn-lg position-relative w-50" data-toggle="modal" data-target="#staticBackdrop">
                        <i class="fas fa-notes-medical"></i>
                        Ver resumen
                        <span id="notificacion-badge" class="badge position-absolute top-0 start-100 translate-middle rounded-circle badge-light" style="color: #fe495f; font-size: 0.90rem; min-width: 30px; height: 25px; line-height: 20px; text-align: center;">
                            0
                        </span>
                    </button>
                </div>
                <div class="col-md-4 mb-3">
                    <select id="tipo_pago" name="tipo_pago" required class="form-select" style="cursor: pointer;">
                        <option value="" disabled selected>Seleccione un método de pago</option>
                        <option value="contra_entrega">Pago contra entrega</option>
                        <option value="pasarela_izipay">Tarjeta, Yape, Plin</option>
                        <option value="transferencia">Depósito o Transferencia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <!-- Botón para contra entrega -->
                    <div id="boton_guardar_pdf" style="display: none;">
                        <button type="submit" class="btn btn_crear btn-lg w-100">
                            <i class="fas fa-save me-1" style="margin-right: 8px;"></i> Guardar Pedido
                        </button>
                    </div>
                    <!-- Botones para pasarela -->
                    <div id="pasarela_pagos" style="display: none;">
                        <button id="btnPayNow" class="btn btn_crear btn-lg w-100 mb-2" type="button" disabled title="Recuerda llenar los datos de envío">
                            esperando pago...
                        </button>
                        <button type="submit" id="mi-boton" style="display: none;" disabled class="btn btn_crear btn-lg w-100">
                            <i class="fas fa-save me-1"></i> Guardar Pedido
                        </button>
                    </div>
                </div>
            </div>
            <div class="row" id="transferencia_info" style="display: none;">
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <p><em style="color: #fe495f;"><strong><i class="fa-solid fa-building-columns"></i>
                                Banco Continental - Grobdi SAC</strong></em><br>
                                <strong>
                                    <em>Cuenta:</em> 001101640100062987<br>
                                    <em>CCI:</em> 01116400010006298711
                                </strong>
                            </p>
                            <p><em style="color: #fe495f;"><strong><i class="fa-solid fa-building-columns"></i>
                                BCP - Grobdi SAC</strong></em><br>
                                <strong>
                                    <em>Cuenta:</em> 1938091395060<br>
                                    <em>CCI:</em> 00219300809139506018
                                </strong>
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <label for="voucher" class="form-label fw-bold" style="color: #fe495f; font-style: italic;">
                                Subir comprobante
                            </label>
                            <div class="input-group">
                                <label class="input-group-text bg-white border border-1 mx-auto" for="voucher" style="cursor: pointer;">
                                    <i class="fas fa-file-upload me-2 text-secondary" style="margin-right: 5px;"></i> 
                                    Elegir archivo
                                </label>
                                <input type="file" class="d-none" id="voucher" name="voucher" accept="image/*,application/pdf">
                            </div>
                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG (máx. 2MB)</small><br>
                            <div class="form-group mt-2">
                                <label for="codigo_transaccion" style="color: #fe495f; font-style: italic;">Código de transacción (opcional)</label>
                                <input type="text" name="codigo_transaccion" id="codigo_transaccion" class="form-control mx-auto" style="width: 70%;" placeholder="Ejemplo: 1234567890ABCD" maxlength="12">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <button type="submit" class="btn btn_crear btn-lg w-100">
                                <i class="fas fa-save me-1" style="margin-right: 5px;"></i> Guardar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="staticBackdropLabel"style="color: #fe495f;"><b><i class="fa-solid fa-basket-shopping"></i>
                                        Resumen</b></h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <div style="position: relative; padding-top: 3rem;">
                                <button id="btn-limpiar" type="button"
                                class="btn btn-light"
                                style="position: absolute; top: 0; right: 0; border: 1px solid #fe495f; color: rgb(240, 99, 115);">
                                <i class="fas fa-broom"></i> Limpiar
                                </button>
                                    
                                <div style="overflow-x: auto; width: 100%; border-radius: 20px;">
                                    <table class="table table-centered mb-3" style="background-color: rgb(255, 244, 244); border-color: #fe495f;border: 1px solid #fe495f; ">
                                        <thead>
                                            <tr style="background-color:rgb(255, 122, 122); color: white;">
                                                <th>Formulación</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                                <th style="width: 100px; text-align: center;">Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resumen-body"></tbody>
                                        <tfoot>
                                            <tr style="background-color: #fe495f; color: white;">
                                                <th colspan="3">Total</th>
                                                <th id="total">S/ 0.00</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @endunless
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         
        </form> 
    <!-- Modal de Facturación - Versión mejorada Bootstrap 4 -->
    <div class="modal fade" id="billingModal" tabindex="-1" role="dialog" aria-labelledby="billingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm"> <!-- Aumenté el tamaño a lg -->
            <form id="billingForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header text-white"  style="background-color:rgb(251, 81, 101); "> <!-- Color mejorado -->
                <h5 class="modal-title" id="billingModalLabel">
                <i class="fas fa-file-invoice mr-2"></i>Datos de Facturación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row"> <!-- Organización en columnas -->
                    <div class="col-md-12">
                        <div class="form-group">
                        <label for="firstName"><i class="fas fa-user mr-1"></i> Nombre</label>
                        <input type="text" class="form-control shadow-sm" name="firstName" id="firstName" value="{{ old('firstName', auth()->user()->name) }}" required>
                        </div>
                        <div class="form-group">
                        <label for="lastName"><i class="fas fa-user mr-1"></i> Apellido</label>
                        <input type="text" class="form-control shadow-sm" name="lastName" id="lastName" value="{{ old('lastName', auth()->user()->last_name) }}" required>
                        </div>
                        <div class="form-group">
                        <label for="email"><i class="fas fa-envelope mr-1"></i> Email</label>                                      
                        <input type="email" class="form-control shadow-sm" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" placeholder="Ejemplo: usuario@dominio.com" required>
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber"><i class="fas fa-phone mr-1"></i> Teléfono</label>                                       
                            <input type="tel" class="form-control shadow-sm" name="phoneNumber" value="{{ old('telefono', auth()->user()->cliente->telefono) }}" id="phoneNumber" pattern="[9][0-9]{8}" maxlength="9" placeholder="Ejemplo: 987654321 (9 dígitos)" required>
                        </div>
                        <div class="form-group">
                            <label for="document"><i class="fas fa-id-card mr-1"></i> DNI</label>
                            <input type="text" class="form-control shadow-sm" name="document" id="document" pattern="[0-9]{8}" maxlength="8" placeholder="Ejemplo: 87654321 (8 dígitos)" required>
                        </div>
                    </div>
                        <input type="hidden" class="form-control shadow-sm" name="street" id="street" value="Av Brasil 1241">
                        <input type="hidden" class="form-control shadow-sm" name="city" id="city" value="Lima">
                        <input type="hidden" class="form-control shadow-sm" name="state" id="state" value="Lima">
                        <input type="hidden" class="form-control shadow-sm" name="country" id="country" value="PE">
                        <input type="hidden" class="form-control shadow-sm" name="postalCode" id="postalCode" value="00001">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn_crear btn-block py-2">
                <i class="fas fa-check-circle mr-2"></i>Continuar al Pago
                </button>
            </div>
            </form>
        </div>
    </div>
<script>
    window.currentUserId = {{ Auth::id() }}; 
    //para que sea aceptado por el script de izipay
     @if (Auth::check() && Auth::user()->cliente)
        window.currentClienteId = "{{ Auth::user()->cliente->id }}";
    @else
        window.currentClienteId = null;
    @endif
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="module" src="/src/js/pay.js"></script>
                    
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    em, label {
        color:rgb(238, 134, 146);
    }
    #tipo_pago {
       color: rgb(245, 114, 129);
        border: 2px solid rgb(245, 114, 129);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 1rem;
        background-color: #fff;
        box-shadow: 0 0 8px rgba(245, 114, 129, 0.3);
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg fill=\'%23f57281\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/></svg>');
        padding-right: 40px;
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 25px 25px;
    }
    .btn-agregar:hover {
        background-color: #5d5f7d !important;
    }
    .btn_crear:hover {
        background-color:rgb(255, 121, 121) !important;
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
    .btn_crear {
        border: 1px solid#fe495f !important;
        background-color:rgb(251, 81, 101); 
        color: white;
        margin: 0 auto;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
     document.getElementById('tipo_pago').addEventListener('change', function () {
        let tipo = this.value;

        document.getElementById('boton_guardar_pdf').style.display = (tipo === 'contra_entrega') ? 'block' : 'none';
        document.getElementById('pasarela_pagos').style.display = (tipo === 'pasarela_izipay') ? 'block' : 'none';
        document.getElementById('transferencia_info').style.display = (tipo === 'transferencia') ? 'block' : 'none';
    });
    window.addEventListener("message", function(event) {
        if (event.data.action === "habilitar-boton") {
            const boton = document.querySelector("#mi-boton");
            if (boton) {
                boton.disabled = false;
                boton.style.display = 'inline-block'; 
                boton.click();
                boton.style.display = 'none';
            }
        }
    });
    document.getElementById('tipo_pago').dispatchEvent(new Event('change')); 
</script>
<!-- script para el dom completo-->
<script type="module" src="/src/js/cotizacion.js"></script>

<script src="https://sandbox-checkout.izipay.pe/payments/v1/js/index.js"></script>
<!--ENDPOINT PARA PRODUCCION 
                    <script src="https://checkout.izipay.pe/payments/v1/js/index.js" defer></script>-->
@stop