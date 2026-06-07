<div>
    {{-- Modal de confirmación de envío --}}
    <div class="modal fade" id="modalQuejaEnviada" tabindex="-1" role="dialog" aria-hidden="true"
         data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-body text-center py-4 px-4">
                    <div style="width:70px;height:70px;border-radius:50%;background:#d4edda;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fas fa-check-circle" style="font-size:2.2rem;color:#28a745;"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2" id="modalQuejaEnviadaTitulo">Enviada correctamente</h5>
                    <p class="text-muted mb-3" style="font-size:0.9rem;">
                        Puedes hacer seguimiento del estado en <strong>Mis Solicitudes</strong>.
                    </p>
                    <div class="d-flex justify-content-center" style="gap:10px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-plus mr-1"></i>Nueva solicitud
                        </button>
                        <a href="{{ route('quejas.mis') }}" class="btn btn-success">
                            <i class="fas fa-clipboard-list mr-1"></i>Mis Solicitudes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Encabezado institucional — escritorio --}}
    <div class="card card-outline card-primary mb-0 d-none d-md-block">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0" style="font-size:0.85rem;">
                <tbody>
                    <tr>
                        <td rowspan="3" class="text-center align-middle" style="width:130px; padding:6px;">
                            <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
                                 alt="Escuela de Jueces del Estado"
                                 style="max-height:80px; max-width:110px;">
                        </td>
                        <td class="text-center font-weight-bold align-middle" style="padding:6px;">
                            ESCUELA DE JUECES DEL ESTADO
                        </td>
                        <td class="text-center align-middle" style="width:130px; padding:6px;">
                            <strong>Código</strong>
                        </td>
                        <td class="text-center align-middle" style="width:140px; padding:6px;">
                            EJE-E-REG.-30
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center font-weight-bold align-middle" style="padding:6px;">
                            FORMULARIO DE QUEJAS O SUGERENCIAS DE MEJORA
                        </td>
                        <td class="text-center align-middle" style="padding:6px;"><strong>Versión</strong></td>
                        <td class="text-center align-middle" style="padding:6px;">1</td>
                    </tr>
                    <tr>
                        <td style="padding:6px;"></td>
                        <td class="text-center align-middle" style="padding:6px;"><strong>Vigente desde</strong></td>
                        <td class="text-center align-middle" style="padding:6px;">19/01/2026</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Encabezado institucional — móvil --}}
    <div class="card card-outline card-primary mb-0 d-md-none">
        <div class="card-body py-3 px-3 text-center">
            <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
                 alt="Escuela de Jueces del Estado"
                 style="max-height:60px; max-width:80px; margin-bottom:8px;">
            <p class="font-weight-bold mb-1" style="font-size:0.82rem; line-height:1.3;">
                ESCUELA DE JUECES DEL ESTADO
            </p>
            <p class="mb-1" style="font-size:0.78rem; color:#555;">
                FORMULARIO DE QUEJAS O SUGERENCIAS DE MEJORA
            </p>
            <div class="d-flex justify-content-center" style="gap:12px; font-size:0.75rem; color:#777;">
                <span><strong>Código:</strong> EJE-E-REG.-30</span>
                <span><strong>Versión:</strong> 1</span>
                <span><strong>Desde:</strong> 19/01/2026</span>
            </div>
        </div>
    </div>

    {{-- Párrafo introductorio --}}
    <div class="card card-outline card-primary mt-0 mb-0" style="border-top:none; border-bottom:none;">
        <div class="card-body py-2 px-3">
            <p class="mb-0" style="font-size:0.83rem; font-style:italic;">
                Estimado (a) usuario, si usted no se encuentra satisfecho con nuestros servicios académicos
                o detectó una manera en la que podamos mejorar nuestros servicios, tiene la opción de
                hacernos llegar una queja u sugerencia de mejora, gracias por ayudarnos a mejorar:
            </p>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="card card-outline card-primary mt-0" style="border-top:none;">
        <div class="card-body">
            <div class="row">

                {{-- Tipo de solicitud --}}
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label style="font-size:0.88rem;">Tipo de Solicitud <span class="text-danger">*</span></label>
                        <select wire:model="tipo_solicitud"
                                class="form-control @error('tipo_solicitud') is-invalid @enderror">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($tipos as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        @error('tipo_solicitud')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Servicio --}}
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label style="font-size:0.88rem;">Servicio Académico/Administrativo <span class="text-danger">*</span></label>
                        <select wire:model="servicio"
                                class="form-control @error('servicio') is-invalid @enderror">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($servicios as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        @error('servicio')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Nombre actividad --}}
                <div class="col-12">
                    <div class="form-group">
                        <label style="font-size:0.88rem; line-height:1.3;">
                            Nombre del Curso / Acción de Capacitación / Actividad <span class="text-danger">*</span>
                        </label>
                        <input wire:model="nombre_actividad"
                               type="text"
                               class="form-control @error('nombre_actividad') is-invalid @enderror"
                               placeholder="Ingrese el nombre de la actividad">
                        @error('nombre_actividad')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="col-12">
                    <div class="form-group">
                        <label style="font-size:0.88rem;">
                            Descripción de la queja o sugerencia <span class="text-danger">*</span>
                        </label>
                        <div class="alert alert-info py-2 px-3 mb-2" style="font-size:0.8rem;">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <strong>Nota:</strong> No aplica para reclamos o impugnaciones de calificaciones.
                        </div>
                        <textarea wire:model="descripcion"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  rows="5"
                                  maxlength="1000"
                                  placeholder="Describa detalladamente su queja o sugerencia..."></textarea>
                        <small class="text-muted d-block text-right mt-1">
                            {{ strlen($descripcion) }} / 1000 caracteres
                        </small>
                        @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Respaldo PDF --}}
                <div class="col-12">
                    <div class="form-group">
                        <label style="font-size:0.88rem;">
                            Evidencias
                            <span class="text-muted">(Opcional — solo PDF, máx. 5MB)</span>
                        </label>
                        <div class="custom-file">
                            <input wire:model="respaldo"
                                   type="file"
                                   class="custom-file-input @error('respaldo') is-invalid @enderror"
                                   accept=".pdf"
                                   id="respaldo">
                            <label class="custom-file-label" for="respaldo"
                                   style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $respaldo ? $respaldo->getClientOriginalName() : 'Seleccionar archivo PDF...' }}
                            </label>
                        </div>
                        @error('respaldo')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        <div wire:loading wire:target="respaldo" class="text-info small mt-1">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Subiendo archivo...
                        </div>
                    </div>
                </div>

                {{-- Aceptación --}}
                <div class="col-12">
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input wire:model="aceptacion"
                                   type="checkbox"
                                   class="custom-control-input @error('aceptacion') is-invalid @enderror"
                                   id="aceptacion">
                            <label class="custom-control-label" for="aceptacion" style="font-size:0.88rem; line-height:1.4;">
                                Acepto que la información proporcionada es verídica y autorizo su procesamiento.
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:0.78rem;">
                            Los datos aquí consignados serán tratados únicamente para fines de mejora del servicio
                            académico y bajo las normativas de protección de datos personales.
                        </small>
                        @error('aceptacion')
                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center" style="gap:10px;">
                <small class="text-muted"><span class="text-danger">*</span> Campos obligatorios</small>
                <button wire:click="submit"
                        wire:loading.attr="disabled"
                        class="btn btn-primary btn-block d-sm-inline-block"
                        style="max-width:220px;">
                    <span wire:loading.remove wire:target="submit">
                        <i class="fas fa-paper-plane mr-1"></i>Enviar Solicitud
                    </span>
                    <span wire:loading wire:target="submit">
                        <i class="fas fa-spinner fa-spin mr-1"></i>Enviando...
                    </span>
                </button>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('quejaEnviada', ({ tipo }) => {
            const titulo = tipo === 'queja'
                ? '¡Queja enviada correctamente!'
                : '¡Sugerencia enviada correctamente!';
            document.getElementById('modalQuejaEnviadaTitulo').textContent = titulo;
            $('#modalQuejaEnviada').modal('show');
        });
    </script>
    @endscript
</div>
