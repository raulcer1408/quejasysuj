@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-home mr-2"></i>Inicio</h1>
        <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item active">Inicio</li>
        </ol>
    </div>
@stop

@section('content')
    @livewire('dashboard')
@stop

@section('css')
    <style>
        .info-box { border-radius: 6px; }
        .badge-purple { background-color: #6f42c1; color: #fff; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
@stop
