@extends('adminlte::page')
@section('title', 'Mi Historial de Solicitudes')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-home mr-1"></i>Inicio
            </a>
            <h1 class="mb-0" style="font-size:1.3rem;">
                <i class="fas fa-history mr-2"></i>
                @php $authUser = Auth::user(); @endphp
                @if ($authUser->isJefeUnidad())
                    Historial de Mi Unidad
                @elseif ($authUser->isRevisor())
                    Historial de Solicitudes Revisadas
                @elseif ($authUser->isCoordinador())
                    Historial de Solicitudes Asignadas
                @elseif ($authUser->isSuperusuario() || $authUser->isSistemas())
                    Registro Histórico General
                @else
                    Mi Historial de Solicitudes
                @endif
            </h1>
        </div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Historial</li>
        </ol>
    </div>
@stop
@section('content')
    @livewire('quejas.historial-propio')
@stop
@section('css')
    <style>
        .badge-purple { background-color: #6f42c1; color: #fff; }
    </style>
@stop
