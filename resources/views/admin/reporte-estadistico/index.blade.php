@extends('adminlte::page')

@section('title', 'Reporte Estadístico')

@section('content_header')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
        <div class="d-flex align-items-center mb-1 mb-sm-0">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary mr-3">
                <i class="fas fa-home mr-1"></i>Inicio
            </a>
            <h1 class="h4 mb-0"><i class="fas fa-chart-pie mr-2"></i>Reporte Estadístico Resumen</h1>
        </div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Reporte Estadístico</li>
        </ol>
    </div>
@stop

@section('content')
    @livewire('admin.reporte-estadistico')
@stop
