@extends('master', [
  'menuActual' => 'buscador'])

@section('contenido')
<div class="col-md-12">
    <div class="mb-3">
        <a href="{{ url('buscador-trabajadores') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Buscador
        </a>
    </div>

    <!-- Información Principal del Empleado -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-user"></i> {{ $trabajador->nombre }}
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
                            <th>Área:</th>
                            <td>{{ $trabajador->area ? $trabajador->area->nombre : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Comuna:</th>
                            <td>{{ $trabajador->comuna ? $trabajador->comuna->nombre : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Dirección:</th>
                            <td>{{ $trabajador->direccion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Inicio Contrato:</th>
                            <td>{{ $trabajador->fecha_inicio_contrato ? $trabajador->fecha_inicio_contrato->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Término Contrato:</th>
                            <td>{{ $trabajador->fecha_termino_contrato ? $trabajador->fecha_termino_contrato->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Cargo Actual:</th>
                            <td>{{ $stats['cargo_actual'] && $stats['cargo_actual']->cargo ? $stats['cargo_actual']->cargo->nombre : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $stats['total_licencias'] }}</h2>
                    <p class="mb-0">Licencias Médicas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $stats['total_vacaciones'] }}</h2>
                    <p class="mb-0">Vacaciones Tomadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $stats['dias_vacaciones_pendientes'] }}</h2>
                    <p class="mb-0">Días Vac. Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h2 class="mb-0">{{ $stats['total_movimientos'] }}</h2>
                    <p class="mb-0">Movimientos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de Información -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#vacaciones" role="tab">
                        <i class="fas fa-umbrella-beach"></i> Vacaciones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#licencias" role="tab">
                        <i class="fas fa-file-medical"></i> Licencias
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#movimientos" role="tab">
                        <i class="fas fa-exchange-alt"></i> Movimientos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#sueldos" role="tab">
                        <i class="fas fa-dollar-sign"></i> Sueldos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#bonos" role="tab">
                        <i class="fas fa-gift"></i> Bonos
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Tab Vacaciones -->
                <div class="tab-pane fade show active" id="vacaciones" role="tabpanel">
                    <h5>Historial de Vacaciones</h5>
                    @if($vacaciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Término</th>
                                        <th>Días</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vacaciones as $vacacion)
                                    <tr>
                                        <td>{{ $vacacion->fecha_inicio ? $vacacion->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $vacacion->fecha_termino ? $vacacion->fecha_termino->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            @if($vacacion->fecha_inicio && $vacacion->fecha_termino)
                                                {{ $vacacion->fecha_inicio->diffInDays($vacacion->fecha_termino) + 1 }} días
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $vacacion->observaciones ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay registros de vacaciones.</p>
                    @endif
                </div>

                <!-- Tab Licencias -->
                <div class="tab-pane fade" id="licencias" role="tabpanel">
                    <h5>Historial de Licencias Médicas</h5>
                    @if($trabajador->licencias->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Término</th>
                                        <th>Días</th>
                                        <th>Causa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trabajador->licencias as $licencia)
                                    <tr>
                                        <td>{{ $licencia->fecha_inicio ? $licencia->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $licencia->fecha_termino ? $licencia->fecha_termino->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $licencia->dias_corridos ?? 'N/A' }}</td>
                                        <td>{{ $licencia->causa ? $licencia->causa->nombre : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay registros de licencias médicas.</p>
                    @endif
                </div>

                <!-- Tab Movimientos -->
                <div class="tab-pane fade" id="movimientos" role="tabpanel">
                    <h5>Movimientos de Asistencia (Últimos 20)</h5>
                    @if($trabajador->movimientosAsistencias->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trabajador->movimientosAsistencias as $movimiento)
                                    <tr>
                                        <td>{{ $movimiento->fecha ? $movimiento->fecha->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $movimiento->tipo ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $movimiento->observaciones ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay registros de movimientos.</p>
                    @endif
                </div>

                <!-- Tab Sueldos -->
                <div class="tab-pane fade" id="sueldos" role="tabpanel">
                    <h5>Historial de Sueldos Base</h5>
                    @if($trabajador->sueldosBase->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trabajador->sueldosBase as $sueldo)
                                    <tr>
                                        <td>{{ $sueldo->created_at ? $sueldo->created_at->format('d/m/Y') : 'N/A' }}</td>
                                        <td><strong>${{ number_format($sueldo->monto ?? 0, 0, ',', '.') }}</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $sueldo->estado == 1 ? 'success' : 'secondary' }}">
                                                {{ $sueldo->estado == 1 ? 'Vigente' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay registros de sueldos.</p>
                    @endif
                </div>

                <!-- Tab Bonos -->
                <div class="tab-pane fade" id="bonos" role="tabpanel">
                    <h5>Bonos Asignados</h5>
                    @if($trabajador->bonos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trabajador->bonos as $bono)
                                    <tr>
                                        <td>{{ $bono->created_at ? $bono->created_at->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $bono->tipoBono ? $bono->tipoBono->nombre : 'N/A' }}</td>
                                        <td><strong>${{ number_format($bono->monto ?? 0, 0, ',', '.') }}</strong></td>
                                        <td>{{ $bono->descripcion ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay bonos asignados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
