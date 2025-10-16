@extends('master', [
  'menuActual' => 'buscador'])

@section('contenido')
<div class="col-md-12">
    <div class="mb-3">
        <a href="{{ url('buscador-trabajadores') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Buscador
        </a>
    </div>

    <!-- Información Principal del Trabajador -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-user-tie"></i> {{ $trabajador->nombre }}
                <span class="badge badge-light ml-2">Trabajador Veritas</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">RUT:</th>
                            <td><strong>{{ $trabajador->rut }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $trabajador->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge badge-{{ $trabajador->estado == 1 ? 'success' : 'secondary' }}">
                                    {{ $trabajador->estado == 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Total Producciones:</th>
                            <td><strong>{{ number_format($stats['total_producciones']) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Total Kilos:</th>
                            <td><strong>{{ number_format($stats['produccion_total_kilos'], 2) }} kg</strong></td>
                        </tr>
                        <tr>
                            <th>Promedio por Producción:</th>
                            <td>
                                @if($stats['total_producciones'] > 0)
                                    <strong>{{ number_format($stats['produccion_total_kilos'] / $stats['total_producciones'], 2) }} kg</strong>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Producción -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ number_format($stats['total_producciones']) }}</h2>
                    <p class="mb-0">Total Producciones</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ number_format($stats['produccion_total_kilos'], 0) }}</h2>
                    <p class="mb-0">Total Kilos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h2 class="mb-0">
                        @if($stats['total_producciones'] > 0)
                            {{ number_format($stats['produccion_total_kilos'] / $stats['total_producciones'], 1) }}
                        @else
                            0
                        @endif
                    </h2>
                    <p class="mb-0">Promedio kg/Producción</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Producciones -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Historial de Producciones (Últimas 20)
            </h5>
        </div>
        <div class="card-body">
            @if(count($producciones) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Kilos</th>
                                <th>Tipo Producción</th>
                                <th>Grupo</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($producciones as $produccion)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($produccion->fecha)) }}</td>
                                <td><strong>{{ number_format($produccion->kilos, 2) }} kg</strong></td>
                                <td>{{ $produccion->tipo_produccion_id ?? 'N/A' }}</td>
                                <td>{{ $produccion->grupo_id ?? 'N/A' }}</td>
                                <td>{{ $produccion->observaciones ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th>TOTAL (mostradas):</th>
                                <th><strong>{{ number_format(collect($producciones)->sum('kilos'), 2) }} kg</strong></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay registros de producciones para este trabajador.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Nota informativa -->
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Los datos de este trabajador provienen del sistema Veritas. 
        Para información más detallada sobre vacaciones, licencias y otros movimientos, 
        consulte el sistema correspondiente.
    </div>
</div>
@stop
