@extends('adminlte::page')
@section('title', 'Histórico de Flujo')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary mr-3">
                <i class="fas fa-home mr-1"></i>Inicio
            </a>
        <h1><i class="fas fa-stream mr-2"></i>Histórico de Flujo de Solicitudes</h1>
        </div>
        <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Histórico de Flujo</li>
        </ol>
    </div>
@stop
@section('content')
    @livewire('quejas.historico-flujo')
@stop
@section('css')
    <style>
        .badge-purple { background-color: #6f42c1; color: #fff; }
    </style>
@stop
