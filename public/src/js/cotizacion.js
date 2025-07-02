$(document).ready(function () {
        $('#formulaciones').DataTable({
            paging: true,
            searching: false,
            ordering: false,
            info: false,
            lengthChange: false, 
            language: {
                paginate: {
                    previous: '«',
                    next:     '»'
                }
            },
            dom: 'tp' // Solo tabla + paginación
        });
    });
document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("cotizacion-form");
        const btnPayNow = document.getElementById("btnPayNow");

        const inputs = form.querySelectorAll("input, select, textarea");

        const verificarFormulario = () => {
            if (form.checkValidity()) {
            btnPayNow.disabled = false;
            } else {
            btnPayNow.disabled = true;
            }
        };

        inputs.forEach((input) => {
            input.addEventListener("input", verificarFormulario);
            input.addEventListener("change", verificarFormulario);
        });
    });

$(function() {
            const cotizador = {};  
            const $form = $('#cotizacion-form');
            

            const userId = window.currentUserId; 
            const storageKey = `cotizadorData_${userId}`;

            function getStorage() {
                return localStorage.getItem(storageKey);
            }
            
            function clearStorage() {
                localStorage.removeItem(storageKey);
            }
            
            function setStorage(data) {
                localStorage.setItem(storageKey, data);
            }
            
            // Función para actualizar el resumen de la cotización
            const actualizarResumen = () => {
                let html = '', total = 0;
                
            const observacion = $('#observacion').val().trim();
            setStorage(JSON.stringify({
                    items: cotizador, 
                    observacion: observacion,
                    timestamp: new Date().getTime(),
                    userId: userId // Añadimos el userId para verificación adicional
                }));

                $.each(cotizador, (id, item) => {
                    const subtotal = item.precio * item.cantidad;
                    total += subtotal;
                    html += `<tr>
                        <td>${item.nombre}</td>
                        <td>${item.cantidad}</td>
                        <td>S/ ${item.precio.toFixed(2)}</td>
                        <td>S/ ${subtotal.toFixed(2)}</td>
                        <td><button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${id}" style="border: none"><i class="fas fa-trash-alt"></i></button></td>
                    </tr>`;
                });
                
                $('#resumen-body').html(html);
                $('#total').text(`S/ ${total.toFixed(2)}`);
                const totalEvent = new CustomEvent('totalUpdated', {
                    detail: { amount: total.toFixed(2) } 
                });
                window.dispatchEvent(totalEvent);
            };
            function actualizarBadgeNotificacion() {
                let totalProductos = 0;
                for (const id in cotizador) {
                    totalProductos += cotizador[id].cantidad;
                }

                $('#notificacion-badge').text(totalProductos);
            }

            // Manejo del botón "+" para agregar el producto
            $(document).on('click', '.btn-agregar', function () {
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
                        precio: cantidad >= 8 ? precioMedico : precioPublico, 
                        cantidad: cantidad
                    };
                }
                
                actualizarBadgeNotificacion();
                actualizarResumen();
            });

            const savedData = getStorage();
            if (savedData) {
                try {
                    const parsedData = JSON.parse(savedData);

                    if (parsedData.items) {
                        const itemIds = Object.keys(parsedData.items);

                        $.ajax({
                            url: '/verificar-productos', // Ruta en tu backend
                            method: 'POST',
                            data: { ids: itemIds },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                const productosValidos = res.ids_validos;

                                for (const id of productosValidos) {
                                    cotizador[id] = parsedData.items[id];
                                }

                                if (parsedData.observacion) {
                                    $('#observacion').val(parsedData.observacion);
                                }

                                actualizarBadgeNotificacion();
                                actualizarResumen();
                                setStorage(JSON.stringify({
                                    items: cotizador,
                                    observacion: $('#observacion').val().trim(),
                                    timestamp: new Date().getTime(),
                                    userId: userId
                                }));
                            },
                            error: function () {
                                console.error('Error al verificar productos. Limpiando cotización.');
                                clearStorage();
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error al cargar datos guardados', e);
                    clearStorage();
                }
            }

            $('#btn-limpiar').click(function () {
            Swal.fire({
                title: '¿Limpiar cotización?',
                text: "Se eliminarán todas las formulaciones agregadas.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#fe495f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    for (const key in cotizador) {
                        delete cotizador[key];
                    }
                    clearStorage();
                    actualizarBadgeNotificacion();
                    actualizarResumen();
                    $('#codigo_transaccion').val('');
                    Swal.fire('¡Hecho!', 'La cotización ha sido limpiada.', 'success');
                }
            });
        });
            // Manejo del botón de eliminar
            $(document).on('click', '.btn-eliminar', function() {
                const idProducto = $(this).data('id');
                delete cotizador[idProducto];
                actualizarResumen();
                actualizarBadgeNotificacion();
            });

            $form.submit(function(e) {
            e.preventDefault();

            const telefono = $('#telefono').val().trim();
            const tipoDelivery = $('#tipo_delivery').val();
            const direccion = $('#direccion').val().trim();
            const observacion = $('#observacion').val().trim();
            const tipoPago = $('#tipo_pago').val().toLowerCase();
            const voucherFile = $('#voucher')[0].files[0];
              const codigoTransaccion = $('#codigo_transaccion').val().trim();

            if (!telefono || !tipoDelivery || !direccion) {
                Swal.fire({
                    title: "Error",
                    text: "Por favor complete todos los datos de envío.",
                    icon: "error",
                });
                return;
            }
            if (tipoPago === "transferencia" && !voucherFile) {
                Swal.fire({
                    title: "Advertencia",
                    text: "No se ha subido un voucher. El pago quedará como pendiente. ¿Desea continuar?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, continuar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmarAntesDeEnviar(); 
                    }
                });
            }
            // si es transferencia con voucher o contra_entrega, confirmar antes de enviar
            else if (tipoPago === "transferencia" || tipoPago === "contra_entrega") {
                confirmarAntesDeEnviar();
            }
            else {
                enviarFormulario();
            }

            function confirmarAntesDeEnviar() {
                Swal.fire({
                    title: "¿Desea confirmar el pedido?",
                    text: "Este paso guardará el pedido con los datos ingresados.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, confirmar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarFormulario();
                    }
                });
            }

            function enviarFormulario() {
                $('#staticBackdrop').modal('hide'); 
                Swal.fire({
                    title: "Procesando...",
                    text: "Guardando pedido y generando PDF...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                Object.values(cotizador).forEach((item, index) => {
                    formData.append(`items[${index}][id]`, item.id);
                    formData.append(`items[${index}][cantidad]`, item.cantidad);
                    formData.append(`items[${index}][precio]`, item.precio);
                });

                formData.append('telefono', telefono);
                formData.append('tipo_delivery', tipoDelivery);
                formData.append('direccion', direccion);
                formData.append('observacion', observacion);
                formData.append('tipo_pago', tipoPago);
                formData.append('codigo_transaccion', codigoTransaccion);

                if (voucherFile) {
                    formData.append('voucher', voucherFile);
                }

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        Swal.close();
                        clearStorage();

                        const pdfUrl = res.pdf_url;
                        const link = document.createElement('a');
                        link.href = pdfUrl;
                        link.download = 'Cotizacion Grobdi.pdf';
                        link.click();

                        Object.keys(cotizador).forEach(k => delete cotizador[k]);
                        actualizarResumen();
                        $('#notificacion-badge').text('0');
                        $('#voucher').val('');
                        $('.cantidad-input').val('1');
                        $('#codigo_transaccion').val('');
                    },
                    error: function(err) {
                        Swal.close();
                        const errorMsg = err.responseJSON?.message || err.statusText || 'Error al guardar';
                        Swal.fire({
                            title: "Error",
                            text: errorMsg,
                            icon: "error"
                        });
                    }
                });
            }
        });
    });

