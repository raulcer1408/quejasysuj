@extends('adminlte::page')

@section('title', 'Administración de Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary mr-3">
                <i class="fas fa-home mr-1"></i>Inicio
            </a>
        <h1><i class="fas fa-users-cog mr-2"></i>Administración de Usuarios</h1>
        </div>
        <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>
    </div>
@stop

@section('content')
    @livewire('admin.user-manager')
@stop

@section('css')
    <style>
        .badge-purple { background-color: #6f42c1; color: #fff; }
    </style>
@stop
