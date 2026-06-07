@extends('adminlte::page')
@section('title', 'Buzón Revista — Investigación')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary mr-3">
                <i class="fas fa-home mr-1"></i>Inicio
            </a>
            <h1><i class="fas fa-book-open mr-2"></i>Buzón Revista — Investigación</h1>
        </div>
        <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Buzón Revista</li>
        </ol>
    </div>
@stop
@section('content')
    @livewire('quejas.buzon-revista')
@stop
@section('css')
    <style>
        .badge-purple { background-color: #6f42c1; color: #fff; }
        .btn-purple   { background-color: #6f42c1; border-color: #6f42c1; color: #fff; }
        .btn-purple:hover { background-color: #5a349c; border-color: #5a349c; color: #fff; }
        .border-purple { border-color: #6f42c1 !important; }
    </style>
@stop
