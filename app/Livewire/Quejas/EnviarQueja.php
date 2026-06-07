<?php

namespace App\Livewire\Quejas;

use App\Models\AuditLog;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class EnviarQueja extends Component
{
    use WithFileUploads;

    public string $tipo_solicitud = '';
    public string $servicio = '';
    public string $nombre_actividad = '';
    public string $descripcion = '';
    public bool $aceptacion = false;
    public $respaldo = null;

    public function submit(): void
    {
        $this->validate([
            'tipo_solicitud'   => ['required', 'in:queja,sugerencia'],
            'servicio'         => ['required', 'in:formacion,capacitacion,administrativo,investigacion'],
            'nombre_actividad' => ['required', 'string', 'max:255'],
            'descripcion'      => ['required', 'string', 'min:20', 'max:1000'],
            'aceptacion'       => ['accepted'],
            'respaldo'         => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'tipo_solicitud.required'   => 'Seleccione el tipo de solicitud.',
            'servicio.required'         => 'Seleccione el servicio.',
            'nombre_actividad.required' => 'Ingrese el nombre de la actividad.',
            'descripcion.required'      => 'La descripción es obligatoria.',
            'descripcion.min'           => 'La descripción debe tener al menos 20 caracteres.',
            'descripcion.max'           => 'La descripción no puede superar los 1000 caracteres.',
            'aceptacion.accepted'       => 'Debe aceptar los términos para continuar.',
            'respaldo.mimes'            => 'El respaldo debe ser un archivo PDF.',
            'respaldo.max'              => 'El archivo no puede superar los 5MB.',
        ]);

        $rutaRespaldo = null;
        if ($this->respaldo) {
            $rutaRespaldo = $this->respaldo->store('respaldos', 'public');
        }

        // Determinar asignación según el servicio
        $esDirectoJefe = in_array($this->servicio, Queja::SERVICIOS_DIRECTOS_JEFE);
        $rolResponsable = Queja::REVISOR_POR_SERVICIO[$this->servicio];

        if ($esDirectoJefe) {
            $responsable   = User::where('role', $rolResponsable)->where('activo', true)->first();
            $estadoInicial = $responsable ? 'en_validacion' : 'pendiente';
            $revisorId     = null;
            $jefeId        = $responsable?->id;
            $accionInicial = $responsable
                ? "Solicitud asignada directamente a Jefa Administrativa: {$responsable->name}"
                : 'Solicitud enviada. Pendiente de asignación.';
        } else {
            $responsable   = User::revisoresParaServicio($rolResponsable)->first();
            $estadoInicial = $responsable ? 'en_revision' : 'pendiente';
            $revisorId     = $responsable?->id;
            $jefeId        = null;
            $accionInicial = $responsable
                ? "Solicitud enviada y asignada automáticamente a: {$responsable->name}"
                : 'Solicitud enviada. Pendiente de asignación de revisor.';
        }

        $queja = Queja::create([
            'user_id'          => Auth::id(),
            'revisor_id'       => $revisorId,
            'jefe_id'          => $jefeId,
            'tipo_solicitud'   => $this->tipo_solicitud,
            'servicio'         => $this->servicio,
            'nombre_actividad' => $this->nombre_actividad,
            'descripcion'      => $this->descripcion,
            'aceptacion'       => $this->aceptacion,
            'respaldo'         => $rutaRespaldo,
            'estado'           => $estadoInicial,
        ]);

        $queja->seguimientos()->create([
            'user_id'         => Auth::id(),
            'accion'          => $accionInicial,
            'comentario'      => null,
            'estado_anterior' => 'pendiente',
            'estado_nuevo'    => $estadoInicial,
        ]);

        AuditLog::registrar('Solicitud enviada', "Nueva solicitud #{$queja->id} enviada por: " . Auth::user()->name . " — Tipo: {$queja->tipo_solicitud}, Servicio: {$queja->servicio}.");

        $tipo = $queja->tipo_solicitud;
        $this->reset();
        $this->dispatch('quejaEnviada', tipo: $tipo);
    }

    public function render()
    {
        return view('livewire.quejas.enviar-queja', [
            'tipos'     => Queja::TIPOS,
            'servicios' => Queja::SERVICIOS,
        ]);
    }
}
