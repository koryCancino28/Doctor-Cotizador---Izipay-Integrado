<!-- Modal -->
<div class="modal fade" id="detalleFormulacionModal{{ $formulacion->id }}" tabindex="-1" role="dialog" aria-labelledby="detalleFormulacionLabel{{ $formulacion->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header" style="background-color: #fe495f; color: white;">
        <h5 class="modal-title" id="detalleFormulacionLabel{{ $formulacion->id }}">Detalle de Formulación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <div class="row">
            <!-- Información Básica Card -->
            <div class="col-md-6 mb-4">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header" style="background-color:rgb(255, 175, 184); color: rgb(169, 68, 80);">
                        <h5><i class="fas fa-info-circle"></i> Información Básica</h5>
                    </div>
                    <div class="card-body">
                        <p><strong style="color:rgb(224, 61, 80);">Item:</strong> {{ $formulacion->item }}</p>
                        <p><strong style="color:rgb(224, 61, 80);">Nombre:</strong> {{ $formulacion->name }}</p>
                        <p><strong style="color:rgb(224, 61, 80);">Doctor:</strong> {{ $formulacion->cliente->nombre }}</p>
                    </div>
                </div>
            </div>

            <!-- Precios Card -->
            <div class="col-md-6 mb-4">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header" style="background-color:rgb(255, 175, 184); color: rgb(169, 68, 80);">
                        <h5><i class="fas fa-comment-dollar"></i> Precios</h5>
                    </div>
                    <div class="card-body">
                        <p><strong style="color:rgb(224, 61, 80);">Precio Público:</strong> S/ {{ number_format($formulacion->precio_publico, 2) }}</p>
                        <p><strong style="color:rgb(224, 61, 80);">Precio Médico:</strong> S/ {{ number_format($formulacion->precio_medico, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <a href="{{ route('formulaciones.edit', $formulacion) }}" class="btn" style="background-color: #fe495f; color: white;">
            <i class="fas fa-edit"></i> Editar Formulación
        </a>
      </div>
    </div>
  </div>
</div>
