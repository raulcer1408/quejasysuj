<?php

use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Administración de usuarios y asignaciones
    Route::middleware('role:superusuario,sistemas')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', fn() => view('admin.usuarios.index'))->name('usuarios.index');
        Route::get('/visitantes', fn() => view('admin.visitantes.index'))->name('visitantes.index');
        Route::get('/asignar-quejas', fn() => view('quejas.asignar'))->name('asignar.quejas');
        Route::get('/gestion-quejas', fn() => view('quejas.gestion'))->name('gestion.quejas');
        Route::get('/historico-flujo', fn() => view('quejas.historico-flujo'))->name('historico.flujo');
        Route::get('/registro-actividad', fn() => view('admin.registro-actividad.index'))->name('registro.actividad');
        Route::get('/buzon-quejas', fn() => view('admin.buzon.index'))->name('buzon.quejas');
    });

    // Enviar queja/sugerencia (solo visitante)
    Route::middleware('role:visitante,superusuario,sistemas')->group(function () {
        Route::get('/quejas/nueva', fn() => view('quejas.nueva'))->name('quejas.nueva');
    });

    // Ver solicitudes (docentes también — para ver las derivadas a ellos)
    Route::middleware('role:docente_interno,docente,visitante,superusuario,sistemas')->group(function () {
        Route::get('/quejas/mis-solicitudes', fn() => view('quejas.mis-solicitudes'))->name('quejas.mis');
    });

    // Historial propio — accesible a todos los usuarios autenticados
    Route::get('/quejas/mi-historial', fn() => view('quejas.mi-historial'))->name('quejas.historial.propio');

    // Reportes PDF
    Route::get('/quejas/{queja}/reporte-pdf', [ReporteController::class, 'solicitudPdf'])
        ->name('quejas.reporte-pdf');
    Route::get('/quejas/{queja}/reporte-final', [ReporteController::class, 'reporteFinalPdf'])
        ->name('quejas.reporte-final');

    // Revisores: Pedagoga, Miembro Comisión, Revisor Administrativo
    Route::middleware('role:pedagoga_formacion,pedagoga_capacitacion,responsable_revista,responsable_administrativo,pedagoga,miembro_comision,revisor_administrativo,usuario_eje,superusuario,sistemas')->group(function () {
        Route::get('/quejas/revision', fn() => view('quejas.revision'))->name('quejas.revision');
        Route::get('/quejas/control-flujo-revision', fn() => view('quejas.control-flujo'))->name('quejas.control.flujo.revision');
    });

    // Buzón Revista — Investigación
    Route::middleware('role:responsable_revista,miembro_comision,usuario_eje,superusuario,sistemas')->group(function () {
        Route::get('/quejas/buzon-revista', fn() => view('quejas.buzon-revista'))->name('quejas.buzon.revista');
    });

    // Coordinador
    Route::middleware('role:coordinador_academico_capacitacion,coordinador,superusuario,sistemas')->group(function () {
        Route::get('/quejas/coordinador', fn() => view('quejas.coordinador'))->name('quejas.coordinador');
        Route::get('/quejas/coordinador-historial', fn() => view('quejas.coordinador-historial'))->name('quejas.coordinador.historial');
    });

    // Jefe de Unidad
    Route::middleware('role:jefe_formacion,jefe_capacitacion,jefe_administrativo,jefe_unidad,superusuario,sistemas')->group(function () {
        Route::get('/quejas/jefe', fn() => view('quejas.jefe'))->name('quejas.jefe');
        Route::get('/quejas/control-flujo', fn() => view('quejas.control-flujo'))->name('quejas.control.flujo');
        Route::get('/quejas/control-flujo/pdf', [ReporteController::class, 'controlFlujoPdf'])->name('quejas.control.flujo.pdf');
    });

    // Reportes — accesibles para superusuario, sistemas, revisores y jefes
    $rolesReportes = implode(',', [
        'superusuario', 'sistemas',
        'pedagoga_formacion', 'pedagoga_capacitacion', 'responsable_revista', 'responsable_administrativo',
        'pedagoga', 'miembro_comision', 'revisor_administrativo',
        'jefe_formacion', 'jefe_capacitacion', 'jefe_administrativo', 'jefe_unidad',
    ]);
    Route::middleware("role:{$rolesReportes}")->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reporte-general',            fn() => view('admin.reporte-general.index'))->name('reporte.general');
        Route::get('/reporte-estadistico',        fn() => view('admin.reporte-estadistico.index'))->name('reporte.estadistico');
        Route::get('/reporte-tiempos',            fn() => view('admin.reporte-tiempos.index'))->name('reporte.tiempos');
        Route::get('/reporte-atencion-individual',fn() => view('admin.reporte-atencion-individual.index'))->name('reporte.atencion.individual');
    });
});
