<div>
    @if (auth()->user()->isVisitante())
        {{-- Bienvenida visitante --}}
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:60vh;">
            <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
                 alt="Escuela de Jueces del Estado"
                 style="max-width:130px; margin-bottom:16px;">
            <h5 class="text-center mb-1" style="color:#1a3a5c;font-weight:700;">
                Sistema de Quejas y Sugerencias
            </h5>
            <p class="text-muted text-center mb-4" style="font-size:0.88rem;">
                Escuela de Jueces del Estado de Bolivia
            </p>

            <div class="row justify-content-center w-100" style="max-width:640px;">
                <div class="col-6 col-md-4 mb-3">
                    <a href="{{ route('quejas.nueva') }}" class="eje-portal-btn">
                        <i class="fas fa-plus-circle eje-portal-icon"></i>
                        <span class="eje-portal-label">REALIZAR UNA<br>QUEJA / SUGERENCIA</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <a href="{{ route('quejas.mis') }}" class="eje-portal-btn">
                        <i class="fas fa-clipboard-list eje-portal-icon"></i>
                        <span class="eje-portal-label">VER MIS<br>SOLICITUDES</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <button type="button" class="eje-portal-btn"
                            onclick="Livewire.dispatch('openProfileModal')">
                        <i class="fas fa-user-edit eje-portal-icon"></i>
                        <span class="eje-portal-label">EDITAR<br>PERFIL</span>
                    </button>
                </div>
            </div>
        </div>

    @else
    {{-- Botones portal — sistemas / superusuario --}}
    @if (auth()->user()->isSistemas() || auth()->user()->isSuperusuario())

    {{-- Encabezado institucional --}}
    <div class="text-center mb-4 mt-2">
        <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
             alt="Escuela de Jueces del Estado"
             style="max-width:110px; margin-bottom:12px;">
        <h5 class="mb-1" style="color:#1a3a5c; font-weight:700;">
            Sistema de Quejas y Sugerencias
        </h5>
        <p class="text-muted mb-0" style="font-size:0.85rem;">
            Escuela de Jueces del Estado de Bolivia
        </p>
    </div>

    {{-- MI CUENTA --}}
    <p class="eje-section-label"><i class="fas fa-user mr-1"></i>Mi Cuenta</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <button type="button" class="eje-portal-btn eje-portal-btn--sm"
                    onclick="Livewire.dispatch('openProfileModal')">
                <i class="fas fa-user-edit eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">EDITAR<br>PERFIL</span>
            </button>
        </div>
    </div>

    {{-- ADMINISTRACIÓN --}}
    <p class="eje-section-label"><i class="fas fa-cog mr-1"></i>Administración</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.usuarios.index') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-users-cog eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">USUARIOS</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.visitantes.index') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-eye eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">VISITANTES</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.gestion.quejas') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-clipboard-list eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">GESTIÓN DE<br>SOLICITUDES</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.buzon.quejas') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-envelope eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">BUZÓN DE<br>QUEJAS DERIVADAS</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.historico.flujo') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stream eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">HISTÓRICO<br>DE FLUJO</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.historial.propio') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-history eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">HISTORIAL<br>GENERAL</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.registro.actividad') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-shield-alt eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REGISTRO DE<br>ACTIVIDAD</span>
            </a>
        </div>
    </div>

    {{-- REPORTES --}}
    <p class="eje-section-label"><i class="fas fa-chart-bar mr-1"></i>Reportes</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.general') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-bar eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REPORTE<br>GENERAL</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ESTADÍSTICO<br>GENERAL</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}?servicio=formacion" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ESTADÍSTICO<br>FORMACIÓN</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}?servicio=capacitacion" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ESTADÍSTICO<br>CAPACITACIÓN</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}?servicio=administrativo" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ESTADÍSTICO<br>ADMINISTRATIVO</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}?servicio=investigacion" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ESTADÍSTICO<br>REVISTA</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.tiempos') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stopwatch eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">TIEMPOS DE<br>ATENCIÓN</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.atencion.individual') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-user-clock eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ATENCIÓN<br>INDIVIDUAL</span>
            </a>
        </div>
    </div>

    @endif

    {{-- Botones portal — revisores (pedagoga, responsable, etc.) --}}
    @if (auth()->user()->isRevisor() && !auth()->user()->isUsuarioEje())

    {{-- Encabezado institucional --}}
    <div class="text-center mb-4 mt-2">
        <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
             alt="Escuela de Jueces del Estado"
             style="max-width:110px; margin-bottom:12px;">
        <h5 class="mb-1" style="color:#1a3a5c; font-weight:700;">
            Sistema de Quejas y Sugerencias
        </h5>
        <p class="text-muted mb-0" style="font-size:0.85rem;">
            Escuela de Jueces del Estado de Bolivia
        </p>
    </div>

    {{-- MI CUENTA --}}
    <p class="eje-section-label"><i class="fas fa-user mr-1"></i>Mi Cuenta</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <button type="button" class="eje-portal-btn eje-portal-btn--sm"
                    onclick="Livewire.dispatch('openProfileModal')">
                <i class="fas fa-user-edit eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">EDITAR<br>PERFIL</span>
            </button>
        </div>
    </div>

    {{-- SOLICITUDES --}}
    <p class="eje-section-label"><i class="fas fa-clipboard-list mr-1"></i>Solicitudes</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.revision') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-envelope eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REVISIÓN DE<br>SOLICITUDES</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.control.flujo.revision') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stream eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">CONTROL<br>DE FLUJO</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.historial.propio') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-history eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">MI<br>HISTORIAL</span>
            </a>
        </div>
        @if (auth()->user()->role === 'responsable_revista' || auth()->user()->isMiembroComision())
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.buzon.revista') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-book-open eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">BUZÓN<br>REVISTA</span>
            </a>
        </div>
        @endif
    </div>

    {{-- BUZÓN REVISTA (solo visible si tiene acceso) --}}
    @if ((auth()->user()->role === 'responsable_revista' || auth()->user()->isMiembroComision() || auth()->user()->isSistemas() || auth()->user()->isSuperusuario()) && !auth()->user()->isUsuarioEje())
    <p class="eje-section-label"><i class="fas fa-book-open mr-1"></i>Revista</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.buzon.revista') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-book-open eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">BUZÓN<br>REVISTA</span>
            </a>
        </div>
    </div>
    @endif

    {{-- REPORTES --}}
    <p class="eje-section-label"><i class="fas fa-chart-bar mr-1"></i>Reportes</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.general') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-bar eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REPORTE<br>GENERAL</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REPORTE<br>ESTADÍSTICO</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.tiempos') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stopwatch eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">TIEMPOS DE<br>ATENCIÓN</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.atencion.individual') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-user-clock eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ATENCIÓN<br>INDIVIDUAL</span>
            </a>
        </div>
    </div>

    @endif

    {{-- Botones portal — Jefe de Unidad (formación, capacitación, administrativo) --}}
    @if (auth()->user()->isJefeUnidad())

    <div class="text-center mb-4 mt-2">
        <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
             alt="Escuela de Jueces del Estado"
             style="max-width:110px; margin-bottom:12px;">
        <h5 class="mb-1" style="color:#1a3a5c; font-weight:700;">Sistema de Quejas y Sugerencias</h5>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Escuela de Jueces del Estado de Bolivia</p>
    </div>

    <p class="eje-section-label"><i class="fas fa-user mr-1"></i>Mi Cuenta</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <button type="button" class="eje-portal-btn eje-portal-btn--sm"
                    onclick="Livewire.dispatch('openProfileModal')">
                <i class="fas fa-user-edit eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">EDITAR<br>PERFIL</span>
            </button>
        </div>
    </div>

    <p class="eje-section-label"><i class="fas fa-gavel mr-1"></i>Solicitudes</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.jefe') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-envelope eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">BUZÓN DE<br>QUEJAS PENDIENTES</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.control.flujo') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stream eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">CONTROL<br>DE FLUJO</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.historial.propio') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-history eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">HISTORIAL DE<br>MI UNIDAD</span>
            </a>
        </div>
    </div>

    <p class="eje-section-label"><i class="fas fa-chart-bar mr-1"></i>Reportes</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.general') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-bar eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REPORTE<br>GENERAL</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.estadistico') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-chart-pie eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">REPORTE<br>ESTADÍSTICO</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.tiempos') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stopwatch eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">TIEMPOS DE<br>ATENCIÓN</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('admin.reporte.atencion.individual') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-user-clock eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">ATENCIÓN<br>INDIVIDUAL</span>
            </a>
        </div>
    </div>

    @endif

    {{-- Botones portal — Coordinador --}}
    @if (auth()->user()->isCoordinador())

    <div class="text-center mb-4 mt-2">
        <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
             alt="Escuela de Jueces del Estado"
             style="max-width:110px; margin-bottom:12px;">
        <h5 class="mb-1" style="color:#1a3a5c; font-weight:700;">Sistema de Quejas y Sugerencias</h5>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Escuela de Jueces del Estado de Bolivia</p>
    </div>

    <p class="eje-section-label"><i class="fas fa-user mr-1"></i>Mi Cuenta</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <button type="button" class="eje-portal-btn eje-portal-btn--sm"
                    onclick="Livewire.dispatch('openProfileModal')">
                <i class="fas fa-user-edit eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">EDITAR<br>PERFIL</span>
            </button>
        </div>
    </div>

    <p class="eje-section-label"><i class="fas fa-envelope mr-1"></i>Solicitudes</p>
    <div class="row mb-2">
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.coordinador') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-envelope eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">SOLICITUDES<br>DERIVADAS</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.coordinador.historial') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-history eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">HISTORIAL<br>DERIVADAS</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <a href="{{ route('quejas.historial.propio') }}" class="eje-portal-btn eje-portal-btn--sm">
                <i class="fas fa-stream eje-portal-icon--sm"></i>
                <span class="eje-portal-label--sm">MI HISTORIAL<br>COMPLETO</span>
            </a>
        </div>
    </div>

    @endif

    {{-- Botones portal — Docente --}}
    @if (auth()->user()->isDocente())

    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:60vh;">
        <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
             alt="Escuela de Jueces del Estado"
             style="max-width:130px; margin-bottom:16px;">
        <h5 class="text-center mb-1" style="color:#1a3a5c; font-weight:700;">
            Sistema de Quejas y Sugerencias
        </h5>
        <p class="text-muted text-center mb-4" style="font-size:0.88rem;">
            Escuela de Jueces del Estado de Bolivia
        </p>
        <div class="row justify-content-center w-100" style="max-width:640px;">

            <div class="col-6 col-md-4 mb-3">
                <a href="{{ route('quejas.mis') }}" class="eje-portal-btn">
                    <i class="fas fa-envelope eje-portal-icon"></i>
                    <span class="eje-portal-label">BUZÓN DE<br>QUEJAS PENDIENTES</span>
                </a>
            </div>
            <div class="col-6 col-md-4 mb-3">
                <a href="{{ route('quejas.historial.propio') }}" class="eje-portal-btn">
                    <i class="fas fa-history eje-portal-icon"></i>
                    <span class="eje-portal-label">MI<br>HISTORIAL</span>
                </a>
            </div>
            <div class="col-6 col-md-4 mb-3">
                <button type="button" class="eje-portal-btn"
                        onclick="Livewire.dispatch('openProfileModal')">
                    <i class="fas fa-user-edit eje-portal-icon"></i>
                    <span class="eje-portal-label">EDITAR<br>PERFIL</span>
                </button>
            </div>
        </div>
    </div>

    @endif

    {{-- Botones portal — Usuario EJE --}}
    @if (auth()->user()->isUsuarioEje())

    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:60vh;">
        <img src="{{ asset('vendor/adminlte/dist/img/logoeje.png') }}"
             alt="Escuela de Jueces del Estado"
             style="max-width:130px; margin-bottom:16px;">
        <h5 class="text-center mb-1" style="color:#1a3a5c; font-weight:700;">
            Sistema de Quejas y Sugerencias
        </h5>
        <p class="text-muted text-center mb-4" style="font-size:0.88rem;">
            Escuela de Jueces del Estado de Bolivia
        </p>
        <div class="row justify-content-center w-100" style="max-width:640px;">

            @if (auth()->user()->isMiembroComision())
            {{-- Con rol de comisión: acceso al buzón revista y revisión --}}
            <div class="col-6 col-md-4 mb-3">
                <a href="{{ route('quejas.buzon.revista') }}" class="eje-portal-btn">
                    <i class="fas fa-book-open eje-portal-icon"></i>
                    <span class="eje-portal-label">BUZÓN<br>REVISTA</span>
                </a>
            </div>
            <div class="col-6 col-md-4 mb-3">
                <a href="{{ route('quejas.revision') }}" class="eje-portal-btn">
                    <i class="fas fa-envelope eje-portal-icon"></i>
                    <span class="eje-portal-label">BUZÓN DE<br>QUEJAS PENDIENTES</span>
                </a>
            </div>
            <div class="col-6 col-md-4 mb-3">
                <a href="{{ route('quejas.historial.propio') }}" class="eje-portal-btn">
                    <i class="fas fa-history eje-portal-icon"></i>
                    <span class="eje-portal-label">MI<br>HISTORIAL</span>
                </a>
            </div>
            @endif

            <div class="col-6 col-md-4 mb-3">
                <button type="button" class="eje-portal-btn"
                        onclick="Livewire.dispatch('openProfileModal')">
                    <i class="fas fa-user-edit eje-portal-icon"></i>
                    <span class="eje-portal-label">EDITAR<br>PERFIL</span>
                </button>
            </div>
        </div>
    </div>

    @endif

    {{-- Estadísticas: solo para roles sin sección de botones propia --}}
    @if (!auth()->user()->isSistemas() && !auth()->user()->isSuperusuario()
      && !auth()->user()->isRevisor() && !auth()->user()->isJefeUnidad()
      && !auth()->user()->isCoordinador() && !auth()->user()->isDocente()
      && !auth()->user()->isUsuarioEje())
    {{-- Info boxes --}}
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1">
                    <i class="fas fa-clipboard-list"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Solicitudes</span>
                    <span class="info-box-number">{{ $total }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-spinner"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">En Proceso</span>
                    <span class="info-box-number">{{ $enProceso }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Resueltas</span>
                    <span class="info-box-number">{{ $resueltas }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-danger elevation-1">
                    <i class="fas fa-ban"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">No Procede</span>
                    <span class="info-box-number">{{ $noProcede }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos fila 1 --}}
    <div class="row">
        {{-- Tipo de solicitud --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Tipo de Solicitud</h3>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div style="position:relative;width:220px;height:220px;">
                        <canvas id="chartTipo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Por servicio --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Solicitudes por Servicio</h3>
                </div>
                <div class="card-body">
                    <canvas id="chartServicio" height="110"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos fila 2 --}}
    <div class="row">
        {{-- Tendencia mensual --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Tendencia Mensual (últimos 6 meses)</h3>
                </div>
                <div class="card-body">
                    <canvas id="chartTendencia" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- Por estado --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Por Estado</h3>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div style="position:relative;width:220px;height:220px;">
                        <canvas id="chartEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif {{-- fin estadísticas --}}
    @endif {{-- fin !isSistemas && !isSuperusuario --}}

    @if (!auth()->user()->isVisitante() && !auth()->user()->isSistemas() && !auth()->user()->isSuperusuario()
      && !auth()->user()->isRevisor() && !auth()->user()->isJefeUnidad()
      && !auth()->user()->isCoordinador() && !auth()->user()->isDocente()
      && !auth()->user()->isUsuarioEje())
    @script
    <script>
        const coloresEstado = {
            pendiente:            '#ffc107',
            en_revision:          '#17a2b8',
            derivado_coordinador: '#007bff',
            respondido:           '#6c757d',
            en_validacion:        '#6f42c1',
            resuelto:             '#28a745',
            no_procede:           '#dc3545',
        };

        const etiquetasEstado = {
            pendiente:            'Pendiente',
            en_revision:          'En Revisión',
            derivado_coordinador: 'Derivado',
            respondido:           'Respondido',
            en_validacion:        'En Validación',
            resuelto:             'Resuelto',
            no_procede:           'No Procede',
        };

        // — Tipo de solicitud (doughnut) —
        Chart.register(ChartDataLabels);
        new Chart(document.getElementById('chartTipo'), {
            type: 'doughnut',
            data: {
                labels: ['Quejas', 'Sugerencias'],
                datasets: [{
                    data: [
                        {{ $porTipo['queja'] ?? 0 }},
                        {{ $porTipo['sugerencia'] ?? 0 }},
                    ],
                    backgroundColor: ['#dc3545', '#17a2b8'],
                    borderWidth: 2,
                }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 14 },
                        formatter: (value) => value > 0 ? value : '',
                    },
                },
                cutout: '60%',
            },
        });

        // — Por servicio (bar horizontal) —
        new Chart(document.getElementById('chartServicio'), {
            type: 'bar',
            data: {
                labels: ['Formación', 'Capacitación', 'Administrativo', 'Investigación'],
                datasets: [{
                    label: 'Solicitudes',
                    data: [
                        {{ $porServicio['formacion'] ?? 0 }},
                        {{ $porServicio['capacitacion'] ?? 0 }},
                        {{ $porServicio['administrativo'] ?? 0 }},
                        {{ $porServicio['investigacion'] ?? 0 }},
                    ],
                    backgroundColor: ['#007bff','#28a745','#ffc107','#6f42c1'],
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    datalabels: { display: false },
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
            },
        });

        // — Tendencia mensual (line) —
        new Chart(document.getElementById('chartTendencia'), {
            type: 'line',
            data: {
                labels: {!! $tendencia->pluck('label')->toJson() !!},
                datasets: [{
                    label: 'Solicitudes ingresadas',
                    data: {!! $tendencia->pluck('total')->toJson() !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#007bff',
                }],
            },
            options: {
                plugins: {
                    legend: { display: false },
                    datalabels: { display: false },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
            },
        });

        // — Por estado (doughnut) —
        const estadoLabels = @json(array_keys($porEstado->toArray()));
        const estadoData   = @json(array_values($porEstado->toArray()));

        new Chart(document.getElementById('chartEstado'), {
            type: 'doughnut',
            data: {
                labels: estadoLabels.map(k => etiquetasEstado[k] ?? k),
                datasets: [{
                    data: estadoData,
                    backgroundColor: estadoLabels.map(k => coloresEstado[k] ?? '#aaa'),
                    borderWidth: 2,
                }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                    datalabels: { display: false },
                },
                cutout: '60%',
            },
        });
    </script>
    @endscript
    @endif

    <style>
        /* — Botones portal compartidos — */
        .eje-portal-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 130px;
            padding: 20px 10px;
            background: linear-gradient(145deg, #7b1a1a 0%, #c0392b 100%);
            border-radius: 12px;
            color: #fff !important;
            text-decoration: none !important;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0,0,0,.22);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .eje-portal-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(0,0,0,.28);
            color: #fff !important;
        }
        .eje-portal-icon {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        .eje-portal-label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-align: center;
            line-height: 1.4;
            text-transform: uppercase;
        }
        /* — Etiqueta de sección — */
        .eje-section-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6c757d;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 4px;
            margin-bottom: 10px;
            margin-top: 4px;
        }
        /* — Variante compacta para admin — */
        .eje-portal-btn--sm {
            width: 100%;
            min-height: 95px;
            padding: 12px 8px;
        }
        .eje-portal-icon--sm {
            font-size: 1.7rem;
            margin-bottom: 7px;
        }
        .eje-portal-label--sm {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-align: center;
            line-height: 1.35;
            text-transform: uppercase;
        }
    </style>
</div>
